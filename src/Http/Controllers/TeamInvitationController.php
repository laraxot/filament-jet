<?php

namespace ArtMin96\FilamentJet\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use ArtMin96\FilamentJet\Contracts\AddsTeamMembers;
use ArtMin96\FilamentJet\Contracts\TeamInvitationContract;
use ArtMin96\FilamentJet\Datas\FilamentData;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Gate;

final class TeamInvitationController extends Controller
{
    /**
     * Accept a team invitation.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function accept(Request $request, TeamInvitationContract $teamInvitationContract)
    {
        if (! Features::hasTeamFeatures()) {
            $teamInvitationContract->delete();

            Notification::make()
                ->title(__('filament-jet::teams/invitations.messages.feature_disabled'))
                ->success()
                ->send();
            $filamentPath = config('filament.path');
            if (! is_string($filamentPath)) {
                throw new Exception('strange things');
            }

            return redirect($filamentPath);
        }

        app(AddsTeamMembers::class)->add(
            $teamInvitationContract->team->owner,
            $teamInvitationContract->team,
            $teamInvitationContract->email,
            $teamInvitationContract->role
        );

        $teamInvitationContract->delete();

        $userContract = FilamentJet::findUserByEmailOrFail($teamInvitationContract->email);

        if (! $userContract->switchTeam($teamInvitationContract->team)) {
            abort(403);
        }

        Notification::make()
            ->title(__('filament-jet::teams/invitations.messages.invited', ['team' => $teamInvitationContract->team->name]))
            ->success()
            ->send();
        $filamentData = FilamentData::make();

        return redirect($filamentData->path);
    }

    /**
     * Cancel the given team invitation.
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Request $request, TeamInvitationContract $teamInvitationContract)
    {
        if (! Gate::forUser($request->user())->check('removeTeamMember', $teamInvitationContract->team)) {
            throw new AuthorizationException;
        }

        $teamInvitationContract->delete();

        return back(303);
    }
}
