<?php

namespace ArtMin96\FilamentJet\Http\Livewire;

use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Support\Str;
use Livewire\Component;

class PrivacyPolicy extends Component
{
    /**
     * Show the terms of service for the application.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $policyFile = FilamentJet::localizedMarkdownPath('policy.md');

        if ($policyFile == null) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        if (! file_get_contents($policyFile)) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
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
