<?php

namespace ArtMin96\FilamentJet\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;
<<<<<<< HEAD
use Laravel\Passport\Token;
use Laravel\Passport\TransientToken;
use Laravel\Passport\PersonalAccessTokenResult;
=======
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Passport\Token;
use Laravel\Passport\TransientToken;

>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
/**
 * @propery \Laravel\Passport\Token|\Laravel\Passport\TransientToken|null $accessToken;
 */
interface PassportHasApiTokensContract
{
    /**
     * Get all of the user's registered OAuth clients.
     *
     * @return HasMany
     */
    public function clients();

    /**
     * Get all of the access tokens for the user.
     *
     * @return HasMany
     */
    public function tokens();

    /**
     * Get the current access token being used by the user.
     *
     * @return Token|TransientToken|null
     */
    public function token();

    /**
     * Determine if the current API token has a given scope.
     *
     * @param  string  $scope
     * @return bool
     */
    public function tokenCan($scope);

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @return PersonalAccessTokenResult
     */
    public function createToken($name, array $scopes = []);

    /**
     * Set the current access token for the user.
     *
<<<<<<< HEAD
     * @param Token|TransientToken $accessToken
=======
     * @param  Token|TransientToken  $accessToken
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
     * @return $this
     */
    public function withAccessToken($accessToken);
}
