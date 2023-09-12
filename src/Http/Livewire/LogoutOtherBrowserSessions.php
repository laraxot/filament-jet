<?php

namespace ArtMin96\FilamentJet\Http\Livewire;

use ArtMin96\FilamentJet\Datas\SessionData;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;
use Livewire\Component;

final class LogoutOtherBrowserSessions extends Component
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
     * @return Agent
     */
<<<<<<< HEAD
    private function createAgent(mixed $session)
    {
        return tap(new Agent, static function ($agent) use ($session) : void {
=======
    protected function createAgent(mixed $session)
    {
        return tap(new Agent, function ($agent) use ($session): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $agent->setUserAgent($session->user_agent);
        });
    }
}
