<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Traits\Properties;

use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Support\Collection;

trait HasSanctumPermissionsProperty
{
    public function getSanctumPermissionsProperty(): Collection
    {
        return collect(FilamentJet::$permissions)
            ->mapWithKeys(static fn($permission): array => [$permission => $permission]);
    }
}
