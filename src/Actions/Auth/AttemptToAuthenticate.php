<?php

namespace ArtMin96\FilamentJet\Actions\Auth;

use ArtMin96\FilamentJet\FilamentJet;
use Closure;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;

class AttemptToAuthenticate
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        /**
         * The guard implementation.
         */
        protected StatefulGuard $statefulGuard
    )
    {
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
    protected function throwFailedAuthenticationException(): never
    {
        throw ValidationException::withMessages([
            FilamentJet::username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param  array<string, string>  $data
     */
    protected function fireFailedEvent(array $data): void
    {
        if (! is_string(config('filament.auth.guard'))) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }

        event(new Failed(config('filament.auth.guard'), null, [
            FilamentJet::username() => $data[FilamentJet::username()],
            'password' => $data['password'],
        ]));
    }
}
