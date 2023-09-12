<?php

namespace ArtMin96\FilamentJet\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class AddingTeam
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public mixed $owner)
    {
    }
}
