<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\TwoFactorAuthenticationConfirmed;
use Illuminate\Validation\ValidationException;

class ConfirmTwoFactorAuthentication {
    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct(
        /**
         * The two factor authentication provider.
         */
        protected TwoFactorAuthenticationProvider $twoFactorAuthenticationProvider
    ) {
    }

    /**
     * Confirm the two factor authentication configuration for the user.
     *
     * @param UserContract $user
     * @param string       $code
     *
     * @throws ValidationException
     *
     * @return void
     */
    public function __invoke($user, $code) {
        if (empty($user->two_factor_secret) ||
            empty($code) ||
            ! $this->twoFactorAuthenticationProvider->verify(decrypt($user->two_factor_secret), $code)) {
            throw ValidationException::withMessages(['two_factor_code' => [__('filament-jet::account/two-factor.messages.invalid_confirmation_code')]])->errorBag('confirmTwoFactorAuthentication');
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        TwoFactorAuthenticationConfirmed::dispatch($user);
    }
}
