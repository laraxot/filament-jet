<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ---
 */
interface RemovesTeamMembers
{
    public function remove(UserContract $user, TeamContract $teamContract, UserContract $teamMember): void;
}
