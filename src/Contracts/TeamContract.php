<?php

namespace ArtMin96\FilamentJet\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * ArtMin96\FilamentJet\Contracts\TeamContract
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $personal_team
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $role
 * @property-read UserContract|null $owner
 * @property-read EloquentCollection<int, TeamInvitationContract> $teamInvitations
 * @property-read int|null $team_invitations_count
 * @property-read EloquentCollection<int, UserContract> $users
 * @property-read int|null $users_count
 *
 * @method static Builder|Team newModelQuery()
 * @method static Builder|Team newQuery()
 * @method static Builder|Team query()
 * @method static Builder|Team whereCreatedAt($value)
 * @method static Builder|Team whereId($value)
 * @method static Builder|Team whereName($value)
 * @method static Builder|Team wherePersonalTeam($value)
 * @method static Builder|Team whereUpdatedAt($value)
 * @method static Builder|Team whereUserId($value)
 *
 * @mixin \Eloquent
 */
interface TeamContract extends ModelContract
{
    /**
     * Get the owner of the team.
     */
    public function owner(): BelongsTo;

    /**
     * Get all of the team's users including its owner.
     */
    public function allUsers(): Collection;

    /**
     * Get all of the users that belong to the team.
     */
    public function users(): BelongsToMany;

    /**
     * Determine if the given user belongs to the team.
     */
    public function hasUser(UserContract $userContract): bool;

    /**
     * Determine if the given email address belongs to a user on the team.
     */
    public function hasUserWithEmail(string $email): bool;

    /**
     * Determine if the given user has the given permission on the team.
     */
    public function userHasPermission(UserContract $userContract, string $permission): bool;

    /**
     * Get all of the pending user invitations for the team.
     */
    public function teamInvitations(): HasMany;

    /**
     * Remove the given user from the team.
     */
    public function removeUser(UserContract $userContract): void;

    /**
     * Purge all of the team's resources.
     */
    public function purge(): void;

    /* --non qui
     * Get the disk that profile photos should be stored on.

    public function profilePhotoDisk(): string;
    */
    /**
     * Get a fresh instance of the batch represented by this ID.
     *
     * @return self
     */
    public function fresh();
}
