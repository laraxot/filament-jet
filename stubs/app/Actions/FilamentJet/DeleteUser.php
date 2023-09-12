<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use ArtMin96\FilamentJet\Contracts\UserContract;
use Exception;

final class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(UserContract $userContract): void
    {
        if (! method_exists($userContract, 'deleteProfilePhoto')) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }
        
        if (! method_exists($userContract, 'delete')) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }
        
        $userContract->deleteProfilePhoto();
        $userContract->tokens->each->delete();
        $userContract->delete();
    }
}
