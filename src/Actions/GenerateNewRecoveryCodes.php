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
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, static fn() => RecoveryCode::generate())->all(), JSON_THROW_ON_ERROR)),
        ])->save();

        RecoveryCodesGenerated::dispatch($user);
    }
}
