<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use ArtMin96\FilamentJet\Contracts\ResetsUserPasswords;
use ArtMin96\FilamentJet\Contracts\UserContract;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class ResetUserPassword implements ResetsUserPasswords
{
    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(UserContract $userContract, array $input): void
    {
        if (! method_exists($userContract, 'forceFill')) {
            throw new Exception('forceFill method not exists in user');
        }
        
        $userContract->forceFill([
            'password' => Hash::make($input['password']),
            'remember_token' => Str::random(60),
        ])->save();
        if (! $userContract instanceof Authenticatable) {
            throw new Exception('user must implements Authenticatable');
        }

        event(new PasswordReset($userContract));
    }
}
