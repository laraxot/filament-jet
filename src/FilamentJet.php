<?php

namespace ArtMin96\FilamentJet;

use Modules\User\Models\User;
use Modules\User\Models\Team;
use Modules\User\Models\Membership;
use Modules\User\Models\TeamInvitation;
use ArtMin96\FilamentJet\Contracts\AddsTeamMembers;
use ArtMin96\FilamentJet\Contracts\CreatesNewUsers;
use ArtMin96\FilamentJet\Contracts\CreatesTeams;
use ArtMin96\FilamentJet\Contracts\DeletesTeams;
use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use ArtMin96\FilamentJet\Contracts\InvitesTeamMembers;
use ArtMin96\FilamentJet\Contracts\RemovesTeamMembers;
use ArtMin96\FilamentJet\Contracts\ResetsUserPasswords;
use ArtMin96\FilamentJet\Contracts\UpdatesTeamNames;
use ArtMin96\FilamentJet\Contracts\UpdatesUserPasswords;
use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Traits\HasTeams;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

final class FilamentJet
{
    /**
     * The callback that is responsible for building the authentication pipeline array, if applicable.
     *
     * @var callable|null
     */
    public static $authenticateThroughCallback;

    /**
     * The roles that are available to assign to users.
     *
     * @var array
     */
    public static $roles = [];

    /**
     * The permissions that exist within the application.
     *
     * @var array
     */
    public static $permissions = [];

    /**
     * The default permissions that should be available to new entities.
     *
     * @var array
     */
    public static $defaultPermissions = [];

    /**
     * The user model that should be used by FilamentJet.
     *
     * @var string
     */
    public static $userModel = User::class;

    /**
     * The team model that should be used by FilamentJet.
     *
     * @var string
     */
    public static $teamModel = Team::class;

    /**
     * The membership model that should be used by FilamentJet.
     *
     * @var string
     */
    public static $membershipModel = Membership::class;

    /**
     * The team invitation model that should be used by FilamentJet.
     *
     * @var string
     */
    public static $teamInvitationModel = TeamInvitation::class;

    /**
     * The password rules that should be used by FilamentJet.
     */
    public static array $passwordRules = [];

    /**
     * Register a callback that is responsible for building the authentication pipeline array.
     */
    public static function loginThrough(callable $callback): void
    {
        self::authenticateThrough($callback);
    }

    /**
     * Register a callback that is responsible for building the authentication pipeline array.
     */
    public static function authenticateThrough(callable $callback): void
    {
        self::$authenticateThroughCallback = $callback;
    }

    /**
     * Get the username used for authentication.
     *
     * @return string
     */
    public static function username()
    {
        return config('filament-jet.username', 'email');
    }

    /**
     * Get the name of the email address request variable / field.
     *
     * @return string
     */
    public static function email()
    {
        return config('filament-jet.email', 'email');
    }

    /**
     * Determine if FilamentJet has registered roles.
     */
    public static function hasRoles(): bool
    {
        return self::$roles !== [];
    }

    /**
     * Find the role with the given key.
     *
     * @return Role
     */
    public static function findRole(string $key)
    {
        return self::$roles[$key] ?? null;
    }

    /**
     * Define a role.
     *
     * @return Role
     */
    public static function role(string $key, string $name, array $permissions)
    {
        self::$permissions = collect(array_merge(self::$permissions, $permissions))
            ->unique()
            ->sort()
            ->values()
            ->all();

        return tap(new Role($key, $name, $permissions), static function ($role) use ($key) : void {
            self::$roles[$key] = $role;
        });
    }

    /**
     * Determine if any permissions have been registered with FilamentJet.
     */
    public static function hasPermissions(): bool
    {
        return self::$permissions !== [];
    }

    /**
     * Define the available API token permissions.
     */
    public static function permissions(array $permissions): self
    {
        self::$permissions = $permissions;

        return new self;
    }

    /**
     * Define the default permissions that should be available to new API tokens.
     */
    public static function defaultApiTokenPermissions(array $permissions): self
    {
        self::$defaultPermissions = $permissions;

        return new self;
    }

    /**
     * Return the permissions in the given list that are actually defined permissions for the application.
     */
    public static function validPermissions(array $permissions): array
    {
        return array_values(array_intersect($permissions, self::$permissions));
    }

    /**
     * Determine if FilamentJet is managing profile photos.
     *
     * @return bool
     */
    public static function managesProfilePhotos()
    {
        return Features::managesProfilePhotos();
    }

    /**
     * Determine if FilamentJet is supporting API features.
     *
     * @return bool
     */
    public static function hasApiFeatures()
    {
        return Features::hasApiFeatures();
    }

