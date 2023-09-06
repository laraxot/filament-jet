<?php

namespace ArtMin96\FilamentJet\Listeners\Auth;

use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Notifications\Auth\VerifyEmail;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification as BaseListener;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class SendEmailVerificationNotification extends BaseListener
{
    public function handle(Registered $registered): void
    {
        if (! $registered->user instanceof MustVerifyEmail) {
            return;
        }

        if ($registered->user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($registered->user, 'notify')) {
            $userClass = $registered->user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        if (! $registered->user instanceof UserContract) {
            throw new Exception('strange things');
        }

        $verifyEmail = new VerifyEmail;
        $verifyEmail->url = FilamentJet::getVerifyEmailUrl($registered->user);

        $registered->user->notify($verifyEmail);
    }
}
