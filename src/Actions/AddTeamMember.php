<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\AddsTeamMembers;
use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\AddingTeamMember;
use ArtMin96\FilamentJet\Events\TeamMemberAdded;
use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Support\Facades\Gate;

class AddTeamMember implements AddsTeamMembers
{
    /**
     * Add a new team member to the given team.
     */
    public function add(UserContract $user, TeamContract $team, string $email, string $role = null): void
    {
        Gate::forUser($user)->authorize('addTeamMember', $team);

        $newTeamMember = FilamentJet::findUserByEmailOrFail($email);

        AddingTeamMember::dispatch($team, $newTeamMember);

        $team->users()->attach(
            $newTeamMember, ['role' => $role]
        );

        TeamMemberAdded::dispatch($team, $newTeamMember);
    }
}
