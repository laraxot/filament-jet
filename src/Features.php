<?php

namespace ArtMin96\FilamentJet;

class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, (array) config('filament-jet.features', []));
    }

    /**
     * Determine if the feature is enabled and has a given option enabled.
     *
     * @return bool
     */
    public static function optionEnabled(string $feature, string $option)
    {
        return static::enabled($feature) &&
            config("filament-jet-options.{$feature}.{$option}") === true;
    }

    /**
     * Determine if the feature is enabled and has a given option.
     *
     * @return mixed
     */
    public static function getOption(string $feature, string $option)
    {
        return static::enabled($feature) && config("filament-jet-options.{$feature}.{$option}")
            ? config("filament-jet-options.{$feature}.{$option}")
            : null;
    }

    /**
     * Determine if the application has registration features.
     */
    public static function hasRegistrationFeature(): bool
    {
        return static::enabled(static::registration());
    }

    /**
     * Determine if the application has reset password features.
     */
    public static function hasResetPasswordFeature(): bool
    {
        return static::enabled(static::resetPasswords());
    }

    /**
     * Determine if the application is using any features that require "profile" management.
     *
     * @return bool
     */
    public static function hasProfileFeatures()
    {
        return static::enabled(static::updateProfileInformation()) ||
            static::enabled(static::updatePasswords()) ||
            static::enabled(static::twoFactorAuthentication());
    }

    /**
     * Determine if the application can update a user's profile information.
     *
     * @return bool
     */
    public static function canUpdateProfileInformation()
    {
        return static::enabled(static::updateProfileInformation());
    }

    /**
     * Determine if the application is using any security profile features.
     *
     * @return bool
     */
    public static function hasSecurityFeatures()
    {
        return static::enabled(static::updatePasswords()) ||
            static::canManageTwoFactorAuthentication();
    }

    /**
     * Determine if the application can manage two factor authentication.
     *
     * @return bool
     */
    public static function canManageTwoFactorAuthentication()
    {
        return static::enabled(static::twoFactorAuthentication());
    }

    /**
     * Determine if the application is allowing profile photo uploads.
     *
     * @return bool
     */
    public static function managesProfilePhotos()
    {
        return static::enabled(static::profilePhotos());
    }

    /**
     * Determine if the application is using any API features.
     *
     * @return bool
     */
    public static function hasApiFeatures()
    {
        return static::enabled(static::api());
    }

    /**
     * Determine if the application is using any team features.
     *
     * @return bool
     */
    public static function hasTeamFeatures()
    {
        return static::enabled(static::teams());
    }

    /**
     * Determine if invitations are sent to team members.
     *
     * @return bool
     */
    public static function sendsTeamInvitations()
    {
        return static::optionEnabled(static::teams(), 'invitations');
    }

    /**
     * Determine if the application has terms of service / privacy policy confirmation enabled.
     *
     * @return bool
     */
    public static function hasTermsAndPrivacyPolicyFeature()
    {
        return static::enabled(static::termsAndPrivacyPolicy());
    }

    /**
     * Determine if the application is using any account deletion features.
     *
     * @return bool
     */
    public static function hasAccountDeletionFeatures()
    {
        return static::enabled(static::accountDeletion());
    }

    /**
     * Get login feature options.
     *
     * @return string
     */
    public static function login(array $options = [])
    {
        if (! empty($options)) {
            config(['filament-jet-options.login' => $options]);
        }

        return 'login';
    }

    /**
     * Enable the registration feature.
     *
     * @return string
     */
    public static function registration(array $options = [])
    {
        if (! empty($options)) {
            config(['filament-jet-options.registration' => $options]);
        }

        return 'registration';
    }

    /**
     * Enable the two factor authentication feature.
     *
     * @return string
     */
    public static function twoFactorAuthentication(array $options = [])
    {
        if (! empty($options)) {
            config(['filament-jet-options.two-factor-authentication' => $options]);
        }

        return 'two-factor-authentication';
    }

    /**
     * Determine if the application can logout other browser sessions.
     */
    public static function canLogoutOtherBrowserSessions(): bool
    {
        return static::enabled(static::logoutOtherBrowserSessions());
    }

    /**
     * Determine if the application can export personal data.
     */
    public static function canExportPersonalData(): bool
    {
        return static::enabled(static::personalDataExport());
    }

    /**
     * Enable the password reset feature.
     *
     * @return string
     */
    public static function resetPasswords(array $options = [])
    {
        if (! empty($options)) {
            config(['filament-jet-options.reset-passwords' => $options]);
        }

        return 'reset-passwords';
    }

    /**
     * Enable the email verification feature.
     *
     * @return string
     */
    public static function emailVerification(array $options = [])
    {
        if (! empty($options)) {
            config(['filament-jet-options.email-verification' => $options]);
        }

        return 'email-verification';
    }

    /**
     * Enable the update profile information feature.
     *
     * @return string
     */
    public static function updateProfileInformation()
    {
        return 'update-profile-information';
    }

    /**
     * Enable the update password feature.
     *
     * @return string
     */
    public static function updatePasswords(array $options = [])
    {
        if (! empty($options)) {
            config(['filament-jet-options.update-passwords' => $options]);
        }

        return 'update-passwords';
    }

    /**
     * Enable the profile photo upload feature.
     *
     * @return string
     */
    public static function profilePhotos()
    {
        return 'profile-photos';
    }

    /**
     * Enable the API feature.
     *
     * @return string
     */
    public static function api()
    {
        return 'api';
    }

    /**
     * Enable the teams feature.
     *
     * @return string
     */
    public static function teams(array $options = [])
    {
        if (! empty($options)) {
            config(['filament-jet-options.teams' => $options]);
        }

        return 'teams';
    }

    /**
     * Enable the terms of service and privacy policy feature.
     *
     * @return string
     */
    public static function termsAndPrivacyPolicy()
    {
        return 'terms';
    }

    /**
     * Enable the logout other browser sessions feature.
     */
    public static function logoutOtherBrowserSessions(): string
    {
        return 'logout-other-browser-sessions';
    }

    /**
     * Enable the account deletion feature.
     *
     * @return string
     */
    public static function accountDeletion()
    {
        return 'account-deletion';
    }

    /**
     * Enable the account personal data export feature.
     *
     * @return string
     */
    public static function personalDataExport(array $options = [])
    {
        if (! empty($options)) {
            config(['filament-jet-options.personal-data-export' => $options]);
        }

        return 'personal-data-export';
    }
}
