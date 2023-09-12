<?php

namespace ArtMin96\FilamentJet\Filament\Pages\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Auth\Authenticatable;
use ArtMin96\FilamentJet\Actions\CreateNewUser;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\CardPage;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Traits\RedirectsActions;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Phpsa\FilamentPasswordReveal\Password;

/**
 * Undocumented class
 *
 * @property UserContract $user
 * @property ComponentContainer $form
 */
final class Register extends CardPage
{
    use WithRateLimiting;
    use RedirectsActions;

    protected static string $view = 'filament-jet::filament.pages.auth.register';

    public ?string $email = '';

    public ?string $name = '';

    public ?string $password = '';

    public ?string $passwordConfirmation = '';

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    /**
     * Undocumented function
     *
     * @return RedirectResponse|Response|Redirector|null
     */
    public function register(CreateNewUser $createNewUser)
    {
        $rateLimitingOptionEnabled = Features::getOption(Features::registration(), 'rate_limiting.enabled');

        if ($rateLimitingOptionEnabled) {
            try {
                $this->rateLimit(Features::getOption(Features::registration(), 'rate_limiting.limit'));
            } catch (TooManyRequestsException $exception) {
                Notification::make()
                    ->title(__('filament-jet::auth/register.messages.throttled', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]))
                    ->danger()
                    ->send();

                return null;
            }
        }

        $data = $this->form->getState();

        $userContract = $createNewUser->create($data);
        if (! $userContract instanceof Authenticatable) {
            throw new Exception('user no authenticable');
        }

        Filament::auth()->login(
            user: $userContract,
            remember: true
        );

        session()->regenerate();

        return $this->redirectPath($createNewUser);
    }

    protected function getTitle(): string
    {
        return __('filament-jet::auth/register.title');
    }

    protected function getHeading(): string
    {
        return __('filament-jet::auth/register.heading');
    }

    protected function getCardWidth(): string
    {
        $res = Features::getOption(Features::registration(), 'card_width');
        if (! is_string($res)) {
            throw new Exception('wip');
        }

        return $res;
    }

    protected function hasBrand(): bool
    {
        $res = Features::optionEnabled(Features::registration(), 'has_brand');
        if (! is_bool($res)) {
            throw new Exception('wip');
        }

        return $res;
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('filament-jet::auth/register.fields.name.label'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('filament-jet::auth/register.fields.email.label'))
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(FilamentJet::userModel()),
            Password::make('password')
                ->label(__('filament-jet::auth/register.fields.password.label'))
                ->required()
                ->same('passwordConfirmation')
                ->revealable()
                ->copyable()
                ->generatable()
                ->rules(FilamentJet::getPasswordRules()),
            Password::make('passwordConfirmation')
                ->label(__('filament-jet::auth/register.fields.passwordConfirmation.label'))
                ->required()
                ->revealable(),
            Checkbox::make('terms')
                ->label(
                    new HtmlString(
                        __('filament-jet::auth/register.fields.terms_and_policy.label', [
                            'terms_of_service' => '<a target="_blank" href="'.route(config('filament-jet.route_group_prefix').'terms').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('filament-jet::auth/register.fields.terms_and_policy.terms_of_service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route(config('filament-jet.route_group_prefix').'policy').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('filament-jet::auth/register.fields.terms_and_policy.privacy_policy').'</a>',
                        ])
                    )
                )
                ->rules(
                    FilamentJet::hasTermsAndPrivacyPolicyFeature()
                        ? ['accepted', 'required']
                        : []
                )
                ->validationAttribute('terms')
                ->visible(FilamentJet::hasTermsAndPrivacyPolicyFeature()),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getMessages(): array
    {
        return [
            'password.same' => __('validation.confirmed', ['attribute' => __('filament-jet::auth/register.fields.password.validation_attribute')]),
        ];
    }
}
