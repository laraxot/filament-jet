<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\InvitesTeamMembers;
use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\TeamInvitationContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\InvitingTeamMember;
use ArtMin96\FilamentJet\Mail\TeamInvitation;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

final class InviteTeamMember implements InvitesTeamMembers
{
    /**
     * Invite a new team member to the given team.
     */
    public function invite(UserContract $userContract, TeamContract $teamContract, string $email, string $role = null): void
    {
        Gate::forUser($userContract)->authorize('addTeamMember', $teamContract);

        InvitingTeamMember::dispatch($teamContract, $email, $role);

        $model = $teamContract->teamInvitations()->create([
            'email' => $email,
            'role' => $role,
        ]);
        if (! $model instanceof TeamInvitationContract) {
            throw new Exception('invitation must implements TeamInvitationContract');
        }

        Mail::to($email)->send(new TeamInvitation($model));
    }
}
