<?php

namespace ArtMin96\FilamentJet;

use Illuminate\Support\Str;

final class RecoveryCode
{
    /**
     * Generate a new recovery code.
     */
    public static function generate(): string
    {
        return Str::random(10).'-'.Str::random(10);
    }
}
