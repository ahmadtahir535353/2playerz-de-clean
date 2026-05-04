<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\LanguagesResource\Pages;
use App\Filament\Resources\LanguagesResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Language;
use App\Models\Languages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
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
use File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class LanguagesResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?int $navigationSort = Sidebar::LANGUAGE->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Hidden::make('id'),
                TextInput::make('name')
                    ->label(__('messages.language.language') . ':')
                    ->validationAttribute(__('messages.language.language'))
                    ->placeholder(__('messages.language.language'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('iso_code')
                    ->label(__('messages.language.iso_code') . ':')
                    ->validationAttribute(__('messages.language.iso_code'))
                    ->placeholder(__('messages.language.iso_code'))
                    ->required()
                    ->maxLength(255),
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
                    ->sortable()
                    ->searchable(),
                TextColumn::make('iso_code')
                    ->label(__('messages.language.iso_code'))
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('front_language_status')
                    ->label(__('messages.language.front_language'))
                    ->sortable()
                    ->updateStateUsing(function ($state, Language $record) {
                        if ($record->iso_code === 'en') {
                            $record->update(['front_language_status' => true]);

                            return  Notification::make()
                                ->danger()
                                ->title(__('messages.english_is_default'))
                                ->duration(2000)
                                ->send();
                        }

                        $record->update(['front_language_status' => $state]);

                        return Notification::make()
                            ->success()
                            ->title(
                                $state
                                    ? __('messages.placeholder.language_added_to_front_successfully')
                                    : __('messages.placeholder.language_removed_from_front_successfully')
                            )
                            ->duration(2000)
                            ->send();
                    }),
                TextColumn::make('translation_link')
                    ->label(__('messages.language.translation'))
                    ->default(__('messages.language.edit_translation'))
                    ->color('primary')
                    ->url(fn(Language $record) => route('filament.admin.resources.languages.translation', ['record' => $record->id])),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('lg')
                    ->iconButton()
                    ->modalWidth('md')
                    ->modalHeading(__('messages.language.edit_language'))
                    // ->successNotificationTitle(__('messages.placeholder.language_updated_successfully'))
                    ->action(function ($data) {
                        try {
                            $language = Language::find($data['id']); // Assume $data['id'] is passed for update, null for create

                            if ($language) {
                                $oldIsoCode = $language->iso_code;
                                $language->update([
                                    'name' => $data['name'],
                                    'iso_code' => $data['iso_code'],
                                ]);

                                if ($oldIsoCode !== $data['iso_code']) {
                                    $path = App::langPath();
                                    if (File::exists($path . '/' . $oldIsoCode)) {
                                        rename($path . '/' . $oldIsoCode, $path . '/' . $data['iso_code']);
                                    }
                                }
                            }
                            return Notification::make()
                                ->title(__('messages.placeholder.language_updated_successfully'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            throw new UnprocessableEntityHttpException($e->getMessage());
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Language $record) {
                        $userLanguages = User::where('status', 1)->pluck('language')->toArray();

                        if ($record->is_default == true) {
                            return Notification::make()
                                ->title(__('messages.placeholder.default_language_deleted'))
                                ->danger()
                                ->send();
                        }

                        if (in_array($record->iso_code, $userLanguages)) {
                            return Notification::make()
                                ->title(__('messages.placeholder.language_be_deleted'))
                                ->danger()
                                ->send();
                        }


                        try {
                            // Delete the corresponding language folder in 'lang' directory
                            File::deleteDirectory(lang_path() . '/' . $record->iso_code);
                            $record->delete();
                            return Notification::make()
                                ->title(__('messages.placeholder.language_deleted_successfully'))
                                ->success()
                                ->send();

                            return true;
                        } catch (\Exception $e) {
                            throw new UnprocessableEntityHttpException($e->getMessage());
                        }
                    })
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.language.language')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.languages'))
                        ->successNotificationTitle(__('messages.placeholder.language_deleted_successfully')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLanguages::route('/'),
            'translation' => Pages\LanguageTranslation::route('/{record}/translation'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.languages');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.languages');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_language');
    }
}
