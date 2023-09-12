<?php

declare(strict_types=1);

namespace App\Policies;

use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserContract $userContract): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserContract $userContract, TeamContract $teamContract): bool
    {
        return $userContract->belongsToTeam($teamContract);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserContract $userContract): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserContract $userContract, TeamContract $teamContract): bool
    {
        return $userContract->ownsTeam($teamContract);
    }

    /**
     * Determine whether the user can add team members.
     */
    public function addTeamMember(UserContract $userContract, TeamContract $teamContract): bool
    {
        return $userContract->ownsTeam($teamContract);
    }

    /**
     * Determine whether the user can update team member permissions.
     */
    public function updateTeamMember(UserContract $userContract, TeamContract $teamContract): bool
    {
        return $userContract->ownsTeam($teamContract);
    }

    /**
     * Determine whether the user can remove team members.
     */
    public function removeTeamMember(UserContract $userContract, TeamContract $teamContract): bool
    {
        return $userContract->ownsTeam($teamContract);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserContract $userContract, TeamContract $teamContract): bool
    {
        return $userContract->ownsTeam($teamContract);
    }
}
