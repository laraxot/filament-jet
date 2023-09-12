<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UpdatesTeamNames;
use ArtMin96\FilamentJet\Contracts\UserContract;
use Illuminate\Support\Facades\Gate;

class UpdateTeamName implements UpdatesTeamNames
{
    /**
     * Validate and update the given team's name.
     *
     * @param  array<string, string>  $input
     */
    public function update(UserContract $userContract, TeamContract $teamContract, array $input): void
    {
        Gate::forUser($userContract)->authorize('update', $teamContract);

        $teamContract->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
