<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Traits\Properties;

use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Support\Collection;

trait HasSanctumPermissionsProperty
{
    public function getSanctumPermissionsProperty(): Collection
    {
        return collect(FilamentJet::$permissions)
<<<<<<< HEAD
            ->mapWithKeys(static fn($permission): array => [$permission => $permission]);
=======
            ->mapWithKeys(fn ($permission): array => [$permission => $permission]);
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    }
}
