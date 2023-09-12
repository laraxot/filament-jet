<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\UpdatesUserPasswords;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Traits\PasswordValidationRules;
use Exception;
use Illuminate\Support\Facades\Hash;

final class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Update the user's password.
     */
    public function update(UserContract $userContract, array $input): void
    {
        if (! method_exists($userContract, 'forceFill')) {
            throw new Exception('forceFill method not exists in user');
        }
        
        $userContract->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
