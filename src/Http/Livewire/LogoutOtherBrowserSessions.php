<?php

namespace ArtMin96\FilamentJet\Http\Livewire;

use ArtMin96\FilamentJet\Datas\SessionData;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;
use Livewire\Component;

class LogoutOtherBrowserSessions extends Component
{
    public function render(): View
    {
        return view('filament-jet::livewire.logout-other-browser-sessions');
    }

    /**
     * Get the current sessions.
     */
    public function getSessionsProperty(): Collection
    {
        $sessionData = SessionData::make();

        return $sessionData->getSessionsProperty();
    }

    protected function getListeners(): array
    {
        return [
            'loggedOut' => '$refresh',
        ];
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return \Jenssegers\Agent\Agent
     */
    protected function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
