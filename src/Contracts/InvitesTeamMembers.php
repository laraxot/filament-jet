<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ---
 */
interface InvitesTeamMembers
{
    public function invite(UserContract $userContract, TeamContract $teamContract, string $email, string $role = null): void;
}
