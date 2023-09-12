<?php

namespace ArtMin96\FilamentJet\Actions\Auth;

use Closure;

class PrepareAuthenticatedSession
{
    /**
     * @param  array<string, string>  $data
     * @return mixed
     */
    public function handle(array $data, Closure $next)
    {
        session()->regenerate();

        return $next($data);
    }
}
