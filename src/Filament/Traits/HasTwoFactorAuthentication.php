<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use ArtMin96\FilamentJet\Actions\ConfirmTwoFactorAuthentication;
use ArtMin96\FilamentJet\Actions\DisableTwoFactorAuthentication;
use ArtMin96\FilamentJet\Actions\EnableTwoFactorAuthentication;
use ArtMin96\FilamentJet\Actions\GenerateNewRecoveryCodes;
use ArtMin96\FilamentJet\Features;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

/**
 * Undocumented trait
 *
 * @property UserContract $user
 */
trait HasTwoFactorAuthentication
{
    /**
     * Indicates if two factor authentication QR code is being displayed.
     *
     * @var bool
     */
    public $showingQrCode = false;

    /**
     * Indicates if the two factor authentication confirmation input and button are being displayed.
     *
     * @var bool
     */
    public $showingConfirmation = false;

    /**
     * Indicates if two factor authentication recovery codes are being displayed.
     *
     * @var bool
     */
    public $showingRecoveryCodes = false;

    /**
     * The OTP code for confirming two factor authentication.
     *
     * @var string|null
     */
    public $two_factor_code;

    protected function twoFactorFormSchema(): array
    {
        return [
            TextInput::make('two_factor_code')
                ->label(__('filament-jet::account/two-factor.fields.code'))
                ->disableLabel()
                ->placeholder(__('filament-jet::account/two-factor.fields.code'))
                ->rules('nullable|string'),
        ];
    }

    /**
     * Enable two factor authentication for the user.
     *
     * @return void
     */
    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable)
    {
        $enable($this->user);

        $this->showingQrCode = true;

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm')) {
            $this->showingConfirmation = true;
        } else {
            $this->showingRecoveryCodes = true;
        }
    }

    /**
     * Confirm two factor authentication for the user.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function confirmTwoFactorAuthentication(ConfirmTwoFactorAuthentication $confirm)
    {
        if ($this->two_factor_code == null) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        $confirm($this->user, $this->two_factor_code);

        Notification::make()
            ->title(__('filament-jet::account/two-factor.messages.verified'))
            ->success()
            ->send();

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;

        $this->two_factor_code = null;
    }

    /**
     * Generate new recovery codes for the user.
     *
     * @return void
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate)
    {
        $generate($this->user);

        $this->showingRecoveryCodes = true;

        Notification::make()
            ->title(__('filament-jet::account/two-factor.messages.recovery_codes_regenerated'))
            ->success()
            ->send();
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @return void
     */
    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable)
    {
        $disable($this->user);

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;

        Notification::make()
            ->title(__('filament-jet::account/two-factor.messages.disabled'))
            ->warning()
            ->send();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function exportPersonalData()
    {
        $this->export();
    }

    /**
     * Display the user's recovery codes.
     *
     * @return void
     */
    public function showRecoveryCodes()
    {
        $this->showingRecoveryCodes = true;
    }

    /**
     * Hide the user's recovery codes.
     *
     * @return void
     */
    public function hideRecoveryCodes()
    {
        $this->showingRecoveryCodes = false;
    }

    /**
     * Determine if two factor authentication is enabled.
     *
     * @return bool
     */
    public function getEnabledProperty()
    {
        return ! empty($this->user->two_factor_secret);
    }
}
