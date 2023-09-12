<?php

namespace ArtMin96\FilamentJet;

use Exception;

final class RouteActions
{
    public function routePrefix(): string
    {
        $res = config('filament-jet.route_group_prefix');
        if (is_string($res)) {
            return $res;
        }
        
        throw new Exception('config filament-jet.route_group_prefix is not a string ');
    }

    public function loginRoute(): string
    {
        return route('filament.auth.login');
    }

    public function registrationRoute(): string
    {
        return route($this->routePrefix().'auth.register');
    }

    public function getRequestPasswordResetRoute(): string
    {
        return route($this->routePrefix().'auth.password-reset.request');
    }

    public function emailVerificationPromptRoute(): string
    {
        return $this->routePrefix().'auth.email-verification.prompt';
    }
}
