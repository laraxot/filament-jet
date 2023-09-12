<?php

namespace ArtMin96\FilamentJet\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail as BaseNotification;

final class VerifyEmail extends BaseNotification
{
    public string $url;

    protected function verificationUrl($notifiable): string
    {
        return $this->url;
    }
}
