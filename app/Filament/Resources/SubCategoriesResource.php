<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\SubCategoriesResource\Pages;
use App\Filament\Resources\SubCategoriesResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Category;
use App\Models\Navigation;
use App\Models\SubCategories;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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

class SubCategoriesResource extends Resource
{
    protected static ?string $model = SubCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?int $navigationSort = Sidebar::SUB_CATEGORIES->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                TextInput::make('name')
                    ->label(__('messages.common.name') . ':')
                    ->validationAttribute(__('messages.common.name'))
                    ->placeholder(__('messages.common.name'))
                    ->required()
                    ->live(debounce: 800)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state) {
                        if ($operation == 'edit') {
                            $set('slug', str($state)->slug()->toString());
                        }
                        if (($get('slug') ?? '') !== str($old)->slug()->toString()) {
                            return;
                        }

                        $set('slug', str($state)->slug()->toString());
                    })
                    ->unique(ignorable: fn(?SubCategory $record) => $record),
                TextInput::make('slug')
                    ->label(__('messages.common.slug') . ':')
                    ->validationAttribute(__('messages.common.slug'))
                    ->placeholder(__('messages.common.slug'))
                    ->readOnly()
                    ->required(),
                Select::make('parent_category_id')
                    ->label(__('messages.sub_category.select_cat') . ':')
                    ->validationAttribute(__('messages.sub_category.select_cat'))
                    ->placeholder(__('messages.sub_category.select_cat'))
                    ->relationship('category', 'name')
                    ->native(false)
                    ->afterStateUpdated(function (Set $set, $state) {
                        Category::find($state)->lang_id;
                        $set('lang_id', Category::find($state)->lang_id);
                    })
                    ->searchable()
                    ->live()
                    ->preload()
                    ->required(),
                Select::make('lang_id')
                    ->label(__('messages.sub_category.add_lan') . ':')
                    ->validationAttribute(__('messages.sub_category.add_lan'))
                    ->placeholder(__('messages.sub_category.add_lan'))
                    ->relationship('language', 'name')
                    ->disableOptionWhen(true)
                    ->preload()
                    ->native(false)
                    ->required(),
                Toggle::make('show_in_menu')
                    ->label(__('messages.sub_category.show_menu') . ':')
                    ->validationAttribute(__('messages.sub_category.show_menu'))
                    ->inline(false)
                    ->default(true),
                Hidden::make('is_default')
                    ->default(false)
                    ->dehydrated(function ($state) {
                        if (Schema::hasColumn('sub_categories', 'is_default')) {
                            return true;
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('messages.menu.parent_menu'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('language.name')
                    ->label(__('messages.common.language'))
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('show_in_menu')->label(__('messages.menu.show_in_menu'))
                    ->afterStateUpdated(fn() => Notification::make()->title(__('messages.placeholder.show_in_menu_updated_successfully'))->success()->send()),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->modalWidth('md')
                    ->modalHeading(__('messages.sub_category.edit'))
                    ->action(function ($record, array $data) {
                        $record = SubCategory::find($record->id);
                        $oldParentId = $record->parent_category_id;
                        $changeParent = $data['parent_category_id'] != $oldParentId;
                        $record->update($data);
                        if ($changeParent) {
                            $navigationOrder = Navigation::whereNavigationableType(SubCategory::class)
                                ->whereParentId($record->parent_category_id)->count() + 1;
                            $record->navigation->update([
                                'order_id' => $navigationOrder,
                                'parent_id' => $record->parent_category_id,
                            ]);
                            $subsNavigation = Navigation::whereNavigationableType(SubCategory::class)
                                ->whereParentId($oldParentId)->orderBy('order_id')->get();
                            foreach ($subsNavigation as $key => $navigation) {
                                $navigation->update([
                                    'order_id' => $key + 1,
                                ]);
                            }
                        }
                        return Notification::make()
                            ->success()
                            ->title(__('messages.placeholder.sub_category_updated_successfully'))
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->action(function (SubCategory $record) {
                        if ($record->is_default) {
                            return Notification::make()
                                ->title(__('messages.placeholder.this_action_not_allowed_for_default_records'))
                                ->danger()
                                ->send();
                        }

                        if ($record->post()->count() > 0) {
                            return Notification::make()
                                ->title(__('messages.placeholder.this_sub_category_is_in_use'))
                                ->danger()
                                ->send();
                        }
                        $parentId = $record->parent_category_id;
                        $record->navigation()->delete();

                        $subsNavigation = Navigation::whereNavigationableType(SubCategory::class)
                            ->whereParentId($parentId)->orderBy('order_id')->get();
                        foreach ($subsNavigation as $key => $navigation) {
                            $navigation->update([
                                'order_id' => $key + 1,
                            ]);
                        }

                        $record->delete();

                        return Notification::make()
                            ->title(__('messages.placeholder.sub_category_delete_successfully'))
                            ->success()
                            ->send();
                    })
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.post.category')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.sub_category.sub_categorys'))
                        ->successNotificationTitle(__('messages.placeholder.staff_deleted_successfully')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubCategories::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.sub_categories');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.sub_categories');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_sub_categories');
    }
}
