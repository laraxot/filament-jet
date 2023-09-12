<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\TwoFactorAuthenticationConfirmed;
use Illuminate\Validation\ValidationException;

final class ConfirmTwoFactorAuthentication
{
    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct(
        /**
         * The two factor authentication provider.
         */
<<<<<<< HEAD
        private readonly TwoFactorAuthenticationProvider $twoFactorAuthenticationProvider
    )
    {
=======
        protected TwoFactorAuthenticationProvider $twoFactorAuthenticationProvider
    ) {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    }

    /**
     * Confirm the two factor authentication configuration for the user.
     *
     * @param  UserContract  $user
     * @param  string  $code
     * @return void
     *
     * @throws ValidationException
     */
    public function __invoke($user, $code)
    {
        if (empty($user->two_factor_secret) ||
            empty($code) ||
            ! $this->twoFactorAuthenticationProvider->verify(decrypt($user->two_factor_secret), $code)) {
            throw ValidationException::withMessages([
                'two_factor_code' => [__('filament-jet::account/two-factor.messages.invalid_confirmation_code')],
            ])->errorBag('confirmTwoFactorAuthentication');
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        TwoFactorAuthenticationConfirmed::dispatch($user);
    }
}
