<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Filament\Traits;

use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Datas\FilamentData;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

trait CanDeleteAccount {
    /**
     * Delete the current user.
     */
    public function deleteAccount(Request $request, DeletesUsers $deletesUsers): Redirector|RedirectResponse {
        $user = auth()->user()?->fresh();
        if (! $user instanceof UserContract) {
            throw new \Exception('put usercontract in user');
        }
        $deletesUsers->delete($user);

        Filament::auth()->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $filamentData = FilamentData::make();

        return redirect($filamentData->path);
    }
}
