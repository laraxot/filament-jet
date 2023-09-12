<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Events\RecoveryCodesGenerated;
use ArtMin96\FilamentJet\RecoveryCode;
use Illuminate\Support\Collection;

final class GenerateNewRecoveryCodes
{
    /**
     * Generate new recovery codes for the user.
     *
     * @return void
     */
    public function __invoke(mixed $user)
    {
        $user->forceFill([
<<<<<<< HEAD
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, static fn() => RecoveryCode::generate())->all(), JSON_THROW_ON_ERROR)),
=======
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, fn () => RecoveryCode::generate())->all(), JSON_THROW_ON_ERROR)),
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
        ])->save();

        RecoveryCodesGenerated::dispatch($user);
    }
}
