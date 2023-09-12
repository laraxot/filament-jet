<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions\Auth;

use ArtMin96\FilamentJet\FilamentJet;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;

class AttemptToAuthenticate {
    /**
     * Create a new controller instance.
     */
    public function __construct(
        /**
         * The guard implementation.
         */
        protected StatefulGuard $statefulGuard
    ) {
    }

    /**
     * @param array<string, string> $data
     *
     * @return array|null
     */
    public function handle(array $data, \Closure $next) {
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
    private function throwFailedAuthenticationException(): never {
        throw ValidationException::withMessages([FilamentJet::username() => [trans('auth.failed')]]);
    }
}
