<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\CreatesNewUsers;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\Team;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): Model|Authenticatable
    {
        return DB::transaction(function () use ($input) {
            return tap(FilamentJet::userModel()::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function ($user) {

                if (Features::enabled(Features::emailVerification())) {
                    app()->bind(
                        \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
                        \ArtMin96\FilamentJet\Listeners\Auth\SendEmailVerificationNotification::class,
                    );
                }
                if (! $user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                    throw new Exception('user must implements Authenticatable');
                }

                event(new Registered($user));

                if (Features::hasTeamFeatures()) {
                    $this->createTeam($user);
                }

                return $user;
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(Model|Authenticatable $user): void
    {
        if (! method_exists($user, 'ownedTeams')) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }
        $teamClass = FilamentJet::teamModel();
        $user->ownedTeams()->save($teamClass::forceCreate([
            'user_id' => $user->getKey(),
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}