<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerzLevelResource\Pages;
use App\Models\PlayerzLevel;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlayerzLevelResource extends Resource
{
    protected static ?string $model = PlayerzLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('messages.other_lang.level_name'))
                    ->required()
                    ->maxLength(50),

                TextInput::make('min_points')
                    ->label(__('messages.other_lang.points_required'))
                    ->numeric()
                    ->required(),

                ColorPicker::make('badge_color')
                    ->label(__('messages.other_lang.badge_background_color'))
                    ->default('#1e40af')
                    ->required(),

                ColorPicker::make('badge_text_color')
                    ->label(__('messages.other_lang.badge_text_color'))
                    ->default('#93c5fd')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(function ($record) {
                return static::getUrl('edit', ['record' => $record]);
            }) 
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.other_lang.level_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('min_points')
                    ->label(__('messages.other_lang.points'))
                    ->sortable(),

                TextColumn::make('badge_color')
                    ->label(__('messages.other_lang.badge_color'))
                    ->html()
                    ->formatStateUsing(fn ($state) => '<span style="display: inline-block; width: 20px; height: 20px; background-color: ' . ($state ?: '#1e40af') . '; border-radius: 4px; border: 1px solid #ccc;"></span> ' . ($state ?: '#1e40af')),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('messages.other_lang.edit_point'))
                    ->iconButton()
                    ->tooltip(__('messages.other_lang.edit_level'))
                    ->url(fn ($record) => static::getUrl('edit', ['record' => $record])),

                DeleteAction::make()
                    ->label(__('messages.other_lang.delete_point'))
                    ->iconButton()
                    ->tooltip(__('messages.other_lang.delete_level'))
                    ->requiresConfirmation()
                    ->modalHeading(__('messages.other_lang.delete_level'))
                    ->modalDescription(__('messages.common.are_you_sure'))
                    ->modalSubmitActionLabel(__('messages.common.confirm'))
                    ->modalCancelActionLabel(__('messages.common.cancel'))
                    ->successNotificationTitle(__('messages.other_lang.level_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.other_lang.actions')); 

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayerzLevels::route('/'),
            'create' => Pages\CreatePlayerzLevel::route('/create'),
            'edit' => Pages\EditPlayerzLevel::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('messages.other_lang.playerz_level_single'); 
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.other_lang.playerz_level_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.other_lang.playerz_level_plural');
    }

}
