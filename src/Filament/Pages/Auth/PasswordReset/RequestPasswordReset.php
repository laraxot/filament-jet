<?php

namespace ArtMin96\FilamentJet\Filament\Pages\Auth\PasswordReset;

use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\CardPage;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;

/**
 * Undocumented class
 *
 * @property UserContract $user
 * @property ComponentContainer $form
 */
class RequestPasswordReset extends CardPage
{
    use WithRateLimiting;

    protected static string $view = 'filament-jet::filament.pages.auth.password-reset.request-password-reset';

    public ?string $email = null;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function request(): void
    {
        $rateLimitingOptionEnabled = Features::getOption(Features::resetPasswords(), 'request.rate_limiting.enabled');

        if ($rateLimitingOptionEnabled) {
            try {
                $this->rateLimit(Features::getOption(Features::resetPasswords(), 'request.rate_limiting.limit'));
            } catch (TooManyRequestsException $exception) {
                Notification::make()
                    ->title(__('filament-jet::auth/password-reset/request-password-reset.messages.throttled', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]))
                    ->danger()
                    ->send();

                return;
            }
        }

        $data = $this->form->getState();

        $status = Password::sendResetLink(
            $data,
            function (UserContract $userContract, string $token): void {
                if (! method_exists($userContract, 'notify')) {
                    $userClass = $userContract::class;

                    throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                }

                $resetPassword = new ResetPasswordNotification($token);
                $resetPassword->url = FilamentJet::getResetPasswordUrl($token, $userContract);

                $userContract->notify($resetPassword);
            },
        );

        if ($status === Password::RESET_THROTTLED) {
            Notification::make()
                ->title(__($status))
                ->danger()
                ->send();

            return;
        }

        $this->form->fill();

        Notification::make()
            ->title(__(Password::RESET_LINK_SENT))
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return __('filament-jet::auth/password-reset/request-password-reset.title');
    }

    public function getHeading(): string
    {
        return __('filament-jet::auth/password-reset/request-password-reset.heading');
    }

    protected function getCardWidth(): string
    {
        $res = Features::getOption(Features::resetPasswords(), 'request.card_width');
        if (! is_string($res)) {
            throw new Exception('wip');
        }

        return $res;
    }

    protected function hasBrand(): bool
    {
        $res = Features::optionEnabled(Features::resetPasswords(), 'request.has_brand');
        if (! is_bool($res)) {
            throw new Exception('wip');
        }

        return $res;
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label(__('filament-jet::auth/password-reset/request-password-reset.fields.email.label'))
                ->email()
                ->required()
                ->autocomplete(),
        ];
    }
}
