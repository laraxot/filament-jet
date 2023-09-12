<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Contracts;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Carbon;
use Laravel\Passport\Client;
use Laravel\Passport\Token;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * ArtMin96\FilamentJet\Contracts\UserContract.
 *
 * @property int                                                       $id
 * @property string                                                    $name
 * @property string                                                    $email
 * @property Carbon|null                                               $email_verified_at
 * @property string                                                    $password
 * @property string|null                                               $two_factor_secret
 * @property string|null                                               $two_factor_recovery_codes
 * @property string|null                                               $two_factor_confirmed_at
 * @property string|null                                               $remember_token
 * @property int|null                                                  $current_team_id
 * @property string|null                                               $profile_photo_path
 * @property Carbon|null                                               $created_at
 * @property Carbon|null                                               $updated_at
 * @property Collection<int, Client>                                   $clients
 * @property int|null                                                  $clients_count
 * @property string                                                    $profile_photo_url
 * @property DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property int|null                                                  $notifications_count
 * @property Collection<int, Permission>                               $permissions
 * @property int|null                                                  $permissions_count
 * @property Collection<int, Role>                                     $roles
 * @property int|null                                                  $roles_count
 * @property Collection<int, Token>                                    $tokens
 * @property int|null                                                  $tokens_count
 *
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereCurrentTeamId($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereProfilePhotoPath($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereTwoFactorConfirmedAt($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @method static Builder|User whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
interface UserContract extends CanResetPassword, HasProfilePhotoContract,
    // HasApiTokens, //no sanctum ma passport
    HasTeamsContract, ModelContract, MustVerifyEmail, PassportHasApiTokensContract, TwoFactorAuthenticatableContract {
}
