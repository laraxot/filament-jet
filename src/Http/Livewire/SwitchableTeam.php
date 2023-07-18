<?php

namespace ArtMin96\FilamentJet\Http\Livewire;

use ArtMin96\FilamentJet\Events\TeamSwitched;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasUserProperty;
use ArtMin96\FilamentJet\Models\Team;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Auth\User;
use Illuminate\View\View;
use Livewire\Component;

class SwitchableTeam extends Component
{
    use HasUserProperty;

    public $teams;

    public $user;

    public function mount(): void
    {
        $this->user = Filament::auth()->user();
        if ($this->user == null) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }
        $this->teams = $this->user->allTeams();
    }

    /**
     * Update the authenticated user's current team.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function switchTeam(string $teamId)
    {
        $team = FilamentJet::newTeamModel()->findOrFail($teamId);

        if ($this->user == null) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        if (! $this->user->switchTeam($team)) {
            abort(403);
        }

        TeamSwitched::dispatch($team->fresh(), $this->user);

        Notification::make()
            ->title(__('Team switched'))
            ->success()
            ->send();

        return redirect(config('filament.path'), 303);
    }

    public function render(): View
    {
        return view('filament-jet::components.switchable-team');
    }
}
