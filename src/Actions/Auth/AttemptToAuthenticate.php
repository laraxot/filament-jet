<?php

namespace ArtMin96\FilamentJet\Actions\Auth;

use ArtMin96\FilamentJet\FilamentJet;
use Closure;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;

final class AttemptToAuthenticate
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        /**
         * The guard implementation.
         */
        protected StatefulGuard $statefulGuard
<<<<<<< HEAD
    )
    {
=======
    ) {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    }

    /**
     * @param  array<string, string>  $data
     * @return array|null
     */
    public function handle(array $data, Closure $next)
    {
        if ($this->statefulGuard->attempt([
            FilamentJet::username() => $data[FilamentJet::username()],
            'password' => $data['password'],
        ], (bool) $data['remember'])) {
            return $next($data);
        }

        $this->throwFailedAuthenticationException();
    }

    /**
     * Throw a failed authentication validation exception.
     */
<<<<<<< HEAD
    private function throwFailedAuthenticationException(): never
=======
    protected function throwFailedAuthenticationException(): never
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    {
        throw ValidationException::withMessages([
            FilamentJet::username() => [trans('auth.failed')],
        ]);
    }
}
