<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerzRankingSettingResource\Pages;
use App\Models\Comment;
use App\Models\PlayerzRankingSetting;
use Filament\Forms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;

class PlayerzRankingSettingResource extends Resource
{
    protected static ?string $model = PlayerzRankingSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string
    {
        return 'Playerz Ranking Settings';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Header Settings')
                    ->schema([
                        TextInput::make('page_title')
                            ->label('Page Title')
                            ->required()
                            ->maxLength(255)
                            ->default('User-Ranking'),
                            
                        TextInput::make('page_subtitle')
                            ->label('Page Subtitle')
                            ->required()
                            ->maxLength(255)
                            ->default('Die besten Spieler unserer Community'),
                            
                        Textarea::make('header_description')
                            ->label('Header Description')
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        FileUpload::make('header_image')
                            ->label('Header Image')
                            ->image()
                            ->directory('playerz-ranking')
                            ->imageEditor()
                            ->columnSpanFull(),

                        Select::make('creator_user_id')
                            ->label('Creator (Author) User')
                            ->relationship('creator', 'username')
                            ->searchable()
                            ->preload()
                            ->helperText('This user will be shown as the page author.')
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Content Settings')
                    ->schema([
                        RichEditor::make('points_rules_content')
                            ->label('Points Rules Content')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page_title')
                    ->label('Page Title')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('page_subtitle')
                    ->label('Page Subtitle')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('stats')
                    ->label('Views / Comments / Likes')
                    ->getStateUsing(function (PlayerzRankingSetting $record): HtmlString {
                        $viewsCount = $record->views_count ?? 0;
                        $commentsCount = Comment::where('item_type', 'page')->where('item_id', $record->id)->where('status', 1)->count();
                        $likesCount = DB::table('likes')->where('item_type', 'page')->where('item_id', $record->id)->count();
                        $viewBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-md bg-primary-600 text-white">' . number_format($viewsCount) . ' Views</span>';
                        $commentsBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-md bg-primary-600 text-white">' . number_format($commentsCount) . ' Comments</span>';
                        $likesBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-md bg-primary-600 text-white">' . number_format($likesCount) . ' Likes</span>';
                        return new HtmlString($viewBadge . ' ' . $commentsBadge . ' ' . $likesBadge);
                    })
                    ->html()
                    ->wrap(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayerzRankingSettings::route('/'),
            'create' => Pages\CreatePlayerzRankingSetting::route('/create'),
            'edit' => Pages\EditPlayerzRankingSetting::route('/{record}/edit'),
        ];
    }
}