    /**
     * Determine if FilamentJet is supporting team features.
     */
    public static function hasTeamFeatures(): bool
    {
        return Features::hasTeamFeatures();
    }

    /**
     * Determine if a given user model utilizes the "HasTeams" trait.
     */
    public static function userHasTeamFeatures(UserContract $userContract): bool
    {
        return (array_key_exists(HasTeams::class, class_uses_recursive($userContract)) ||
            method_exists($userContract, 'currentTeam')) &&
            self::hasTeamFeatures();
    }

    /**
     * Determine if the application is using the terms confirmation feature.
     *
     * @return bool
     */
    public static function hasTermsAndPrivacyPolicyFeature()
    {
        return Features::hasTermsAndPrivacyPolicyFeature();
    }

    /**
     * Determine if the application is using any account deletion features.
     *
     * @return bool
     */
    public static function hasAccountDeletionFeatures()
    {
        return Features::hasAccountDeletionFeatures();
    }

    /**
     * Determine registration page.
     * non e' bool !
     *
     * @return mixed
     */
    public static function registrationPage()
    {
        return Features::getOption(Features::registration(), 'page');
    }

    /**
     * Determine email verification component.
     * non e' bool !
     *
     * @return mixed
     */
    public static function emailVerificationComponent()
    {
        return Features::getOption(Features::emailVerification(), 'page');
    }

    /**
     * Determine email verification controller.
     * non e' bool !
     *
     * @return mixed
     */
    public static function emailVerificationController()
    {
        return Features::getOption(Features::emailVerification(), 'controller');
    }

    /**
     * Determine terms of service component.
     * non e' bool !
     *
     * @return mixed
     */
    public static function termsOfServiceComponent()
    {
        return Features::getOption(Features::registration(), 'terms_of_service');
    }

    /**
     * Determine privacy policy component.
     * non e' bool !
     *
     * @return mixed
     */
    public static function privacyPolicyComponent()
    {
        return Features::getOption(Features::registration(), 'privacy_policy');
    }

    /**
     * Determine password reset component.
     * non e' bool !
     *
     * @return mixed
     */
    public static function resetPasswordPage()
    {
        return Features::getOption(Features::resetPasswords(), 'component');
    }

    /**
     * Determine team invitation controller.
     * non e' bool !
     *
     * @return mixed
     */
    public static function teamInvitationController()
    {
        return Features::getOption(Features::teams(), 'invitation.controller');
    }

    /**
     * Determine team invitation accept action.
     * non e' bool !
     *
     * @return mixed
     */
    public static function teamInvitationAcceptAction()
    {
        return Features::getOption(Features::teams(), 'invitation.actions.accept');
    }

    /**
     * Determine team invitation destroy action.
     * non e' bool !
     *
     * @return mixed
     */
    public static function teamInvitationDestroyAction()
    {
        return Features::getOption(Features::teams(), 'invitation.actions.destroy');
    }

    /**
     * Find a user instance by the given ID.
     *
     * @return UserContract
     */
    public static function findUserByIdOrFail(int $id)
    {
        $res = self::newUserModel()->where('id', $id)->firstOrFail();
        if (! $res instanceof UserContract) {
            throw new Exception('strange things');
        }

        return $res;
    }

    /**
     * Find a user instance by the given email address or fail.
     */
    public static function findUserByEmailOrFail(string $email): UserContract
    {
        $res = self::newUserModel()->where('email', $email)->firstOrFail();
        if (! $res instanceof UserContract) {
            throw new Exception('strange things');
        }

        return $res;
    }

    /**
     * Get the name of the user model used by the application.
     */
    public static function userModel(): string
    {
        return self::$userModel;
    }

    /**
     * Get a new instance of the user model.
     *
     * -return UserContract
     *
     * @return Model
     */
    public static function newUserModel()
    {
        $model = self::userModel();

        $res = new $model;
        if (! $res instanceof Model) {
            throw new Exception('wip');
        }

        return $res;
    }

    /**
     * Specify the user model that should be used by FilamentJet.
     */
    public static function useUserModel(string $model): self
    {
        self::$userModel = $model;

        return new self;
    }

    /**
     * Get the name of the team model used by the application.
     *
     * @return string
     */
    public static function teamModel()
    {
        return config('filament-jet.models.team');
    }

    /**
     * Get a new instance of the team model.
     *
     * @return Model
     */
    public static function newTeamModel()
    {
        $model = self::teamModel();

        $res = new $model;
        if (! $res instanceof Model) {
            throw new Exception('wip');
        }

        return $res;
    }

