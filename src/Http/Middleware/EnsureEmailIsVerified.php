<?php

namespace ArtMin96\FilamentJet\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

final class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
<<<<<<< HEAD
     * @param Request $request
=======
     * @param  Request  $request
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
     * @param  string|null  $redirectToRoute
     * @return Response|RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
                ! $request->user()->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: jetRouteActions()->emailVerificationPromptRoute()));
        }

        return $next($request);
    }
}
