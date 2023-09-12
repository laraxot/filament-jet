<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ---
 */
interface UpdatesUserProfileInformation
{
    public function update(UserContract $userContract, array $input): void;
}
