<?php

namespace ArtMin96\FilamentJet\Actions\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\TwoFactorAuthenticationChallenged;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Traits\TwoFactorAuthenticatable;
use Closure;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Livewire\Redirector;

final class RedirectIfTwoFactorAuthenticatable
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        /**
         * Undocumented variable
         */
<<<<<<< HEAD
        private readonly StatefulGuard $statefulGuard
    )
    {
=======
        protected StatefulGuard $statefulGuard
    ) {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function handle(array $data, Closure $next)
    {
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
     * @param  array<string, string>  $data
     * @return UserContract
     */
    private function validateCredentials(array $data)
    {
        $userProvider = $this->statefulGuard->getProvider();
        if (! method_exists($userProvider, 'getModel')) {
            throw new Exception('strange things');
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

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param  array<string, string>  $data
     */
<<<<<<< HEAD
    private function fireFailedEvent(array $data, UserContract $userContract = null): void
=======
    protected function fireFailedEvent(array $data, UserContract $userContract = null): void
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    {
        if ($userContract instanceof UserContract && ! $userContract instanceof Authenticatable) {
            throw new Exception('strange things');
        }
<<<<<<< HEAD
        
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
        event(new Failed(config('filament.auth.guard'), $userContract, [
            FilamentJet::username() => $data[FilamentJet::username()],
            'password' => $data['password'],
        ]));
    }

    /**
     * Get the two factor authentication enabled response.
     */
<<<<<<< HEAD
    private function twoFactorChallengeResponse(array $data, UserContract $userContract): Redirector|RedirectResponse
=======
    protected function twoFactorChallengeResponse(array $data, UserContract $userContract): Redirector|RedirectResponse
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    {
        session()->put([
            jet()->getTwoFactorLoginSessionPrefix().'login.id' => $userContract->getKey(),
            jet()->getTwoFactorLoginSessionPrefix().'login.remember' => $data['remember'],
        ]);

        TwoFactorAuthenticationChallenged::dispatch($userContract);

        return to_route('auth.two-factor.login');
    }
}
