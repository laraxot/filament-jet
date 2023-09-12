<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ---
 */
interface ResetsUserPasswords
{
    public function reset(UserContract $userContract, array $input): void;
}
