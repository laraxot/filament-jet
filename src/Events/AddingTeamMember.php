<?php

namespace ArtMin96\FilamentJet\Events;

use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use Illuminate\Foundation\Events\Dispatchable;

final class AddingTeamMember
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        /**
         * The team instance.
         */
        public TeamContract $teamContract,
        /**
         * The team member being added.
         */
        public UserContract $userContract
<<<<<<< HEAD
    )
    {
=======
    ) {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    }
}
