<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\PollsResource\Pages;
use App\Filament\Resources\PollsResource\Pages\PollVoteResult;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Poll;
use App\Models\Post;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PollsResource extends Resource
{
    protected static ?string $model = Poll::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?int $navigationSort = Sidebar::POLLS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('lang_id')
                            ->label(__('messages.common.select_language') . ':')
                            ->validationAttribute(__('messages.common.select_language'))
                            ->placeholder(__('messages.common.select_language'))
                            ->relationship('language', 'name')
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required(),
                        Toggle::make('status')
                            ->label(__('messages.status') . ':')
                            ->validationAttribute(__('messages.status'))
                            ->inline(false),

                        Select::make('display_location')
                            ->label('Display Location:')
                            ->options([
                                'home' => 'Home',
                                'article' => 'Article',
                            ])
                            ->default('home')
                            ->reactive()
                            ->required(),

                        Select::make('post_id')
                            ->label('Select Article:')
                            ->options(function () {
                                return Post::pluck('title', 'id')->toArray();
                            })
                            ->searchable()
                            ->visible(fn ($get) => $get('display_location') === 'article')
                            ->required(fn ($get) => $get('display_location') === 'article'),

                        Toggle::make('multi_select')
                            ->label('Multi Select' . ':')
                            ->validationAttribute(__('messages.poll.multi_select'))
                            ->inline(false),
                        TextInput::make('question')
                            ->label(__('messages.poll.question') . ':')
                            ->validationAttribute(__('messages.poll.question'))
                            ->placeholder(__('messages.poll.question'))
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255)
                            ->unique(ignorable: fn(?Poll $record) => $record),
                        TextInput::make('option1')
                            ->label(__('messages.poll.option1') . ':')
                            ->validationAttribute(__('messages.poll.option1'))
                            ->placeholder(__('messages.poll.option1'))
                            ->required(),
                        TextInput::make('option2')
                            ->label(__('messages.poll.option2') . ':')
                            ->validationAttribute(__('messages.poll.option2'))
                            ->placeholder(__('messages.poll.option2'))
                            ->required(),
                        TextInput::make('option3')
                            ->label(__('messages.poll.option3') . ':')
                            ->validationAttribute(__('messages.poll.option3'))
                            ->placeholder(__('messages.poll.option3')),
                        TextInput::make('option4')
                            ->label(__('messages.poll.option4') . ':')
                            ->validationAttribute(__('messages.poll.option4'))
                            ->placeholder(__('messages.poll.option4')),
                        TextInput::make('option5')
                            ->label(__('messages.poll.option5') . ':')
                            ->validationAttribute(__('messages.poll.option5'))
                            ->placeholder(__('messages.poll.option5')),
                        TextInput::make('option6')
                            ->label(__('messages.poll.option6') . ':')
                            ->validationAttribute(__('messages.poll.option6'))
                            ->placeholder(__('messages.poll.option6')),
                        TextInput::make('option7')
                            ->label(__('messages.poll.option7') . ':')
                            ->validationAttribute(__('messages.poll.option7'))
                            ->placeholder(__('messages.poll.option7')),
                        TextInput::make('option8')
                            ->label(__('messages.poll.option8') . ':')
                            ->validationAttribute(__('messages.poll.option8'))
                            ->placeholder(__('messages.poll.option8')),
                        TextInput::make('option9')
                            ->label(__('messages.poll.option9') . ':')
                            ->validationAttribute(__('messages.poll.option9'))
                            ->placeholder(__('messages.poll.option9')),
                        TextInput::make('option10')
                            ->label(__('messages.poll.option10') . ':')
                            ->validationAttribute(__('messages.poll.option10'))
                            ->placeholder(__('messages.poll.option10')),

                        Radio::make('vote_permission')
                            ->label(__('messages.details.vote') . ' ' . __('messages.role.permissions') . ':')
                            ->validationAttribute(__('messages.details.vote') . ' ' . __('messages.role.permissions'))
                            ->options([
                                '1' => __('messages.poll.all_user'),
                                '2' => __('messages.poll.register_user'),
                            ])
                            ->default(2),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('question')
                    ->label(__('messages.poll.question'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('language.name')
                    ->label(__('messages.common.language'))
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('status')->label(__('messages.status'))
                    ->afterStateUpdated(function ($state) {
                        Notification::make()
                            ->success()
                            ->title(__('messages.placeholder.status_updated_successfully'))
                            ->duration(2000)
                            ->send();
                    }),
                // Add null check for display_location
                TextColumn::make('display_location')
                    ->label('Display Location')
                    ->formatStateUsing(fn ($state, $record) => $record && $record->display_location ? ucfirst($record->display_location) : '-'),
                
                TextColumn::make('id')
                    ->label(__('messages.common.result'))
                    ->formatStateUsing(function (Poll $record) {
                        return __('messages.poll.show_result');
                    })
                    ->alignCenter()
                    ->color('primary')
                    ->url(function (Poll $record) {
                        if (auth()->user()->hasRole('customer')) {
                            return route('filament.customer.resources.polls.results', ['record' => $record->id]);
                        } else {
                            return route('filament.admin.resources.polls.results', ['record' => $record->id]);
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.post.post'))
                    ->successNotificationTitle(__('messages.placeholder.poll_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.post.posts'))
                        ->successNotificationTitle(__('messages.placeholder.poll_deleted_successfully')),
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
            'index' => Pages\ListPolls::route('/'),
            'create' => Pages\CreatePolls::route('/create'),
            'view' => Pages\ViewPolls::route('/{record}'),
            'edit' => Pages\EditPolls::route('/{record}/edit'),
            'results' => PollVoteResult::route('/{record}/results'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.polls');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.polls');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_polls');
    }
}