    /**
     * Specify the team model that should be used by FilamentJet.
     */
    public static function useTeamModel(string $model): self
    {
        self::$teamModel = $model;

        return new self;
    }

    /**
     * Get the name of the membership model used by the application.
     *
     * @return string
     */
    public static function membershipModel()
    {
        return self::$membershipModel;
    }

    /**
     * Specify the membership model that should be used by FilamentJet.
     */
    public static function useMembershipModel(string $model): self
    {
        self::$membershipModel = $model;

        return new self;
    }

    /**
     * Get the name of the team invitation model used by the application.
     *
     * @return string
     */
    public static function teamInvitationModel()
    {
        return config('filament-jet.models.team_invitation');
    }

    /**
     * Specify the team invitation model that should be used by FilamentJet.
     */
    public static function useTeamInvitationModel(string $model): self
    {
        self::$teamInvitationModel = $model;

        return new self;
    }

    /**
     * Register a class / callback that should be used to update user profile information.
     */
    public static function updateUserProfileInformationUsing(string $class): void
    {
        app()->singleton(UpdatesUserProfileInformation::class, $class);
    }

    /**
     * Register a class / callback that should be used to update user passwords.
     */
    public static function updateUserPasswordsUsing(string $class): void
    {
        app()->singleton(UpdatesUserPasswords::class, $class);
    }

    /**
     * Register a class / callback that should be used to create users.
     */
    public static function createUsersUsing(string $class): void
    {
        app()->singleton(CreatesNewUsers::class, $class);
    }

    /**
     * Register a class / callback that should be used to create teams.
     */
    public static function createTeamsUsing(string $class): void
    {
        app()->singleton(CreatesTeams::class, $class);
    }

    /**
     * Register a class / callback that should be used to update team names.
     */
    public static function updateTeamNamesUsing(string $class): void
    {
        app()->singleton(UpdatesTeamNames::class, $class);
    }

    /**
     * Register a class / callback that should be used to add team members.
     */
    public static function addTeamMembersUsing(string $class): void
    {
        app()->singleton(AddsTeamMembers::class, $class);
    }

    /**
     * Register a class / callback that should be used to add team members.
     */
    public static function inviteTeamMembersUsing(string $class): void
    {
        app()->singleton(InvitesTeamMembers::class, $class);
    }

    /**
     * Register a class / callback that should be used to remove team members.
     */
    public static function removeTeamMembersUsing(string $class): void
    {
        app()->singleton(RemovesTeamMembers::class, $class);
    }

    /**
     * Register a class / callback that should be used to delete teams.
     */
    public static function deleteTeamsUsing(string $class): void
    {
        app()->singleton(DeletesTeams::class, $class);
    }

    /**
     * Register a class / callback that should be used to delete users.
     */
    public static function deleteUsersUsing(string $class): void
    {
        app()->singleton(DeletesUsers::class, $class);
    }

    /**
     * Register a class / callback that should be used to reset user passwords.
     */
    public static function resetUserPasswordsUsing(string $class): void
    {
        app()->singleton(ResetsUserPasswords::class, $class);
    }

    public static function getVerifyEmailUrl(UserContract $userContract): string
    {
        /**
         * @var int $expire
         */
        $expire = config('auth.verification.expire', 60);

        return URL::temporarySignedRoute(
            config('filament-jet.route_group_prefix').'auth.email-verification.verify',
            now()->addMinutes($expire),
            [
                'id' => $userContract->getKey(),
                'hash' => sha1($userContract->getEmailForVerification()),
            ],
        );
    }

    public static function getResetPasswordUrl(string $token, UserContract $userContract): string
    {
        return URL::signedRoute(config('filament-jet.route_group_prefix').'auth.password-reset.reset', [
            'email' => $userContract->getEmailForPasswordReset(),
            'token' => $token,
        ]);
    }

    public static function setPasswordRules(array $rules): void
    {
        self::$passwordRules = $rules !== [] ? $rules : (array) Password::default();
    }

    public static function getPasswordRules(): array
    {
        return self::$passwordRules;
    }

    /**
     * Determine if FilamentJet is confirming two factor authentication configurations.
     */
    public static function confirmsTwoFactorAuthentication(): bool
    {
        return Features::enabled(Features::twoFactorAuthentication()) &&
            Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
    }

    /**
     * Find the path to a localized Markdown resource.
     *
     * @return string|null
     */
    public static function localizedMarkdownPath(string $name)
    {
        $localName = preg_replace('#(\.md)$#i', '.'.app()->getLocale().'$1', $name);

        return Arr::first([
            resource_path('markdown/'.$localName),
            resource_path('markdown/'.$name),
        ], static fn($path): bool => file_exists($path));
    }
}
