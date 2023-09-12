<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use ArtMin96\FilamentJet\Contracts\CreatesTeams;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasUserProperty;
use ArtMin96\FilamentJet\Traits\RedirectsActions;
use Exception;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Undocumented class
 *
 * @property UserContract $user
 * @property ComponentContainer $form
 */
final class CreateTeam extends Page
{
    use RedirectsActions;
    use HasUserProperty;

    protected static string $view = 'filament-jet::filament.pages.create-team';

    public array $createTeamState = [];

    protected static function shouldRegisterNavigation(): bool
    {
        if (! is_bool(config('filament-jet.should_register_navigation.create_team'))) {
            throw new Exception('['.__LINE__.']['.class_basename(self::class).']');
        }

        return config('filament-jet.should_register_navigation.create_team');
    }

    /**
     * Create a new team.
     *
     * @return RedirectResponse|Response|Redirector
     */
    public function createTeam(CreatesTeams $createsTeams)
    {
        $createsTeams->create($this->user, $this->createTeamState);

        Notification::make()
            ->title(__('filament-jet::teams/create.messages.created'))
            ->success()
            ->send();

        return $this->redirectPath($createsTeams);
    }

    protected function getForms(): array
    {
        return array_merge(
            parent::getForms(),
            [
                'createTeamForm' => $this->makeForm()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-jet::teams/create.fields.team_name'))
                            ->required()
                            ->maxLength(255),
                    ])
                    ->statePath('createTeamState'),
            ]
        );
    }
}
