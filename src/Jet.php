<?php

namespace ArtMin96\FilamentJet;

use Exception;

class Jet
{
    /**
     * Undocumented function
     */
    public function getTwoFactorLoginSessionPrefix(): string
    {
        $res = Features::getOption(Features::twoFactorAuthentication(), 'authentication.session_prefix');
        if (! is_string($res)) {
            throw new Exception('strange things');
        }

        return $res;
    }
}
