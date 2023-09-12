<?php

namespace ArtMin96\FilamentJet\Actions;

use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Exception;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;

final class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(UserContract $userContract, array $input): void
    {
        if (Features::managesProfilePhotos()) {
            if (! method_exists($userContract, 'updateProfilePhoto')) {
                throw new Exception('method updateProfilePhoto not exists in user');
            }
<<<<<<< HEAD
            
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $userContract->updateProfilePhoto($input['profile_photo_path']);
        }

        if ($input[FilamentJet::email()] !== $userContract->{FilamentJet::email()} &&
            $userContract instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($userContract, $input);
        } else {
            $userContract->forceFill([
                'name' => $input['name'],
                FilamentJet::username() => $input[FilamentJet::username()],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
<<<<<<< HEAD
    private function updateVerifiedUser(UserContract $userContract, array $input): void
=======
    protected function updateVerifiedUser(UserContract $userContract, array $input): void
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    {
        $userContract->forceFill([
            'name' => $input['name'],
            FilamentJet::email() => $input[FilamentJet::email()],
            FilamentJet::email().'_verified_at' => null,
        ])->save();

        app()->bind(
            SendEmailVerificationNotification::class,
            \ArtMin96\FilamentJet\Listeners\Auth\SendEmailVerificationNotification::class,
        );
    }
}
