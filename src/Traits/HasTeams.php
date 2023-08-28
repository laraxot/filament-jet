<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Traits;

use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Models\Team;
use ArtMin96\FilamentJet\OwnerRole;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * Undocumented trait.
 *
 * @property TeamContract $currentTeam
 */
trait HasTeams
{
    /**
     * Determine if the given team is the current team.
     */
    public function isCurrentTeam(TeamContract $team): bool
    {
        if ($team === null || $this->currentTeam === null) {
            return false;
        }

        return $team->id ===
            $this->currentTeam->id;
    }

    /**
     * Get the current team of the user's context.
     */
    public function currentTeam(): BelongsTo
    {
        if (is_null($this->current_team_id) && $this->id) {
            $this->switchTeam($this->personalTeam());
        }

        if ($this->allTeams()->count() === 0) {
            $this->current_team_id = null;
            $this->update();
        }

        return $this->belongsTo(FilamentJet::teamModel(), 'current_team_id');
    }

    /**
     * Switch the user's context to the given team.
     */
    public function switchTeam(?TeamContract $team): bool
    {
        if ($team === null) {
            return false;
        }
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        $this->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        $this->setRelation('currentTeam', $team);

        return true;
    }

    /**
     * Get all of the teams the user owns or belongs to.
     *
     * @return Collection<TeamContract>
     */
    public function allTeams(): Collection
    {
        //dov'è this->teams?
        return $this->ownedTeams->merge($this->teams)->sortBy('name');
    }

    /**
     * Get all of the teams the user owns.
     *
     * @return HasMany<Team>
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(FilamentJet::teamModel());
    }

    /**
     * Get all of the teams the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        $pivotClass = FilamentJet::membershipModel();
        $pivot = app($pivotClass);
        $pivotTable = $pivot->getTable();
        $pivotDbName = $pivot->getConnection()->getDatabaseName();
        $pivotTableFull = $pivotDbName.'.'.$pivotTable;
        //$this->setConnection('mysql');
        return $this->belongsToMany(FilamentJet::teamModel(), $pivotTableFull, null, 'team_id')
            ->using($pivotClass)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Get the user's "personal" team.
     */
    public function personalTeam(): ?TeamContract
    {
        $res = $this->ownedTeams->where('personal_team', true)->first();
        if ($res === null) {
            return null;
        }
        if (! $res instanceof TeamContract) {
            throw new Exception('strange things');
        }

        return $res;
    }

    /**
     * Determine if the user owns the given team.
     */
    public function ownsTeam(?TeamContract $team): bool
    {
        if (is_null($team)) {
            return false;
        }

        return $this->id === $team->{$this->getForeignKey()};
    }

    /**
     * Determine if the user belongs to the given team.
     */
    public function belongsToTeam(?TeamContract $team): bool
    {
        if (is_null($team)) {
            return false;
        }

        return $this->ownsTeam($team) || $this->teams->contains(function ($t) use ($team) {
            return $t->getKey() === $team->getKey();
        });
    }

    /**
     * Get the role that the user has on the team.
     *
     * @return \ArtMin96\FilamentJet\Role|null
     */
    public function teamRole(TeamContract $team)
    {
        if ($this->ownsTeam($team)) {
            return new OwnerRole;
        }

        if (! $this->belongsToTeam($team)) {
            return null;
        }

        $role = $team->users
            ->where('id', $this->id)
            ->first()
            ->membership
            ->role;

        return $role ? FilamentJet::findRole($role) : null;
    }

    /**
     * Determine if the user has the given role on the given team.
     */
    public function hasTeamRole(TeamContract $team, string $role): bool
    {
        if ($this->ownsTeam($team)) {
            return true;
        }

        return $this->belongsToTeam($team) && optional(FilamentJet::findRole($team->users->where(
            'id',
            $this->id
        )->first()?->membership?->role))->key === $role;
    }

    /**
     * Get the user's permissions for the given team.
     */
    public function teamPermissions(TeamContract $team): array
    {
        if ($this->ownsTeam($team)) {
            return ['*'];
        }

        if (! $this->belongsToTeam($team)) {
            return [];
        }

        return (array) optional($this->teamRole($team))->permissions;
    }

    /**
     * Determine if the user has the given permission on the given team.
     */
    public function hasTeamPermission(TeamContract $team, string $permission): bool
    {
        if ($this->ownsTeam($team)) {
            return true;
        }

        if (! $this->belongsToTeam($team)) {
            return false;
        }

        if (
            in_array(HasApiTokens::class, class_uses_recursive($this))
            && ! $this->tokenCan($permission)
            && $this->currentAccessToken() !== null
        ) {
            return false;
        }

        $permissions = $this->teamPermissions($team);

        return in_array($permission, $permissions)
            || in_array('*', $permissions)
            || (Str::endsWith($permission, ':create') && in_array('*:create', $permissions))
            || (Str::endsWith($permission, ':update') && in_array('*:update', $permissions));
    }
}