<?php

namespace ArtMin96\FilamentJet\Filament\Pages\Auth;

use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\RecoveryCodeReplaced;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\CardPage;
use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\TwoFactorLoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Redirector;

/**
 * Undocumented class
 *
 * @property UserContract $user
 * @property ComponentContainer $form
 * @property string $sessionPrefix
 */
class TwoFactorLogin extends CardPage
{
    use WithRateLimiting;

    public ?string $code = '';

    public ?string $recoveryCode = '';

    public bool $usingRecoveryCode = false;

    public ?UserContract $challengedUser = null;

    protected static string $view = 'filament-jet::filament.pages.auth.two-factor-login';

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function mount()
    {
        if (! $this->hasChallengedUser()) {
            return redirect()->to(jetRouteActions()->loginRoute());
        }
    }

    public function getSessionPrefixProperty(): string
    {
        return jet()->getTwoFactorLoginSessionPrefix();
    }

    /**
     * Determine if the request has a valid two factor code.
     */
    public function hasValidCode(?string $code): bool
    {
        return $code && tap(app(TwoFactorAuthenticationProvider::class)->verify(
            decrypt($this->challengedUser()->two_factor_secret),
            $code
        ), function ($result) {
            if ($result) {
                session()->forget("{$this->sessionPrefix}login.id");
            }
        });
    }

    /**
     * Get the valid recovery code if one exists on the request.
     */
    public function validRecoveryCode(?string $recoveryCode): ?string
    {
        if (! $recoveryCode) {
            return null;
        }

        return tap(collect($this->challengedUser()->recoveryCodes())->first(function ($code) use ($recoveryCode) {
            return hash_equals($code, $recoveryCode) ? $code : null;
        }), function ($code) {
            if ($code) {
                session()->forget("{$this->sessionPrefix}login.id");
            }
        });
    }

    /**
     * Determine if there is a challenged user in the current session.
     */
    public function hasChallengedUser(): bool
    {
        if ($this->challengedUser) {
            return true;
        }
        $userProvider = Filament::auth()->getProvider();
        if (! method_exists($userProvider, 'getModel')) {
            throw new \Exception('getModel not exists in userProvider');
        }
        $userModel = $userProvider->getModel();

        return session()->has("{$this->sessionPrefix}login.id") &&
            $userModel::find(session()->get("{$this->sessionPrefix}login.id"));
    }

    /**
     * Get the user that is attempting the two factor challenge.
     *
     * @return UserContract|Redirector|\Illuminate\Http\RedirectResponse
     */
    public function challengedUser()
    {
        if ($this->challengedUser) {
            return $this->challengedUser;
        }

        $userProvider = Filament::auth()->getProvider();
        if (! method_exists($userProvider, 'getModel')) {
            throw new \Exception('getModel not exists in userProvider');
        }
        $userModel = $userProvider->getModel();

        if (! session()->has("{$this->sessionPrefix}login.id") ||
            ! $user = $userModel::find(session()->get("{$this->sessionPrefix}login.id"))) {
            return redirect()->to(jetRouteActions()->loginRoute());
        }

        return $this->challengedUser = $user;
    }

    /**
     * Determine if the user wanted to be remembered after login.
     */
    public function remember(): bool
    {
        return session()->pull("{$this->sessionPrefix}login.remember", false);
    }

    protected function getCardWidth(): string
    {
        return Features::getOption(Features::twoFactorAuthentication(), 'authentication.card_width');
    }

    protected function hasBrand(): bool
    {
        return Features::optionEnabled(Features::twoFactorAuthentication(), 'authentication.has_brand');
    }

    public function authenticate(): ?TwoFactorLoginResponse
    {
        $rateLimitingOptionEnabled = Features::getOption(Features::twoFactorAuthentication(), 'authentication.rate_limiting.enabled');

        if ($rateLimitingOptionEnabled) {
            try {
                $this->rateLimit(Features::getOption(Features::login(), 'authentication.rate_limiting.limit'));
            } catch (TooManyRequestsException $exception) {
                Notification::make()
                    ->title(__('filament-jet::auth/two-factor-login.messages.throttled', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]))
                    ->danger()
                    ->send();

                return null;
            }
        }

        $data = $this->form->getState();

        $user = $this->challengedUser();

        if ($code = $this->validRecoveryCode($data['recoveryCode'] ?? '')) {
            $user->replaceRecoveryCode($code);

            event(new RecoveryCodeReplaced($user, $code));
        } elseif (! $this->hasValidCode($data['code'] ?? '')) {
            [$key, $message] = isset($data['recoveryCode'])
                ? ['recoveryCode', __('filament-jet::auth/two-factor-login.messages.failed.recoveryCode')]
                : ['code', __('filament-jet::auth/two-factor-login.messages.failed.code')];

            $this->addError($key, $message);

            return null;
        }

        if (! $user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
            throw new \Exception('strange things');
        }

        Filament::auth()->login($user, $this->remember());

        session()->regenerate();

        return app(TwoFactorLoginResponse::class);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('code')
                ->label(__('filament-jet::auth/two-factor-login.fields.code.label'))
                ->placeholder(__('filament-jet::auth/two-factor-login.fields.code.placeholder'))
                ->required()
                ->hint(
                    new HtmlString(
                        Blade::render(
                            '<x-filament::link :class="\'cursor-pointer\'"
                                x-on:click="usingRecoveryCode = true">
                                {{ __(\'filament-jet::auth/two-factor-login.buttons.recovery_code.label\') }}
                            </x-filament::link>'
                        )
                    )
                )
                ->visible(! $this->usingRecoveryCode),

            TextInput::make('recoveryCode')
                ->label(__('filament-jet::auth/two-factor-login.fields.recoveryCode.label'))
                ->placeholder(__('filament-jet::auth/two-factor-login.fields.recoveryCode.placeholder'))
                ->required()
                ->hint(
                    new HtmlString(
                        Blade::render(
                            '<x-filament::link :class="\'cursor-pointer\'"
                                x-on:click="usingRecoveryCode = false">
                                {{ __(\'filament-jet::auth/two-factor-login.buttons.authentication_code.label\') }}
                            </x-filament::link>'
                        )
                    )
                )
                ->visible($this->usingRecoveryCode),
        ];
    }

    public function getTitle(): string
    {
        return __('filament-jet::auth/two-factor-login.title');
    }

    public function getHeading(): string
    {
        return __('filament-jet::auth/two-factor-login.heading');
    }
}
