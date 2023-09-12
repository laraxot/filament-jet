<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation {
    /**
     * Validate and update the given user's profile information.
     *
     * @param array<string, string> $input
     */
    public function update(UserContract $userContract, array $input): void {
        if (Features::managesProfilePhotos()) {
            if (! method_exists($userContract, 'updateProfilePhoto')) {
                throw new \Exception('method updateProfilePhoto not exists in user');
            }
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
     * @param array<string, string> $input
     */
    private function updateVerifiedUser(UserContract $userContract, array $input): void {
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
