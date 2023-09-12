<?php

namespace ArtMin96\FilamentJet\Events;

use ArtMin96\FilamentJet\Contracts\TeamContract;
use Illuminate\Foundation\Events\Dispatchable;

final class InvitingTeamMember
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
        public mixed $email,
        public mixed $role
<<<<<<< HEAD
    )
    {
=======
    ) {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    }
}
