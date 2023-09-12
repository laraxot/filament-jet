<?php

namespace App\Models;

use ArtMin96\FilamentJet\Models\Membership as FilamentJetMembership;

final class Membership extends FilamentJetMembership
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
