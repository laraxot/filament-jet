<?php

<<<<<<< HEAD
namespace ArtMin96\FilamentJet\Actions;
=======
declare(strict_types=1);

namespace App\Actions\FilamentJet;
>>>>>>> 89797fce (.)

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
use Modules\User\Models\User;
>>>>>>> 39fcb522 (rebase)
=======
>>>>>>> 354a30e7 (Fix styling)
=======
=======
use Modules\User\Models\User;
>>>>>>> 798d2d5 (.)
>>>>>>> 5be9ebe5 (rebase)
=======
=======
use Modules\User\Models\User;
>>>>>>> 798d2d5 (.)
=======
>>>>>>> 88c140b (Fix styling)
>>>>>>> e618ae9f (rebase)
use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use ArtMin96\FilamentJet\Contracts\UserContract;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(UserContract $user): void
    {
        if (! method_exists($user, 'deleteProfilePhoto')) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }
        if (! method_exists($user, 'delete')) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();
    }
}
