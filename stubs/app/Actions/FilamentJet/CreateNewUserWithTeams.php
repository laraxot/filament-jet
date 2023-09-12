<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Auth\Authenticatable;
use ArtMin96\FilamentJet\Contracts\CreatesNewUsers;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class CreateNewUser implements CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): UserContract
    {
<<<<<<< HEAD
        return DB::transaction(fn() => tap(FilamentJet::userModel()::create([
=======
        return DB::transaction(fn () => tap(FilamentJet::userModel()::create([
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
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
<<<<<<< HEAD
            
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            if (! $user instanceof Authenticatable) {
                throw new Exception('user must implements Authenticatable');
            }

            event(new Registered($user));

            if (Features::hasTeamFeatures()) {
                if (! $user instanceof UserContract) {
                    throw new Exception('strange things');
                }
<<<<<<< HEAD
                
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
                $this->createTeam($user);
            }

            return $user;
        }));
    }

    /**
     * Create a personal team for the user.
     */
<<<<<<< HEAD
    private function createTeam(UserContract $userContract): void
=======
    protected function createTeam(UserContract $userContract): void
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    {
        if (! method_exists($userContract, 'ownedTeams')) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }
        
        $teamClass = FilamentJet::teamModel();
        $userContract->ownedTeams()->save($teamClass::forceCreate([
            'user_id' => $userContract->getKey(),
<<<<<<< HEAD
            'name' => explode(' ', $userContract->name, 2)[0]."'s Team",
=======
            'name' => explode(' ', (string) $userContract->name, 2)[0]."'s Team",
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            'personal_team' => true,
        ]));
    }
}
