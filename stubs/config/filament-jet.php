<?php

use ArtMin96\FilamentJet\Features;

return [

    'auth_middleware' => 'auth',

    /*
    |--------------------------------------------------------------------------
    | Username / Email
    |--------------------------------------------------------------------------
    |
    | This value defines which model attribute should be considered as your
    | application's "username" field. Typically, this might be the email
    | address of the users but you are free to change this value here.
    |
    | Out of the box, FilamentJet expects forgot password and reset password
    | requests to have a field named 'email'. If the application uses
    | another name for the field you may define it below as needed.
    |
    */

    'username' => 'email',

    'email' => 'email',

    /*
     |--------------------------------------------------------------------------
     | Route Group Prefix
     |--------------------------------------------------------------------------
     |
     | Set a route name prefix for all of FilamentJet's auth routes.
     | Ex. set filament. to prefix all route names, filament.register.
     | WARNING: if you use a custom route prefix, you'll need to override the
     | default auth routes used throughout your application.
     | This is outside of FilamentJet's scope and will be up to the dev to maintain.
     | Use at your own risk.
     | See example: https://laravel.com/docs/9.x/passwords#password-customization
     |
     */

    'route_group_prefix' => '',

    'profile' => [
        'login_field' => [
            'hint_action' => [
                'icon' => 'heroicon-o-question-mark-circle',
                'tooltip' => 'After changing the email address, confirmation is mandatory.',
            ],
        ],
    ],

    'should_register_navigation' => [
        'account' => false,
        'api_tokens' => false,
        'team_settings' => false,
        'create_team' => false,
    ],

    'user_menu' => [
        'account' => true,
        'api_tokens' => [
            'show' => true,
            'icon' => 'heroicon-o-key',
            'sort' => 1,
        ],
        'team_settings' => [
            'show' => true,
            'icon' => 'heroicon-o-cog',
            'sort' => 2,
        ],
        'create_team' => [
            'show' => true,
            'icon' => 'heroicon-o-users',
            'sort' => 3,
        ],

        'switchable_team' => [
            'show' => true,
            'icon' => '',
        ],
    ],

    'password_confirmation' => [
        'enable_two_factor_authentication' => true,
        'disable_two_factor_authentication' => true,
        'delete_account' => true,
        'download_personal_data' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | If you would like to specify a custom rate limiter to call
    | then you may specify it here.
    |
    */

    'limiters' => [
        'verification' => '6,1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Some of FilamentJet's features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features or you can even remove all of these if you need to.
    |
    */

    'features' => [
        Features::login([
            'card_width' => 'md',
            'has_brand' => true,
            'rate_limiting' => [
                'enabled' => true,
                'limit' => 5,
            ],
            'pipelines' => [],
        ]),
        Features::registration([
            'page' => \ArtMin96\FilamentJet\Filament\Pages\Auth\Register::class,
            'terms_of_service' => \ArtMin96\FilamentJet\Http\Livewire\TermsOfService::class,
            'privacy_policy' => \ArtMin96\FilamentJet\Http\Livewire\PrivacyPolicy::class,
            'card_width' => 'md',
            'has_brand' => true,
            'rate_limiting' => [
                'enabled' => true,
                'limit' => 5,
            ],
        ]),
        Features::resetPasswords([
            'request' => [
                'page' => \ArtMin96\FilamentJet\Filament\Pages\Auth\PasswordReset\RequestPasswordReset::class,
                'card_width' => 'md',
                'has_brand' => true,
                'rate_limiting' => [
                    'enabled' => true,
                    'limit' => 5,
                ],
            ],
            'reset' => [
                'page' => \ArtMin96\FilamentJet\Filament\Pages\Auth\PasswordReset\ResetPassword::class,
                'card_width' => 'md',
                'has_brand' => true,
                'rate_limiting' => [
                    'enabled' => true,
                    'limit' => 5,
                ],
            ],
        ]),
        // Features::emailVerification([
        //     'page' => \ArtMin96\FilamentJet\Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt::class,
        //     'controller' => \ArtMin96\FilamentJet\Http\Controllers\Auth\EmailVerificationController::class,
        //     'card_width' => 'md',
        //     'has_brand' => true,
        //     'rate_limiting' => [
        //         'enabled' => true,
        //         'limit' => 5
        //     ],
        // ]),
        Features::updateProfileInformation(),
        Features::updatePasswords([
            'askCurrentPassword' => true,
        ]),
        Features::twoFactorAuthentication([
            'authentication' => [
                'session_prefix' => 'filament.',
                'card_width' => 'md',
                'has_brand' => true,
                'rate_limiting' => [
                    'enabled' => true,
                    'limit' => 5,
                ],
            ],
            'confirm' => true,
            'toggleRecoveryCodesVisibilityWithConfirmPassword' => true,
            // 'window' => 0,
        ]),

        Features::termsAndPrivacyPolicy(),
        // Features::profilePhotos(),
        // Features::api(),
        // Features::teams([
        //     'invitations' => true,
        //     'middleware' => ['verified'],
        //     'invitation' => [
        //         'controller' => \ArtMin96\FilamentJet\Http\Controllers\TeamInvitationController::class,
        //         'actions' => [
        //             'accept' => 'accept',
        //             'destroy' => 'destroy',
        //         ],
        //     ],
        // ]),
        Features::logoutOtherBrowserSessions(),
        Features::accountDeletion(),

        /**
         * @see https://github.com/spatie/laravel-personal-data-export
         */
        Features::personalDataExport([

            /*
            | The name of the export itself can be set using the personalDataExportName on the user.
            | This will only affect the name of the download that will be sent as a response to the user,
            | not the name of the zip stored on disk.
            */

            'export-name' => 'personal-data',

            /*
            | The first parameter is the name of the file in the inside the zip file.
            | The second parameter is the content that should go in that file.
            | If you pass an array here, we will encode it to JSON.
            */

            'add' => [
                // ['nameInDownload' => '', 'content' => []]
            ],

            /*
            | The first parameter is a path to a file which will be copied to the zip.
            | You can also add a disk name as the second parameter and directory as the third parameter.
            */

            'add-files' => [
                // ['pathToFile' => '', 'diskName' => '', 'directory' => '']
            ],
        ]),
    ],

    /*
    |--------------------------------------------------------------------------
    | Profile Photo Disk
    |--------------------------------------------------------------------------
    |
    | This configuration value determines the default disk that will be used
    | when storing profile photos for your application's users. Typically
    | this will be the "public" disk but you may adjust this if needed.
    |
    */

    'profile_photo_disk' => 'public',

    'profile_photo_directory' => 'profile-photos',

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Seconds
    |--------------------------------------------------------------------------
    |
    | Number of seconds before asking the user to confirm their password in
    | PasswordConfirmationAction again. 300 = 5 minutes.
    |
    */

    'password_confirmation_seconds' => config('auth.password_timeout'),

    /*
    |--------------------------------------------------------------------------
    | Filament Jet Password Broker
    |--------------------------------------------------------------------------
    |
    | Here you may specify which password broker Fortify can use when a user
    | is resetting their password. This configured value should match one
    | of your password brokers setup in your "auth" configuration file.
    |
    */

    'passwords' => config('auth.defaults.passwords'),

    'models' => [
        'membership' => App\Models\Membership::class,
        'team' => App\Models\Team::class,
        'team_invitation' => App\Models\TeamInvitation::class,
    ],
];
