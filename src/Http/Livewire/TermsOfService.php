<?php

namespace ArtMin96\FilamentJet\Http\Livewire;

use ArtMin96\FilamentJet\FilamentJet;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class TermsOfService extends Component
{
    /**
     * Show the terms of service for the application.
     *
     * @return View
     */
    public function render()
    {
        $termsFile = FilamentJet::localizedMarkdownPath('terms.md');
        if (! is_string($termsFile)) {
            throw new Exception('strange things');
        }
        $fileContents = file_get_contents($termsFile);
        if ($fileContents === false) {
            throw new Exception('strange things');
        }

        $view = view('filament-jet::livewire.terms-of-service', [
            'terms' => Str::markdown($fileContents),
        ]);

        $view->layout('filament::components.layouts.base', [
            'title' => __('filament-jet::registration.terms_of_service'),
        ]);

        return $view;
    }
}
