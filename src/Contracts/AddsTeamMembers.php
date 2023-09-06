<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ---
 */
interface AddsTeamMembers
{
    public function add(UserContract $userContract, TeamContract $teamContract, string $email, string $role = null): void;
}
