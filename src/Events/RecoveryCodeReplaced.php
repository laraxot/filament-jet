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
    )
    {
    }
}
