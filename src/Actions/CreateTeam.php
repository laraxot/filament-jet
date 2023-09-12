<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\CreatesTeams;
use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Events\AddingTeam;
use ArtMin96\FilamentJet\FilamentJet;
use Exception;
use Illuminate\Support\Facades\Gate;

final class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  array<string, string>  $input
     */
    public function create(UserContract $userContract, array $input): TeamContract
    {
        Gate::forUser($userContract)->authorize('create', FilamentJet::newTeamModel());

        AddingTeam::dispatch($userContract);

        if (! method_exists($userContract, 'ownedTeams')) {
            throw new Exception('['.__LINE__.']['.class_basename(self::class).']');
        }

        if (! method_exists($userContract, 'switchTeam')) {
            throw new Exception('['.__LINE__.']['.class_basename(self::class).']');
        }
        
        $model = $userContract->ownedTeams()->create([
            'name' => $input['name'],
            'personal_team' => false,
        ]);
        if (! $model instanceof TeamContract) {
            throw new Exception('team not have TeamContract');
        }
        
        $userContract->switchTeam($model);

        return $model;
    }
}
