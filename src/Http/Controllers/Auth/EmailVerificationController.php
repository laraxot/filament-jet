<?php

namespace ArtMin96\FilamentJet\Http\Controllers\Auth;

use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\EmailVerificationResponse;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Routing\Controller;

final class EmailVerificationController extends Controller
{
    public function __invoke(): EmailVerificationResponse
    {
        /** @var MustVerifyEmail $user */
        $user = Filament::auth()->user();
        if ($user->hasVerifiedEmail()) {
            return app(EmailVerificationResponse::class);
        }
        if (!$user->markEmailAsVerified()) {
            return app(EmailVerificationResponse::class);
        }
        event(new Verified($user));
        return app(EmailVerificationResponse::class);
    }
}
