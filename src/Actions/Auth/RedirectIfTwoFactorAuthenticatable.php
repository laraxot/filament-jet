<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions\Auth;

use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\TwoFactorAuthenticationChallenged;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Traits\TwoFactorAuthenticatable;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Livewire\Redirector;

class RedirectIfTwoFactorAuthenticatable {
    /**
     * Create a new controller instance.
     */
    public function __construct(
        /**
         * Undocumented variable.
         */
        protected StatefulGuard $statefulGuard
    ) {
    }

    /**
     * Undocumented function.
     *
     * @return mixed
     */
    public function handle(array $data, \Closure $next) {
        $userContract = $this->validateCredentials($data);

        if (FilamentJet::confirmsTwoFactorAuthentication()) {
            if ($userContract->two_factor_secret &&
                ! is_null($userContract->two_factor_confirmed_at) &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($userContract))) {
                return $this->twoFactorChallengeResponse($data, $userContract);
            }

            return $next($data);
        }

        if (optional($userContract)->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($userContract))) {
            return $this->twoFactorChallengeResponse($data, $userContract);
        }

        return $next($data);
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @param array<string, string> $data
     *
     * @return UserContract
     */
    private function validateCredentials(array $data) {
        $userProvider = $this->statefulGuard->getProvider();
        if (! method_exists($userProvider, 'getModel')) {
            throw new \Exception('strange things');
        }

        $model = $userProvider->getModel();

        return tap($model::where(FilamentJet::username(), $data[FilamentJet::username()])->first(), function ($user) use ($data): void {
            if (! $user || ! $this->statefulGuard->getProvider()->validateCredentials($user, ['password' => $data['password']])) {
                $this->fireFailedEvent($data, $user);

                $this->throwFailedAuthenticationException();
            }
        });
    }

    /**
     * Throw a failed authentication validation exception.
     */
    private function throwFailedAuthenticationException(): never {
        throw ValidationException::withMessages([FilamentJet::username() => [trans('auth.failed')]]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param array<string, string> $data
     */
    private function fireFailedEvent(array $data, UserContract $userContract = null): void {
        if ($userContract instanceof UserContract && ! $userContract instanceof Authenticatable) {
            throw new \Exception('strange things');
        }
        event(new Failed(config('filament.auth.guard'), $userContract, [
            FilamentJet::username() => $data[FilamentJet::username()],
            'password' => $data['password'],
        ]));
    }

    /**
     * Get the two factor authentication enabled response.
     */
    private function twoFactorChallengeResponse(array $data, UserContract $userContract): Redirector|RedirectResponse {
        session()->put([
            jet()->getTwoFactorLoginSessionPrefix().'login.id' => $userContract->getKey(),
            jet()->getTwoFactorLoginSessionPrefix().'login.remember' => $data['remember'],
        ]);

        TwoFactorAuthenticationChallenged::dispatch($userContract);

        return to_route('auth.two-factor.login');
    }
}
