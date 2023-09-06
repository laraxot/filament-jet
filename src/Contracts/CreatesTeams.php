<?php

namespace ArtMin96\FilamentJet\Contracts;

interface CreatesTeams
{
    public function create(UserContract $userContract, array $input): TeamContract;
}
