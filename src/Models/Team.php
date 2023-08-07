<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Models;

use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\TeamInvitationContract;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * ArtMin96\FilamentJet\Models\Team.
 *
 * @property int                                             $id
 * @property int                                             $user_id
 * @property string                                          $name
 * @property int                                             $personal_team
 * @property Carbon|null                                     $created_at
 * @property Carbon|null                                     $updated_at
 * @property UserContract|null                               $owner
 * @property EloquentCollection<int, TeamInvitationContract> $teamInvitations
 * @property int|null                                        $team_invitations_count
 * @property EloquentCollection<int, UserContract>           $users
 * @property int|null                                        $users_count
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
abstract class Team extends Model implements TeamContract
{
    /**
     * Get the owner of the team.
     *
     * @return BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(FilamentJet::userModel(), 'user_id');
    }

    /**
     * Get all of the team's users including its owner.
     *
     * @return Collection
     */
    public function allUsers()
    {
        return $this->users->merge([$this->owner]);
    }

    /**
     * Get all of the users that belong to the team.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(FilamentJet::userModel(), FilamentJet::membershipModel())
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Determine if the given user belongs to the team.
     *
     * @param UserContract $user
     *
     * @return bool
     */
    public function hasUser($user)
    {
        return $this->users->contains($user) || $user->ownsTeam($this);
    }

    /**
     * Determine if the given email address belongs to a user on the team.
     *
     * @return bool
     */
    public function hasUserWithEmail(string $email)
    {
        return $this->allUsers()->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Determine if the given user has the given permission on the team.
     *
     * @param UserContract $user
     * @param string       $permission
     *
     * @return bool
     */
    public function userHasPermission($user, $permission)
    {
        return $user->hasTeamPermission($this, $permission);
    }

    /**
     * Get all of the pending user invitations for the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamInvitations()
    {
        return $this->hasMany(FilamentJet::teamInvitationModel());
    }

    /**
     * Remove the given user from the team.
     *
     * @param UserContract $user
     *
     * @return void
     */
    public function removeUser($user)
    {
        if ($user->current_team_id === $this->id) {
            $user->forceFill([
                'current_team_id' => null,
            ])->save();
        }

        $this->users()->detach($user);
    }

    /**
     * Purge all of the team's resources.
     *
     * @return void
     */
    public function purge()
    {
        $this->owner()->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->users()->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->users()->detach();

        $this->delete();
    }
}
