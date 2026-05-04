<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\RolesResource\Pages;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RolesResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = Sidebar::ROLE_PERMISSSION->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];
    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make()
                    ->schema([
                        Group::make([
                            TextInput::make('display_name')
                                ->label(__('messages.common.name') . ':')
                                ->validationAttribute(__('messages.common.name'))
                                ->placeholder(__('messages.common.name'))
                                ->required()
                                ->maxLength(255)
                                ->live(true)
                                ->afterStateUpdated(function (callable $set, $state) {
                                    $formattedState = strtolower(str_replace(' ', '_', $state));
                                    $set('name', $formattedState);
                                }),
                        ])->columns(2)->columnSpanFull(),
                        CheckboxList::make('permissions')
                            ->label(__('messages.role.role_permissions'))
                            ->columns(2)
                            ->bulkToggleable()
                            ->required()
                            ->relationship('permissions', 'display_name')
                            ->validationMessages([
                                'required' => __('messages.placeholder.permissions_required'),
                            ]),

                        Hidden::make('name'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        $table = $table->modifyQueryUsing(function ($query) {
            $query->with(['users', 'permissions:display_name']);
        });
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label(__('messages.common.count'))
                    ->counts('users'),
                TextColumn::make('permissions')
                    ->label(__('messages.role.permissions'))
                    ->wrap()
                    ->formatStateUsing(function (Role $record) {
                        $permissionNames = [];
                        foreach ($record->permissions as $permission) {
                            $permissionNames[] = "<span class='inline-block my-2 mx-1 px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded'>{$permission->display_name}</span>";
                        }
                        return implode(' ', $permissionNames);
                    })
                    ->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Role $record) {
                        if ($record->name == 'admin' || $record->name == 'staff') {
                            return Notification::make()
                                ->title(__('messages.placeholder.this_action_not_allowed_for_default_records'))
                                ->danger()
                                ->send();
                        }

                        if ($record->is_default == 1) {
                            return Notification::make()
                                ->title(__('messages.placeholder.default_role_do_not_deleted'))
                                ->danger()
                                ->send();
                        }
                        $record->delete();

                        return Notification::make()
                            ->title(__('messages.placeholder.role_deleted_successfully'))
                            ->success()
                            ->send();
                    })
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.role.role'))
                    ->hidden(function (Role $record) {
                        return $record->name == 'customer';
                    }),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.roles'))
                        ->successNotificationTitle(__('messages.placeholder.role_deleted_successfully')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRoles::route('/create'),
            'view' => Pages\ViewRoles::route('/{record}'),
            'edit' => Pages\EditRoles::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.roles_permissions');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.roles_permissions');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_roles_permission');
    }
}
