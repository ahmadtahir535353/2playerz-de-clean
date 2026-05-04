<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointRuleResource\Pages;
use App\Models\PointRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;

class PointRuleResource extends Resource
{
    protected static ?string $model = PointRule::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    public static function getNavigationLabel(): string
    {
        return __('messages.other_lang.point_rules');
    }

    public static function getPluralLabel(): string
    {
        return __('messages.other_lang.point_rules');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('key')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('points')->numeric()->minValue(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->searchable()->label(__('messages.other_lang.point_rules_lable')),
                Tables\Columns\TextColumn::make('key')->badge()->label(__('messages.other_lang.point_rules_key')),
                Tables\Columns\TextColumn::make('points')->label(__('messages.other_lang.point_rules_points')),
            ])
            ->actions([
                Action::make('editPoints')
                    ->label('')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->url(fn (PointRule $record): string => static::getUrl('edit', ['record' => $record])),
            ])
            ->actionsColumnLabel(__('messages.other_lang.actions'))
            ->bulkActions([]);
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPointRules::route('/'),
            'edit' => Pages\EditPointRule::route('/{record}/edit'),
        ];
    }
}