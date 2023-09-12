<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Events;

use ArtMin96\FilamentJet\Contracts\UserContract;
use Illuminate\Foundation\Events\Dispatchable;

abstract class TwoFactorAuthenticationEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        /**
         * The user instance.
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
