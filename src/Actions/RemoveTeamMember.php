<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\RemovesTeamMembers;
use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
// use ArtMin96\FilamentJet\Contracts\UserContract;;
use ArtMin96\FilamentJet\Events\TeamMemberRemoved;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class RemoveTeamMember implements RemovesTeamMembers
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
<<<<<<< HEAD
    private function authorize(UserContract $user, TeamContract $teamContract, UserContract $teamMember): void
    {
        if (Gate::forUser($user)->check('removeTeamMember', $teamContract)) {
            return;
=======
    protected function authorize(UserContract $user, TeamContract $teamContract, UserContract $teamMember): void
    {
        if (! Gate::forUser($user)->check('removeTeamMember', $teamContract) &&
            $user->id !== $teamMember->id) {
            throw new AuthorizationException;
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
        }
        if ($user->id === $teamMember->id) {
            return;
        }
        throw new AuthorizationException;
    }

    /**
     * Ensure that the currently authenticated user does not own the team.
     */
<<<<<<< HEAD
    private function ensureUserDoesNotOwnTeam(UserContract $userContract, TeamContract $teamContract): void
=======
    protected function ensureUserDoesNotOwnTeam(UserContract $userContract, TeamContract $teamContract): void
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    {
        if ($userContract->id === $teamContract->owner?->id) {
            throw ValidationException::withMessages([
                'team' => [__('filament-jet::teams/members.messages.cannot_leave_own_team')],
            ])->errorBag('removeTeamMember');
        }
    }
}
