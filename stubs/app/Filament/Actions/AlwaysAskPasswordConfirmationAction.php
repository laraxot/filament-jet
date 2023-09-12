<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Filament\Actions;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;

class AlwaysAskPasswordConfirmationAction extends ButtonAction {
    protected function setUp(): void {
        $this->requiresConfirmation()
            ->modalHeading(__('filament-jet::account.account_page.password_confirmation_modal.heading'))
            ->modalSubheading(
                __('filament-jet::account.account_page.password_confirmation_modal.description')
            )
            ->form([
                TextInput::make('current_password')
                    ->label(__('filament-jet::account.account_page.password_confirmation_modal.current_password'))
                    ->required()
                    ->password()
                    ->rule('current_password'),
            ]);
    }
}
