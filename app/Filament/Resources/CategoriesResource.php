<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\CategoriesResource\Pages;
use App\Filament\Resources\CategoriesResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Categories;
use App\Models\Category;
use App\Models\Navigation;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Mailer\Transport\Dsn;
use Illuminate\Support\Str;

class CategoriesResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-2';

    protected static ?int $navigationSort = Sidebar::CATEGORIES->value;

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
                    ->maxLength(255)
                    ->live(debounce: 800)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state) {
                        if ($operation == 'edit') {
                            $set('slug', Str::slug($state));
                        }
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    })
                    ->unique(ignorable: fn(?Category $record) => $record),
                TextInput::make('slug')
                    ->label(__('messages.category.slug') . ':')
                    ->validationAttribute(__('messages.category.slug'))
                    ->placeholder(__('messages.category.slug'))
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                Select::make('lang_id')
                    ->label(__('messages.common.language') . ':')
                    ->validationAttribute(__('messages.common.language'))
                    ->placeholder(__('messages.common.select_language'))
                    ->relationship('language', 'name')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->required(),
                ColorPicker::make('color')
                    ->label(__('messages.other_lang.color') . ':')
                    ->validationAttribute(__('messages.other_lang.color'))
                    ->default('#B051B0') 
                    ->required()
                    ->alpha(false)
                    ->helperText(__('messages.other_lang.color_helper')),    
                SpatieMediaLibraryFileUpload::make('category_image')
                    ->label(__('messages.category_image') . ':')
                    ->validationAttribute(__('messages.category_image'))
                    ->reorderable()
                    ->required()
                    ->image()
                    ->collection(Category::CATEGORY_IMAGE),
                Group::make([
                    Toggle::make('show_in_menu')
                        ->inline(false)
                        ->default(true)
                        ->label(__('messages.category.show_menu') . ':')
                        ->validationAttribute(__('messages.category.show_menu')),
                    Toggle::make('show_in_home_page')
                        ->inline(false)
                        ->default(true)
                        ->label(__('messages.category.show_home') . ':')
                        ->validationAttribute(__('messages.category.show_home')),
                ])->columns(2),
                Hidden::make('is_default')
                    ->default(false)
                    ->dehydrated(function ($state) {
                        if (Schema::hasColumn('categories', 'is_default')) {
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
                SpatieMediaLibraryImageColumn::make('profile')
                    ->label(__('messages.post.image'))
                    ->circular()
                    ->collection(Category::CATEGORY_IMAGE)
                    ->simpleLightbox()
                    ->defaultImageUrl(asset('front_web/images/default.jpg')),
                TextColumn::make('name')
                    ->label(__('messages.common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('language.name')
                    ->label(__('messages.common.language'))
                    ->sortable(),
                TextColumn::make('posts_count')
                    ->label(__('messages.common.count'))
                    ->counts('posts'),
                ToggleColumn::make('show_in_menu')->label(__('messages.category.show_menu'))
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->title(__('messages.placeholder.show_in_menu_updated_successfully'))
                            ->success()
                            ->send();
                    }),
                ToggleColumn::make('show_in_home_page')->label(__('messages.category.show_home'))
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->title(__('messages.placeholder.show_in_home_updated_successfully'))
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->modalWidth('md')
                    ->modalHeading(__('messages.category.edit_category'))
                    ->action(function ($record, array $data) {
                        $category = Category::findOrFail($record->id);
                        $category->update($data);

                        return  Notification::make()
                            ->success()
                            ->title(__('messages.placeholder.category_updated_successfully'))
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Category $record) {
                        if ($record->is_default) {
                            return Notification::make()
                                ->title(__('messages.placeholder.this_action_not_allowed_for_default_records'))
                                ->danger()
                                ->send();
                        }

                        $id = $record->id;

                        if ($record->subCategories()->count() > 0 || $record->posts()->count() > 0) {
                            return Notification::make()
                                ->title(__('messages.placeholder.this_category_is_in_use'))
                                ->danger()
                                ->send();
                        }

                        $record->navigation()->each(function ($navigation) {
                            $navigation->delete();
                        });

                        Navigation::whereNavigationableType(SubCategory::class)
                            ->whereParentId($id)
                            ->delete();

                        $record->delete();

                        return Notification::make()
                            ->title(__('messages.placeholder.category_deleted_successfully'))
                            ->success()
                            ->send();
                    })
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.post.category'))

            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.category.categorys'))
                        ->successNotificationTitle(__('messages.placeholder.staff_deleted_successfully')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategories::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.categories');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.categories');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_categories');
    }
}
