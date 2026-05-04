<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = Sidebar::PAGES->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('messages.page.name') . ':')
                            ->validationAttribute(__('messages.page.name'))
                            ->placeholder(__('messages.page.name'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignorable: fn(?Page $record) => $record),
                        TextInput::make('title')
                            ->label(__('messages.page.title') . ':')
                            ->validationAttribute(__('messages.page.title'))
                            ->placeholder(__('messages.page.title'))
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state) {
                                if ($operation == 'edit') {
                                    $set('slug', str($state)->slug()->toString());
                                }
                                if (($get('slug') ?? '') !== str($old)->slug()->toString()) {
                                    return;
                                }

                                $set('slug', str($state)->slug()->toString());
                            })
                            ->unique(ignorable: fn(?Page $record) => $record),
                        TextInput::make('slug')
                            ->label(__('messages.page.slug') . ':')
                            ->validationAttribute(__('messages.page.slug'))
                            ->placeholder(__('messages.page.slug'))
                            ->columnSpanFull()
                            ->required()
                            ->readOnly()
                            ->maxLength(255),
                        TextInput::make('meta_title')
                            ->label(__('messages.page.meta_title') . ':')
                            ->validationAttribute(__('messages.page.meta_title'))
                            ->placeholder(__('messages.page.meta_title'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('meta_description')
                            ->label(__('messages.page.meta_description') . ':')
                            ->validationAttribute(__('messages.page.meta_description'))
                            ->placeholder(__('messages.page.meta_description'))
                            ->required()
                            ->maxLength(255),
                        Select::make('lang_id')
                            ->label(__('messages.page.add_lang') . ':')
                            ->validationAttribute(__('messages.page.add_lang'))
                            ->placeholder(__('messages.page.add_lang'))
                            ->relationship('language', 'name')
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required(),
                        Radio::make('location')
                            ->label(__('messages.page.location') . ':')
                            ->validationAttribute(__('messages.page.location'))
                            ->columnSpanFull()
                            ->columns(2)
                            ->required()
                            // ->inline(true)
                            ->inlineLabel(true)
                            ->options(Page::getMenuType()),
                        Toggle::make('visibility')
                            ->label(__('messages.page.visibility') . ':')
                            ->validationAttribute(__('messages.page.visibility'))
                            ->columnSpanFull()
                            ->inlineLabel(true)
                            ->default(true),
                        Toggle::make('show_breadcrumb')
                            ->label(__('messages.page.show_breadcrumb') . ':')
                            ->validationAttribute(__('messages.page.show_breadcrumb'))
                            ->columnSpanFull()
                            ->inlineLabel(true),
                        Toggle::make('show_right_column')
                            ->label(__('messages.page.show_right') . ':')
                            ->validationAttribute(__('messages.page.show_right'))
                            ->columnSpanFull()
                            ->inlineLabel(true),
                        Toggle::make('show_title')
                            ->label(__('messages.page.show_title') . ':')
                            ->validationAttribute(__('messages.page.show_title'))
                            ->columnSpanFull()
                            ->inlineLabel(true),
                        Toggle::make('permission')
                            ->label(__('messages.page.user_show') . ':')
                            ->validationAttribute(__('messages.page.user_show'))
                            ->columnSpanFull()
                            ->inlineLabel(true),
                        RichEditor::make('content')
                            ->label(__('messages.content') . ':')
                            ->validationAttribute(__('messages.content'))
                            ->placeholder(__('messages.content'))
                            ->columnSpanFull(),
                        // TextInput::make('parent_menu_link')
                        //     ->numeric(),
                        // TextInput::make('lang_id')
                        //     ->required()
                        //     ->numeric(),
                        Hidden::make('is_default')
                            ->default(false)
                            ->dehydrated(function ($state) {
                                if (Schema::hasColumn('pages', 'is_default')) {
                                    return true;
                                }
                            }),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('messages.common.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('language.name')
                    ->label(__('messages.common.language'))
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('visibility')
                    ->label(__('messages.page.visibility'))
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->success()
                            ->title(__('messages.placeholder.visibility_updated_successfully'))
                            ->duration(2000)
                            ->send();
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.page.page'))
                    ->modalDescription(__('messages.common.are_you_sure'))
                    ->modalCancelActionLabel(__('messages.common.cancel'))
                    ->modalSubmitActionLabel(__('messages.common.confirm'))
                    ->successNotificationTitle(__('messages.placeholder.page_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.pages'))
                        ->modalDescription(__('messages.common.are_you_sure'))
                        ->modalCancelActionLabel(__('messages.common.cancel'))
                        ->modalSubmitActionLabel(__('messages.common.confirm'))
                        ->successNotificationTitle(__('messages.placeholder.page_deleted_successfully')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.pages');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.pages');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'view' => Pages\ViewPage::route('/{record}'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_pages');
    }
}
