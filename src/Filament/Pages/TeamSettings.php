<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use ArtMin96\FilamentJet\Actions\UpdateTeamMemberRole;
use ArtMin96\FilamentJet\Actions\ValidateTeamDeletion;
use ArtMin96\FilamentJet\Contracts\AddsTeamMembers;
use ArtMin96\FilamentJet\Contracts\DeletesTeams;
use ArtMin96\FilamentJet\Contracts\InvitesTeamMembers;
use ArtMin96\FilamentJet\Contracts\RemovesTeamMembers;
use ArtMin96\FilamentJet\Contracts\TeamContract;
use ArtMin96\FilamentJet\Contracts\UpdatesTeamNames;
use ArtMin96\FilamentJet\Contracts\UserContract;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Actions\AlwaysAskPasswordConfirmationAction;
use ArtMin96\FilamentJet\Filament\Traits\HasCachedAction;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasUserProperty;
use ArtMin96\FilamentJet\Role;
use ArtMin96\FilamentJet\Traits\RedirectsActions;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Redirector;
use Suleymanozev\FilamentRadioButtonField\Forms\Components\RadioButton;

/**
 * Undocumented trait
 *
 * @property UserContract $user
 * @property ComponentContainer $form
 * @property ComponentContainer $updateTeamNameForm
 * @property ComponentContainer $addTeamMemberForm
 * @property array $roles
 */
class TeamSettings extends Page
{
    use HasCachedAction;
    use RedirectsActions;
    use HasUserProperty;

    protected static string $view = 'filament-jet::filament.pages.team-settings';

    public ?TeamContract $team;

    public ?array $teamState = [];

    public ?array $addTeamMemberState = [];

    public ?string $email = null;

    public ?string $role = null;

    /**
     * The user that is having their role managed.
     */
    //public Authenticatable|Model $managingRoleFor;
    public UserContract $managingRoleFor;

    /**
     * The current role for the user that is having their role managed.
     */
    public ?string $currentRole = null;

    /**
     * Undocumented function
     *
     * @return void|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function mount()
    {
        $this->team = $this->user->currentTeam;

        if (! $this->team) {
            Notification::make()
                ->title(__('filament-jet::teams/messages.current_team_not_exists'))
                ->warning()
                ->send();

            return redirect(config('filament.path'));
        }

        $this->updateTeamNameForm->fill($this->team->withoutRelations()->toArray());
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return config('filament-jet.should_register_navigation.team_settings');
    }

    /**
     * Get the available team member roles.
     */
    public function getRolesProperty(): array
    {
        return collect(FilamentJet::$roles)
            ->transform(function ($role) {
                return with($role->jsonSerialize(), function ($data) {
                    return (new Role(
                        $data['key'],
                        $data['name'],
                        $data['permissions']
                    ))->description($data['description']);
                });
            })
            ->values()
            ->all();
    }

