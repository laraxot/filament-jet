<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class ValidateTeamDeletion
{
    /**
     * Validate that the team can be deleted by the given user.
     */
    public function validate(UserContract $userContract, TeamContract $teamContract): void
    {
        Gate::forUser($userContract)->authorize('delete', $teamContract);

        if ($teamContract->personal_team) {
            Notification::make()
                ->title(__('filament-jet::teams/delete.messages.cannot_delete_personal_team'))
                ->warning()
                ->send();
        }
    }
}
