<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ---
 */
interface DeletesTeams
{
    /**
     * ---
     */
    public function delete(TeamContract $teamContract): void;
}
