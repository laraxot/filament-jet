<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\RemovesTeamMembers;
use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
// use ArtMin96\FilamentJet\Contracts\UserContract;;
use ArtMin96\FilamentJet\Events\TeamMemberRemoved;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RemoveTeamMember implements RemovesTeamMembers
{
    /**
     * Remove the team member from the given team.
     */
    public function remove(UserContract $user, TeamContract $teamContract, UserContract $teamMember): void
    {
        $this->authorize($user, $teamContract, $teamMember);

        $this->ensureUserDoesNotOwnTeam($teamMember, $teamContract);

        $teamContract->removeUser($teamMember);

        TeamMemberRemoved::dispatch($teamContract, $teamMember);
    }

    /**
     * Authorize that the user can remove the team member.
     */
    protected function authorize(UserContract $user, TeamContract $teamContract, UserContract $teamMember): void
    {
        if (! Gate::forUser($user)->check('removeTeamMember', $teamContract)
            && $user->id !== $teamMember->id) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the team.
     */
    protected function ensureUserDoesNotOwnTeam(UserContract $userContract, TeamContract $teamContract): void
    {
        if ($userContract->id === $teamContract->owner?->id) {
            throw ValidationException::withMessages(['team' => [__('filament-jet::teams/members.messages.cannot_leave_own_team')]])->errorBag('removeTeamMember');
        }
    }
}
