<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\DeletesTeams;
use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use Exception;
use Illuminate\Support\Facades\DB;

final class DeleteUserWithTeams implements DeletesUsers
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        /**
         * The team deleter implementation.
         */
<<<<<<< HEAD
        private readonly DeletesTeams $deletesTeams
    )
    {
=======
        protected DeletesTeams $deletesTeams
    ) {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    }

    /**
     * Delete the given user.
     */
    public function delete(UserContract $userContract): void
    {
        DB::transaction(function () use ($userContract) {
            if (! method_exists($userContract, 'deleteProfilePhoto')) {
                throw new Exception('['.__LINE__.']['.__FILE__.']');
            }
<<<<<<< HEAD
            
            if (! method_exists($userContract, 'delete')) {
                throw new Exception('['.__LINE__.']['.__FILE__.']');
            }
            
=======
            if (! method_exists($userContract, 'delete')) {
                throw new Exception('['.__LINE__.']['.__FILE__.']');
            }
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $this->deleteTeams($userContract);
            if (! method_exists($userContract, 'deleteProfilePhoto')) {
                throw new Exception('method deleteProfilePhoto is missing on user');
            }
<<<<<<< HEAD
            
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $userContract->deleteProfilePhoto();
            $userContract->tokens->each->delete();
            $userContract->delete();
        });
    }

    /**
     * Delete the teams and team associations attached to the user.
     */
<<<<<<< HEAD
    private function deleteTeams(UserContract $userContract): void
=======
    protected function deleteTeams(UserContract $userContract): void
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
    {
        if (! method_exists($userContract, 'teams')) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }
<<<<<<< HEAD
        
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
        $userContract->teams()->detach();

        $userContract->ownedTeams->each(function (TeamContract $teamContract): void {
            $this->deletesTeams->delete($teamContract);
        });
    }
}
