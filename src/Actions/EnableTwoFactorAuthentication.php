<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider;
use ArtMin96\FilamentJet\Events\TwoFactorAuthenticationEnabled;
use ArtMin96\FilamentJet\RecoveryCode;
use Illuminate\Support\Collection;

class EnableTwoFactorAuthentication
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
        protected TwoFactorAuthenticationProvider $twoFactorAuthenticationProvider
    )
    {
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
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, fn() => RecoveryCode::generate())->all(), JSON_THROW_ON_ERROR)),
        ])->save();

        TwoFactorAuthenticationEnabled::dispatch($user);
    }
}
