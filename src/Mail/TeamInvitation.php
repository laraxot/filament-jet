<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Mail;

use ArtMin96\FilamentJet\Contracts\TeamInvitationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;

class TeamInvitation extends Mailable {
    use Queueable;

    public function __construct(
        /**
         * The team invitation instance.
         */
        public TeamInvitationContract $teamInvitationContract
    ) {
    }

    /**
     * Build the message.
     */
    public function build(): static {
        return $this->markdown('filament-jet::mail.team-invitation', ['acceptUrl' => URL::signedRoute('team-invitations.accept', [
            'invitation' => $this->teamInvitationContract,
        ]),
        ])->subject(__('filament-jet::teams/invitation-mail.subject'));
    }
}
