<?php

namespace ArtMin96\FilamentJet\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Response;

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
