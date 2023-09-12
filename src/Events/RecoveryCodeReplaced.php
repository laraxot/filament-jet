<?php

namespace ArtMin96\FilamentJet\Events;

use ArtMin96\FilamentJet\Contracts\UserContract;
use Illuminate\Queue\SerializesModels;

final class RecoveryCodeReplaced
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        /**
         * The authenticated user.
         */
        public UserContract $userContract,
        /**
         * The recovery code.
         */
        public string $code
<<<<<<< HEAD
    )
    {
=======
    ) {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    }
}
