<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider;
use ArtMin96\FilamentJet\Events\TwoFactorAuthenticationEnabled;
use ArtMin96\FilamentJet\RecoveryCode;
use Illuminate\Support\Collection;

final class EnableTwoFactorAuthentication
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
     * Enable two factor authentication for the user.
     *
     * @return void
     */
    public function __invoke(mixed $user)
    {
        $user->forceFill([
            'two_factor_secret' => encrypt($this->twoFactorAuthenticationProvider->generateSecretKey()),
<<<<<<< HEAD
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, static fn() => RecoveryCode::generate())->all(), JSON_THROW_ON_ERROR)),
=======
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, fn () => RecoveryCode::generate())->all(), JSON_THROW_ON_ERROR)),
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
        ])->save();

        TwoFactorAuthenticationEnabled::dispatch($user);
    }
}
