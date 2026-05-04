<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\MenusResource\Pages;
use App\Filament\Resources\MenusResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Menu;
use App\Models\Menus;
use App\Models\Navigation;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class MenusResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $slug = 'menus';

    protected static ?string $navigationIcon = 'heroicon-o-bars-4';

    protected static ?int $navigationSort = Sidebar::MENUS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('messages.menu.title') . ':')
                            ->validationAttribute(__('messages.menu.title'))
                            ->placeholder(__('messages.menu.title'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignorable: fn(?Menu $record) => $record),
                        Select::make('parent_menu_id')
                            ->label(__('messages.menu.parent_menu') . ':')
                            ->validationAttribute(__('messages.menu.parent_menu'))
                            ->placeholder(__('messages.menu.parent_menu'))
                            ->relationship('parent', 'title', ignoreRecord: true)
                            ->options(Menu::where('parent_menu_id', null)->get()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false),
                        TextInput::make('link')
                            ->label(__('messages.menu.link') . ':')
                            ->validationAttribute(__('messages.menu.link'))
                            ->placeholder(__('messages.menu.link'))
                            ->url()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('order')
                            ->label(__('messages.menu.menu_order') . ':')
                            ->validationAttribute(__('messages.menu.menu_order'))
                            ->placeholder(__('messages.menu.menu_order'))
                            ->numeric()
                            ->minValue(1),
                        Toggle::make('show_in_menu')
                            ->label(__('messages.menu.show_in_menu') . ':')
                            ->validationAttribute(__('messages.menu.show_in_menu'))
                            ->inline(false),
                        Hidden::make('is_default')
                            ->default(false)
                            ->dehydrated(function ($state) {
                                if (Schema::hasColumn('menus', 'is_default')) {
                                    return true;
                                }
                            }),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label(__('messages.common.title'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('parent.title')
                    ->label(__('messages.menu.parent_menu'))
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return  isset($state) ? $state : __('messages.menu.n_a');
                    })
                    ->default(__('messages.menu.n_a'))
                    ->searchable(),
                ToggleColumn::make('show_in_menu')->label(__('messages.menu.show_in_menu'))
                    ->afterStateUpdated(fn() => Notification::make()->title(__('messages.placeholder.show_in_menu_updated_successfully'))->success()->send()),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->action(function ($record) {
                        $menuId = $record->id;
                        $parentMenuId = $record->parent_menu_id;

                        $record->navigation()->delete();
                        if (is_null($parentMenuId)) {
                            Navigation::whereNavigationableType(Menu::class)->whereParentId($menuId)->delete();
                        } else {
                            $subsNavigation = Navigation::whereNavigationableType(Menu::class)
                                ->whereParentId($parentMenuId)->orderBy('order_id')->get();
                            foreach ($subsNavigation as $key => $navigation) {
                                $navigation->update([
                                    'order_id' => $key + 1,
                                ]);
                            }
                        }
                        $record->delete();
                    })
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.menu.menu'))
                    ->successNotificationTitle(__('messages.placeholder.menu_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.menus'))
                        ->successNotificationTitle(__('messages.placeholder.menu_deleted_successfully')),
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
            'index' => Pages\ListMenuses::route('/'),
            'create' => Pages\CreateMenus::route('/create'),
            'view' => Pages\ViewMenus::route('/{record}'),
            'edit' => Pages\EditMenus::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.menus');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.menu.menu');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_menu');
    }
}
