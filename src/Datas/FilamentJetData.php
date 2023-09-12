<?php

namespace ArtMin96\FilamentJet\Datas;

use Exception;
use Spatie\LaravelData\Data;

class FilamentJetData extends Data
{
    public ShouldRegisterNavigationData $should_register_navigation;

    public string $passwords;

    public static function make(): self
    {
        $data = config('filament-jet');
        if (! is_array($data)) {
            throw new Exception('config filament-jet is not an array');
        }

        return self::from($data);
    }
}
