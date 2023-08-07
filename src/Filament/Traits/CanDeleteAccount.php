<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Datas\FilamentData;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

trait CanDeleteAccount
{
    /**
     * Delete the current user.
     */
    public function deleteAccount(Request $request, DeletesUsers $deleter): Redirector|RedirectResponse
    {
        $user = Auth::user()?->fresh();
        if (! $user instanceof UserContract) {
            throw new \Exception('put usercontract in user');
        }
        $deleter->delete($user);

        Filament::auth()->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        $filamentData = FilamentData::make();

        return redirect($filamentData->path);
    }
}
