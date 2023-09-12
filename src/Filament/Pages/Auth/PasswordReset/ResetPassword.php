<?php

namespace ArtMin96\FilamentJet\Filament\Pages\Auth\PasswordReset;

use ArtMin96\FilamentJet\Contracts\ResetsUserPasswords;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Datas\FilamentJetData;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\CardPage;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\PasswordResetResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;
use Phpsa\FilamentPasswordReveal\Password as PasswordInput;

/**
 * Undocumented class
 *
 * @property UserContract $user
 * @property ComponentContainer $form
 */
final class ResetPassword extends CardPage
{
    use WithRateLimiting;

    protected static string $view = 'filament-jet::filament.pages.auth.password-reset.reset-password';

    public ?string $email = null;

    public ?string $password = '';

    public ?string $passwordConfirmation = '';

    public ?string $token = null;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        if (is_array(request()->query('token'))) {
            throw new Exception('['.__LINE__.']['.class_basename(self::class).']');
        }

        $this->token = request()->query('token');

        $this->form->fill([
            'email' => request()->query('email'),
        ]);
    }

    public function resetPassword(): ?PasswordResetResponse
    {
        $rateLimitingOptionEnabled = Features::getOption(Features::resetPasswords(), 'reset.rate_limiting.enabled');

        if ($rateLimitingOptionEnabled) {
            try {
                $this->rateLimit(Features::getOption(Features::resetPasswords(), 'reset.rate_limiting.limit'));
            } catch (TooManyRequestsException $exception) {
                Notification::make()
                    ->title(__('filament-jet::auth/password-reset/reset-password.messages.throttled', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]))
                    ->danger()
                    ->send();

                return null;
            }
        }

        $data = $this->form->getState();

        $data['email'] = $this->email;
        $data['token'] = $this->token;

        $status = $this->broker()->reset(
            $data,
<<<<<<< HEAD
            static function (UserContract $userContract) use ($data) : void {
=======
            function (UserContract $userContract) use ($data): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
                app(ResetsUserPasswords::class)->reset($userContract, $data);
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->title(__($status))
                ->success()
                ->send();

            return app(PasswordResetResponse::class);
        }
        
        if (! is_string($status)) {
            throw new Exception('wip');
        }

        $title = __($status);
        if (! is_string($title)) {
            throw new Exception('wip');
        }

        Notification::make()
            ->title($title)
            ->danger()
            ->send();

        return null;
    }

    /**
     * @param  string  $propertyName
     */
    public function propertyIsPublicAndNotDefinedOnBaseClass($propertyName): bool
    {
        if (app()->runningUnitTests()) {
            return parent::propertyIsPublicAndNotDefinedOnBaseClass($propertyName);
        }
        if (!in_array($propertyName, [
            'email',
            'token',
        ])) {
            return parent::propertyIsPublicAndNotDefinedOnBaseClass($propertyName);
        }
        return false;
    }

    protected function getTitle(): string
    {
        return __('filament-jet::auth/password-reset/reset-password.title');
    }

    protected function getHeading(): string
    {
        return __('filament-jet::auth/password-reset/reset-password.heading');
    }

    protected function getCardWidth(): string
    {
        $res = Features::getOption(Features::resetPasswords(), 'reset.card_width');
        if (! is_string($res)) {
            throw new Exception('wip');
        }

        return $res;
    }

    protected function hasBrand(): bool
    {
        $res = Features::optionEnabled(Features::resetPasswords(), 'reset.has_brand');
        if (! is_bool($res)) {
            throw new Exception('wip');
        }

        return $res;
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label(__('filament-jet::auth/password-reset/reset-password.fields.email.label'))
                ->disabled(),
            PasswordInput::make('password')
                ->label(__('filament-jet::auth/password-reset/reset-password.fields.password.label'))
                ->required()
                ->same('passwordConfirmation')
                ->revealable()
                ->copyable()
                ->generatable()
                ->rules(FilamentJet::getPasswordRules()),
            PasswordInput::make('passwordConfirmation')
                ->label(__('filament-jet::auth/password-reset/reset-password.fields.passwordConfirmation.label'))
                ->required()
                ->revealable(),
        ];
    }

    /**
     * Get the broker to be used during password reset.
     */
    private function broker(): PasswordBroker
    {
        $filamentJetData = FilamentJetData::make();

        return Password::broker($filamentJetData->passwords);
    }

    /**
     * @return array<string, string>
     */
    protected function getMessages(): array
    {
        return [
            'password.same' => __('validation.confirmed', ['attribute' => __('filament-jet::auth/password-reset/reset-password.fields.password.validation_attribute')]),
        ];
    }
}
