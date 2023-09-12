<?php

use ArtMin96\FilamentJet\Filament\Pages\Auth\TwoFactorLogin;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\Auth\TwoFactorLogin;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Route;

/**
 * @var string $domain
 */
$domain = config('filament.domain');

/**
 * @var array<int,string> $middlewares
 */
$middlewares = config('filament.middleware.base');

/**
 * @var string $name
 */
$name = config('filament-jet.route_group_prefix');

/**
 * @var string $prefix
 */
$prefix = config('filament.path');

Route::domain($domain)
    ->middleware($middlewares)
    ->name($name)
    ->prefix($prefix)
<<<<<<< HEAD
    ->group(static function () : void {
=======
    ->group(function (): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
        $guard = config('filament.auth.guard');
        $authMiddleware = config('filament-jet.auth_middleware', 'auth');
        Route::name('auth.')
            ->middleware(['guest:'.$guard])
<<<<<<< HEAD
            ->group(static function () : void {
=======
            ->group(function (): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
                // Two Factor Authentication...
                if (Features::enabled(Features::twoFactorAuthentication())) {
                    Route::get('two-factor-login', TwoFactorLogin::class)->name('two-factor.login');
                }
                // Registration...
                if (Features::hasRegistrationFeature()) {
                    Route::get('register', FilamentJet::registrationPage())->name('register');
                }
                // Password Reset...
                if (Features::hasResetPasswordFeature()) {
                    Route::name('password-reset.')
                        ->prefix('/password-reset')
<<<<<<< HEAD
                        ->group(static function () : void {
=======
                        ->group(function (): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
                            Route::get('request', Features::getOption(Features::resetPasswords(), 'request.page'))->name('request');
                            Route::get('reset', Features::getOption(Features::resetPasswords(), 'reset.page'))
                                ->middleware(['signed'])
                                ->name('reset');
                        });
                }
            });
<<<<<<< HEAD
=======

>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
        if (Features::enabled(Features::registration()) && FilamentJet::hasTermsAndPrivacyPolicyFeature()) {
            Route::get('terms-of-service', FilamentJet::termsOfServiceComponent())->name('terms');
            Route::get('privacy-policy', FilamentJet::privacyPolicyComponent())->name('policy');
        }
        // Teams...
        if (Features::hasTeamFeatures()) {
            Route::middleware(
                Features::getOption(Features::teams(), 'middleware') ?? []
<<<<<<< HEAD
            )->group(static function () : void {
=======
            )->group(function (): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
                Route::get('team-invitations/{invitation}', [FilamentJet::teamInvitationController(), FilamentJet::teamInvitationAcceptAction()])
                    ->middleware(['signed'])
                    ->name('team-invitations.accept');
            });
        }
        // Email verification...
        if (Features::enabled(Features::emailVerification())) {
            Route::name('auth.email-verification.')
                ->prefix('/email-verification')
<<<<<<< HEAD
                ->group(static function () : void {
=======
                ->group(function (): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
                    Route::get('prompt', Features::getOption(Features::emailVerification(), 'page'))->name('prompt');
                    Route::get('verify', [
                        Features::getOption(Features::emailVerification(), 'controller') ?? EmailVerificationController::class,
                        '__invoke',
                    ])
                        ->name('verify');
                });
        }
    });
