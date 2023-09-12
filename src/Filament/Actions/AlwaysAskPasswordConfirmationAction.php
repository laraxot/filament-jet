<?php

namespace ArtMin96\FilamentJet\Filament\Actions;

use Filament\Forms\Components\TextInput;
<<<<<<< HEAD
use Filament\Forms;
=======
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
use Filament\Pages\Actions\Action;

final class AlwaysAskPasswordConfirmationAction extends Action
{
    protected function setUp(): void
    {
        $this->requiresConfirmation()
            ->modalHeading(__('filament-jet::jet.password_confirmation_modal.heading'))
            ->modalSubheading(
                __('filament-jet::jet.password_confirmation_modal.description')
            )
            ->form([
                TextInput::make('current_password')
                    ->label(__('filament-jet::jet.password_confirmation_modal.current_password'))
                    ->required()
                    ->password()
                    ->rule('current_password'),
            ]);
    }
}
