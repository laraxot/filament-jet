<?php

namespace ArtMin96\FilamentJet\Notifications\Auth;

use Filament\Forms\ComponentContainer;
use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;

/**
 * Undocumented class
 *
 * @property ComponentContainer $form
 */
class ResetPassword extends BaseNotification
{
    public string $url;

    protected function resetUrl($notifiable): string
    {
        return $this->url;
    }
}
