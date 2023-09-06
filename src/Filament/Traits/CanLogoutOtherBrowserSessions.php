<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use ArtMin96\FilamentJet\Datas\SessionData;
use Filament\Notifications\Notification;

trait CanLogoutOtherBrowserSessions
{
    /**
     * Log out from other browser sessions.
     */
    public function logoutOtherBrowserSessions(): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        auth()->logoutOtherDevices('password');

        $this->deleteOtherSessionRecords();

        request()->session()->put([
            'password_hash_'.auth()->getDefaultDriver() => auth()->user()?->getAuthPassword(),
        ]);

        $this->emit('loggedOut');

        Notification::make()
            ->title(__('filament-jet::account/browser-sessions.messages.cleared'))
            ->success()
            ->send();
    }

    /**
     * Delete the other browser session records from storage.
     *
     * @return void
     */
    protected function deleteOtherSessionRecords()
    {
        $sessionData = SessionData::make();
        $sessionData->deleteOtherSessionRecords();
    }
}
