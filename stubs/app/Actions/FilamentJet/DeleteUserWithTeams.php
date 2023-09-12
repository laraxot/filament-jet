<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\DeletesTeams;
use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use ArtMin96\FilamentJet\Contracts\TeamContract;
// use ArtMin96\FilamentJet\Contracts\UserContract;;
// use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use Exception;
use Illuminate\Support\Facades\DB;

final class DeleteUser implements DeletesUsers
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        /**
         * The team deleter implementation.
         */
        private readonly DeletesTeams $deletesTeams
    )
    {
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
            
            if (! method_exists($userContract, 'delete')) {
                throw new Exception('['.__LINE__.']['.__FILE__.']');
            }
            
            $this->deleteTeams($userContract);
            if (! method_exists($userContract, 'deleteProfilePhoto')) {
                throw new Exception('method deleteProfilePhoto is missing on user');
            }
            
            $userContract->deleteProfilePhoto();
            $userContract->tokens->each->delete();
            $userContract->delete();
        });
    }

    /**
     * Delete the teams and team associations attached to the user.
     */
    private function deleteTeams(UserContract $userContract): void
    {
        if (! method_exists($userContract, 'teams')) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }
        
        $userContract->teams()->detach();

        $userContract->ownedTeams->each(function (TeamContract $teamContract): void {
            $this->deletesTeams->delete($teamContract);
        });
    }
}
