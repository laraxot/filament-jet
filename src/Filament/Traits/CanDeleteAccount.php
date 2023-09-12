<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Datas\FilamentData;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

trait CanDeleteAccount
{
    /**
     * Delete the current user.
     */
    public function deleteAccount(Request $request, DeletesUsers $deletesUsers): Redirector|RedirectResponse
    {
        $user = auth()->user()?->fresh();
        if (! $user instanceof UserContract) {
            throw new Exception('put usercontract in user');
        }
<<<<<<< HEAD
        
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
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
