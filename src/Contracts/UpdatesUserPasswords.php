<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ---
 */
interface UpdatesUserPasswords
{
    public function update(UserContract $userContract, array $input): void;
}
