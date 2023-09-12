<?php

namespace ArtMin96\FilamentJet\Http\Livewire;

use Illuminate\Contracts\View\View;
use ArtMin96\FilamentJet\FilamentJet;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class PrivacyPolicy extends Component
{
    /**
     * Show the terms of service for the application.
     *
     * @return View
     */
    public function render()
    {
        $policyFile = FilamentJet::localizedMarkdownPath('policy.md');

        if ($policyFile === null) {
            throw new Exception('['.__LINE__.']['.class_basename(self::class).']');
        }

        if (! file_get_contents($policyFile)) {
            throw new Exception('['.__LINE__.']['.class_basename(self::class).']');
        }

        $view = view('filament-jet::livewire.privacy-policy', [
            'terms' => Str::markdown(file_get_contents($policyFile)),
        ]);

        $view->layout('filament::components.layouts.base', [
            'title' => __('filament-jet::registration.privacy_policy'),
        ]);

        return $view;
    }
}
