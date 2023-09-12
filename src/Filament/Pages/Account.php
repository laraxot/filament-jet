<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Filament\Pages;

use ArtMin96\FilamentJet\Actions\DisableTwoFactorAuthentication;
use ArtMin96\FilamentJet\Contracts\UpdatesUserPasswords;
use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Datas\FilamentJetData;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Traits\CanDeleteAccount;
use ArtMin96\FilamentJet\Filament\Traits\CanLogoutOtherBrowserSessions;
use ArtMin96\FilamentJet\Filament\Traits\HasCachedAction;
use ArtMin96\FilamentJet\Filament\Traits\HasHiddenAction;
use ArtMin96\FilamentJet\Filament\Traits\HasTwoFactorAuthentication;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasUserProperty;
use ArtMin96\FilamentJet\Traits\ProcessesExport;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Bus\Batch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Livewire\Redirector;
use Phpsa\FilamentPasswordReveal\Password;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Undocumented class.
 *
 * @property UserContract       $user
 * @property ComponentContainer $form
 * @property ComponentContainer $updateProfileInformationForm
 * @property ComponentContainer $updatePasswordForm
 * @property ?Batch             $exportBatch
 */
class Account extends Page {
    use CanDeleteAccount;
    use CanLogoutOtherBrowserSessions;
    use HasCachedAction;
    use HasHiddenAction;
    use HasTwoFactorAuthentication;
    use HasUserProperty;
    use ProcessesExport;

    protected static string $view = 'filament-jet::filament.pages.account';

    public ?array $updateProfileInformationState = [];

    public ?string $currentPassword = null;

    public ?string $password = null;

    public ?string $passwordConfirmation = null;

    public static function shouldRegisterNavigation(): bool {
        $filamentJetData = FilamentJetData::make();

        return $filamentJetData->should_register_navigation->account;
    }

    public function mount(): void {
        $this->updateProfileInformationForm->fill($this->user->withoutRelations()->toArray());

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm')
            && is_null($this->user->two_factor_confirmed_at)) {
            app(DisableTwoFactorAuthentication::class)($this->user);
        }
        app(DisableTwoFactorAuthentication::class)($this->user);
    }

    /**
     * Update the user's profile information.
     *
     * @return Redirector|RedirectResponse
     */
    public function updateProfileInformation(UpdatesUserProfileInformation $updatesUserProfileInformation) {
        $updatesUserProfileInformation->update(
            $this->user,
            $this->updateProfileInformationForm->getState()
        );

        $this->notify(
            status: 'success',
            message: __('filament-jet::account/update-information.messages.updated'),
            isAfterRedirect: true
        );

        return to_route('filament.pages.account');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatesUserPasswords $updatesUserPasswords): void {
        $state = $this->updatePasswordForm->getState();

        $updatesUserPasswords->update($this->user, $state);

        Notification::make()
            ->title(__('filament-jet::account/update-password.messages.updated'))
            ->success()
            ->send();

        session()->forget('password_hash_'.config('filament.auth.guard'));
        if (! $this->user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
            throw new \Exception('strange things');
        }

        Filament::auth()->login($this->user);

        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    public function downloadPersonalData(): BinaryFileResponse {
        $path = glob(Storage::disk(config('personal-data-export.disk'))->path('').($this->user->id.'_*.zip'));

        $this->exportProgress = 0;
        $this->exportBatch = null;

        return response()->download(end($path))->deleteFileAfterSend();
    }

    protected function getForms(): array {
        return [
            'updateProfileInformationForm' => $this->makeForm()
                ->model(FilamentJet::userModel())
                ->schema($this->updateProfileFormSchema())
                ->statePath('updateProfileInformationState'),
            'updatePasswordForm' => $this->makeForm()
                ->schema($this->updatePasswordFormSchema()),
            'confirmTwoFactorForm' => $this->makeForm()
                ->schema($this->twoFactorFormSchema()),
        ];
    }

    private function updateProfileFormSchema(): array {
        if (! $this->user instanceof \Illuminate\Database\Eloquent\Model) {
            throw new \Exception('strange things');
        }

        return array_filter([
            Features::managesProfilePhotos()
                ? FileUpload::make('profile_photo_path')
                    ->image()
                    ->avatar()
                    ->disk($this->user->profilePhotoDisk())
                    ->directory($this->user->profilePhotoDirectory())
                    ->visible(Features::managesProfilePhotos())
                    ->rules(['nullable', 'mimes:jpg,jpeg,png', 'max:1024'])
                : null,
            TextInput::make('name')
                ->label(__('filament-jet::account/update-information.fields.name'))
                ->required()
                ->maxLength(255),
            TextInput::make(FilamentJet::username())
                ->label(__('filament-jet::account/update-information.fields.email'))
                ->hintAction(
                    ! empty(config('filament-jet.profile.login_field.hint_action')) && Features::enabled(Features::emailVerification())
                        ? Action::make('newEmailVerifyNote')
                            ->tooltip(config('filament-jet.profile.login_field.hint_action.tooltip'))
                            ->icon(config('filament-jet.profile.login_field.hint_action.icon'))
                        : null
                )
                ->email(fn (): bool => 'email' === FilamentJet::username())
                ->unique(
                    table: FilamentJet::userModel(),
                    column: FilamentJet::username(),
                    ignorable: $this->user
                )
                ->required()
                ->maxLength(255),
        ]);
    }

    private function updatePasswordFormSchema(): array {
        $requireCurrentPasswordOnUpdate = Features::optionEnabled(Features::updatePasswords(), 'askCurrentPassword');

        return array_filter([
            $requireCurrentPasswordOnUpdate
                ? Password::make('currentPassword')
                    ->label(__('filament-jet::account/update-password.fields.current_password'))
                    ->autocomplete('currentPassword')
                    ->revealable()
                    ->required()
                    ->rule('current_password')
                : null,
            Password::make('password')
                ->label(__('filament-jet::account/update-password.fields.new_password'))
                ->autocomplete('new_password')
                ->copyable()
                ->revealable()
                ->generatable()
                ->required()
                ->rules(FilamentJet::getPasswordRules())
                ->same('passwordConfirmation'),
            Password::make('passwordConfirmation')
                ->label(__('filament-jet::account/update-password.fields.confirm_password'))
                ->autocomplete('passwordConfirmation')
                ->revealable(),
        ]);
    }
}
