<?php

<<<<<<< HEAD
namespace ArtMin96\FilamentJet\Actions;
=======
declare(strict_types=1);

namespace App\Actions\FilamentJet;
>>>>>>> 89797fce (.)

use ArtMin96\FilamentJet\Contracts\CreatesTeams;
use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\AddingTeam;
use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Support\Facades\Gate;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  array<string, string>  $input
     */
<<<<<<< HEAD
    public function create(UserContract $user, array $input): TeamContract
=======
    public function create(User $user, array $input): Team
>>>>>>> 89797fce (.)
    {
        Gate::forUser($user)->authorize('create', FilamentJet::newTeamModel());

        AddingTeam::dispatch($user);

        if (! method_exists($user, 'ownedTeams')) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        if (! method_exists($user, 'switchTeam')) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        $team = $user->ownedTeams()->create([
            'name' => $input['name'],
            'personal_team' => false,
        ]);
        if (! $team instanceof TeamContract) {
            throw new \Exception('team not have TeamContract');
        }
        $user->switchTeam($team);

        return $team;
    }
}
