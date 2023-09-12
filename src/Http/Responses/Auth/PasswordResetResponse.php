<?php

namespace ArtMin96\FilamentJet\Http\Responses\Auth;

use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\PasswordResetResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Redirector;

final class PasswordResetResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->to((string) Filament::getUrl());
    }
}
