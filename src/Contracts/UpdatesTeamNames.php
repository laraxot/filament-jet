<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ---
 */
interface UpdatesTeamNames
{
    public function update(UserContract $userContract, TeamContract $teamContract, array $input): void;
}