    protected function getForms(): array
    {
        return array_merge(
            parent::getForms(),
            [
                'updateTeamNameForm' => $this->makeForm()
                    ->model(FilamentJet::teamModel())
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-jet::teams/name.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->disabled(! Gate::check('update', $this->team)),
                    ])
                    ->statePath('teamState'),
                'addTeamMemberForm' => $this->makeForm()
                    ->model(FilamentJet::teamInvitationModel())
                    ->schema([
                        TextInput::make('email')
                            ->label(__('filament-jet::teams/add-member.fields.email'))
                            ->required()
                            ->maxLength(255)
                            ->rule('email')
                            ->rules([
                                'email',
                                Features::sendsTeamInvitations()
                                    ? '' : Rule::exists(table: FilamentJet::userModel(), column: 'email'),
                                function () {
                                    return function (string $attribute, $value, Closure $fail) {
                                        if ($this->team?->hasUserWithEmail($value)) {
                                            $fail(__('filament-jet::teams/add-member.messages.already_belongs_to_team'));
                                        }
                                    };
                                },
                            ])
                            ->unique(callback: fn (Unique $rule): Unique => $rule->where('team_id', $this->team->id)),
                        RadioButton::make('role')
                            ->label(__('filament-jet::teams/add-member.fields.role'))
                            ->options(
                                collect($this->roles)->mapWithKeys(fn ($role): array => [
                                    $role->key => $role->name,
                                ])->toArray()
                            )
                            ->descriptions(
                                collect($this->roles)->mapWithKeys(fn ($role): array => [
                                    $role->key => $role->description,
                                ])->toArray()
                            )
                            ->columns(1)
                            ->rules(
                                FilamentJet::hasRoles()
                                ? ['required', 'string', new \ArtMin96\FilamentJet\Rules\Role()]
                                : []
                            ),
                    ]),
            ]
        );
    }

    protected function getHiddenActions(): array
    {
        return [
            AlwaysAskPasswordConfirmationAction::make('delete_team')
                ->label(__('filament-jet::teams/delete.buttons.delete'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action('deleteTeam'),
            Action::make('manage_role')
                ->action(function (array $data): void {
                    $this->updateRole(app(UpdateTeamMemberRole::class));
                })
                ->modalWidth('lg')
                ->modalHeading(__('filament-jet::teams/members.modal_heading'))
                ->modalSubheading(__('filament-jet::teams/members.modal_subheading'))
                ->modalButton(__('filament-jet::teams/members.buttons.save'))
                ->form([
                    RadioButton::make('role')
                        ->label(__('filament-jet::teams/members.fields.role'))
                        ->options(
                            collect($this->roles)->mapWithKeys(fn ($role): array => [
                                $role->key => $role->name,
                            ])->toArray()
                        )
                        ->descriptions(
                            collect($this->roles)->mapWithKeys(fn ($role): array => [
                                $role->key => $role->description,
                            ])->toArray()
                        )
                        ->afterStateUpdated(
                            fn ($state) => $this->currentRole = $state
                        )
                        ->columns(1)
                        ->rules(
                            FilamentJet::hasRoles()
                            ? ['required', 'string', new \ArtMin96\FilamentJet\Rules\Role()]
                            : []
                        ),
                ]),
        ];
    }

    public function updateTeamName(UpdatesTeamNames $updater): void
    {
        $updater->update($this->user, $this->team, $this->teamState);

        Notification::make()
            ->title(__('filament-jet::teams/name.messages.updated'))
            ->success()
            ->send();
    }

    /**
     * Add a new team member to a team.
     */
    public function addTeamMember(): void
    {
        $this->addTeamMemberForm->getState();

        if (Features::sendsTeamInvitations()) {
            app(InvitesTeamMembers::class)->invite(
                $this->user,
                $this->team,
                $this->email,
                $this->role
            );

            $message = __('filament-jet::teams/add-member.messages.invited');
        } else {
            app(AddsTeamMembers::class)->add(
                $this->user,
                $this->team,
                $this->email,
                $this->role
            );

            $message = __('filament-jet::teams/add-member.messages.added');
        }

        $this->email = '';
        $this->role = null;

        $this->team = $this->team->fresh();

        Notification::make()
            ->title($message)
            ->success()
            ->send();
    }

    /**
     * Cancel a pending team member invitation.
     */
    public function cancelTeamInvitation(int $invitationId): void
    {
        if (! empty($invitationId)) {
            $model = FilamentJet::teamInvitationModel();

            $model::whereKey($invitationId)->delete();
        }

        $this->team = $this->team->fresh();

        Notification::make()
            ->title(__('filament-jet::teams/invitations.messages.invitation_canceled'))
            ->success()
            ->send();
    }

    /**
     * Delete the team.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function deleteTeam(ValidateTeamDeletion $validator, DeletesTeams $deleter)
    {
        $validator->validate(Filament::auth()->user(), $this->team);

        $deleter->delete($this->team);

        Notification::make()
            ->title(__('filament-jet::teams/delete.messages.deleted'))
            ->success()
            ->send();

        return $this->redirectPath($deleter);
    }

    /**
     * Remove a team member from the team.
     */
    public function removeTeamMember(int $userId, RemovesTeamMembers $remover): void
    {
        $remover->remove(
            $this->user,
            $this->team,
            $user = FilamentJet::findUserByIdOrFail($userId)
        );

        $this->team = $this->team->fresh();

        Notification::make()
            ->title(__('filament-jet::teams/members.messages.removed'))
            ->success()
            ->send();
    }

    /**
     * Remove the currently authenticated user from the team.
     *
     * @return Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function leaveTeam(RemovesTeamMembers $remover)
    {
        $this->errorBagExcept('team');

        $remover->remove(
            $this->user,
            $this->team,
            $this->user
        );

        $this->team = $this->team->fresh();

        Notification::make()
            ->title(__('filament-jet::teams/members.messages.leave'))
            ->success()
            ->send();

        return redirect(config('filament.path'));
    }

    /**
     * Allow the given user's role to be managed.
     */
    public function manageRole(int $userId): void
    {
        $this->managingRoleFor = FilamentJet::findUserByIdOrFail($userId);
        $this->currentRole = $this->managingRoleFor->teamRole($this->team)->key;

        $this->mountAction('manage_role');
        $this->getMountedActionForm()->fill(['role' => $this->currentRole]);
    }

    /**
     * Save the role for the user being managed.
     */
    public function updateRole(UpdateTeamMemberRole $updater): void
    {
        $updater->update(
            $this->user,
            $this->team,
            $this->managingRoleFor->id,
            $this->currentRole
        );

        $this->team = $this->team->fresh();

        Notification::make()
            ->title(__('filament-jet::teams/members.messages.role_updated'))
            ->success()
            ->send();
    }

    /**
     * @return array<string, string>
     */
    protected function getMessages(): array
    {
        return [
            'email.unique' => __('filament-jet::teams/add-member.messages.already_invited_to_team'),
            'email.exists' => __('filament-jet::teams/add-member.messages.email_not_registered'),
        ];
    }
}
