<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\CreatesNewUsers;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateNewUser implements CreatesNewUsers {
    /**
     * Create a newly registered user.
     *
     * @param array<string, string> $input
     */
    public function create(array $input): UserContract {
        return DB::transaction(fn () => tap(FilamentJet::userModel()::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]), function ($user) {
            if (Features::enabled(Features::emailVerification())) {
                app()->bind(
                    SendEmailVerificationNotification::class,
                    \ArtMin96\FilamentJet\Listeners\Auth\SendEmailVerificationNotification::class,
                );
            }
            if (! $user instanceof Authenticatable) {
                throw new \Exception('user must implements Authenticatable');
            }

            event(new Registered($user));

            if (Features::hasTeamFeatures()) {
                if (! $user instanceof UserContract) {
                    throw new \Exception('strange things');
                }
                $this->createTeam($user);
            }

            return $user;
        }));
    }

    /**
     * Create a personal team for the user.
     */
    private function createTeam(UserContract $userContract): void {
        if (! method_exists($userContract, 'ownedTeams')) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }

        $teamClass = FilamentJet::teamModel();
        $userContract->ownedTeams()->save($teamClass::forceCreate([
            'user_id' => $userContract->getKey(),
            'name' => explode(' ', (string) $userContract->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
