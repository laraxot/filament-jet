<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\DeletesTeams;
use ArtMin96\FilamentJet\Contracts\TeamContract;

final class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     */
    public function delete(TeamContract $teamContract): void
    {
        $teamContract->purge();
    }
}
