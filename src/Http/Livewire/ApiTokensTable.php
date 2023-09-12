<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Http\Livewire;

use ArtMin96\FilamentJet\FilamentJet;
// use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasSanctumPermissionsProperty;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;
use Livewire\Component;

/**
 * Undocumented class.
 *
 * @property Collection $sanctumPermissions
 */
final class ApiTokensTable extends Component implements HasTable
{
    use InteractsWithTable;
    // ////use HasSanctumPermissionsProperty;

    /**
     * Undocumented variable.
     *
     * @var array
     */
    protected $listeners = [
        'tokenCreated' => '$refresh',
    ];

    public function render(): View
    {
        return view('filament-jet::livewire.api-tokens-table');
    }

    public function edit(Model $model, array $data): void
    {
        $model->forceFill([
            'abilities' => FilamentJet::validPermissions($data['abilities']),
        ])->save();

        Filament::notify('success', __('filament-jet::api.update.notify'));
    }

    public function delete(Model $model): void
    {
        $model->delete();

        Filament::notify('success', __('filament-jet::api.delete.notify'));
    }

    protected function getTableQuery(): Builder|Relation
    {
        return app(Sanctum::$personalAccessTokenModel)->where([
            ['tokenable_id', '=', Filament::auth()->id()],
            ['tokenable_type', '=', FilamentJet::userModel()],
        ])->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('filament-jet::api.fields.token_name'))
                ->searchable()
                ->sortable(),
            TagsColumn::make('abilities'),
            TextColumn::make('last_used_at')
                ->label(__('filament-jet::api.fields.last_used_at'))
                ->color('secondary')
                ->searchable()
                ->sortable()
                ->formatStateUsing(
                    static fn(?string $state): string => $state ? Carbon::parse($state)->diffForHumans() : __('filament-jet::api.table.never')
                ),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('delete')
                ->label(__('filament-jet::api.table.bulk_actions.delete'))
                ->action(static fn(Collection $records) => $records->each->delete())
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('permissions')
                ->action('edit')
                ->icon('heroicon-o-pencil-alt')
                ->modalWidth('sm')
                ->mountUsing(
                    static fn(ComponentContainer $componentContainer, Model $model): ComponentContainer => $componentContainer->fill($model->toArray())
                )
                ->form([
                    CheckboxList::make('abilities')
                        ->label(__('filament-jet::api.fields.permissions'))
                        ->options($this->sanctumPermissions)
                        ->columns(2)
                        ->required()
                        ->afterStateHydrated(static function ($component, $state) : void {
                            $permissions = FilamentJet::$permissions;
                            $tokenPermissions = collect($permissions)
                                ->filter(static fn($permission): bool => in_array($permission, $state))
                                ->values()
                                ->toArray();
                            $component->state($tokenPermissions);
                        }),
                ]),
            Action::make('delete')
                ->action('delete')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }
}
