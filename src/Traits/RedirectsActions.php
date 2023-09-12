<?php

namespace ArtMin96\FilamentJet\Traits;

use Illuminate\Http\RedirectResponse;
<<<<<<< HEAD
use Illuminate\Routing\Redirector;
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

trait RedirectsActions
{
    /**
     * Get the redirect response for the given action.
     *
     * @param  object  $action
     * @return RedirectResponse|Response|Redirector
     */
    public function redirectPath($action)
    {
        if (method_exists($action, 'redirectTo')) {
            $response = $action->redirectTo();
        } else {
            $response = property_exists($action, 'redirectTo')
                ? $action->redirectTo
                : config('filament.path');
        }

        return $response instanceof Response ? $response : redirect($response);
    }
}
