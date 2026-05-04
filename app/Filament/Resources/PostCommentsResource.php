<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\PostCommentsResource\Pages;
use App\Filament\Resources\PostCommentsResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostComments;
use Illuminate\Support\Facades\DB;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PostCommentsResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    protected static ?int $navigationSort = Sidebar::COMMENTS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $table = $table->modifyQueryUsing(function (Builder $query) {
            $query->with(['post', 'users'])
                ->withoutGlobalScopes();
        });

        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('post.title')
                    ->label(__('messages.post.posts'))
                    ->width(400)
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->url(function ($record) {
                        $baseUrl = route('detailPage', [$record->post->slug ?? '']);
                        return $baseUrl . '#comment-' . $record->id;
                    })
                    ->openUrlInNewTab(), 
                TextColumn::make('users.username')
                    ->label(__('messages.staff.username'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->users->username ?? 'N/A'),
                TextColumn::make('comment')
                    ->label(__('messages.comment.comment'))
                    ->sortable()
                    ->searchable()
                    ->width(800)
                    ->wrap(),
                IconColumn::make('status')
                    ->label(__('messages.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Action::make('hide')
                    ->label(__('messages.comment.hide_comment'))
                    ->icon('heroicon-o-eye-slash')
                    ->color('gray')
                    ->visible(fn (Comment $record) => (int) $record->status === 1 && !$record->trashed())
                    ->fillForm(fn (Comment $record) => [
                        'reply_count' => Comment::withoutGlobalScopes()->where('parent_id', $record->id)->count(),
                        'hide_scope' => 'only',
                    ])
                    ->form(fn (Comment $record) => [
                        Hidden::make('reply_count'),
                        Placeholder::make('message')
                            ->content(function ($get) {
                                $count = (int) $get('reply_count');
                                return $count === 0
                                    ? __('messages.comment.hide_modal_what_do_you_want')
                                    : trans_choice('messages.comment.hide_modal_has_replies', $count, ['count' => $count]) . "\n" . __('messages.comment.hide_modal_what_do_you_want');
                            }),
                        Radio::make('hide_scope')
                            ->label('')
                            ->options([
                                'only' => __('messages.comment.hide_modal_only_this'),
                                'with_replies' => __('messages.comment.hide_modal_with_replies'),
                            ])
                            ->default('only')
                            ->required()
                            ->visible(fn ($get) => (int) $get('reply_count') > 0),
                    ])
                    ->modalHeading(__('messages.comment.hide_comment'))
                    ->modalSubmitActionLabel(__('messages.comment.hide_comment'))
                    ->action(function (array $data, Comment $record) {
                        $record->update(['status' => 0]);
                        if (($data['hide_scope'] ?? 'only') === 'with_replies') {
                            Comment::withoutGlobalScopes()
                                ->where('parent_id', $record->id)
                                ->update(['status' => 0]);
                        }
                    })
                    ->successNotificationTitle(__('messages.placeholder.comment_hidden_successfully')),
                Action::make('show')
                    ->label(__('messages.comment.show_comment'))
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn (Comment $record) => (int) $record->status === 0 && !$record->trashed())
                    ->requiresConfirmation()
                    ->modalHeading(__('messages.comment.show_comment'))
                    ->action(fn (Comment $record) => $record->update(['status' => 1]))
                    ->successNotificationTitle(__('messages.placeholder.comment_restored_successfully')),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.comment.comment'))
                    ->successNotificationTitle(__('messages.placeholder.comment_deleted_successfully'))
                    ->before(function ($record) {
                        // Send notification to the user who made the comment
                        if ($record->user_id && $record->post) {
                            DB::table('notifications')->insert([
                                'type' => 'comment_deleted',
                                'to_user_id' => $record->user_id,
                                'notifiable_type' => 'App\Models\User',
                                'notifiable_id' => $record->user_id,
                                'data' => json_encode([
                                    'type' => 'comment_deleted',
                                    'message' => __('messages.notification.comment_deleted_message'),
                                    'post_id' => $record->post->id,
                                    'comment_id' => $record->id,
                                    'post_title' => $record->post->title,
                                    'post_slug' => $record->post->slug,
                                ]),
                                'read_at' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }),
                Tables\Actions\RestoreAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.restore') . ' ' . __('messages.comment.comment'))
                    ->successNotificationTitle(__('messages.placeholder.comment_restored_successfully')),
                Tables\Actions\ForceDeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.force_delete') . ' ' . __('messages.comment.comment'))
                    ->successNotificationTitle(__('messages.placeholder.comment_force_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.comment.comments'))
                        ->successNotificationTitle(__('messages.placeholder.comment_deleted_successfully'))
                        ->before(function ($records) {
                            // Send notification to each user whose comment is being deleted
                            foreach ($records as $record) {
                                if ($record->user_id && $record->post) {
                                    DB::table('notifications')->insert([
                                        'type' => 'comment_deleted',
                                        'to_user_id' => $record->user_id,
                                        'notifiable_type' => 'App\Models\User',
                                        'notifiable_id' => $record->user_id,
                                        'data' => json_encode([
                                            'type' => 'comment_deleted',
                                            'message' => __('messages.notification.comment_deleted_message'),
                                            'post_id' => $record->post->id,
                                            'comment_id' => $record->id,
                                            'post_title' => $record->post->title,
                                            'post_slug' => $record->post->slug,
                                        ]),
                                        'read_at' => null,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                }
                            }
                        }),
                    Tables\Actions\RestoreBulkAction::make()
                        ->tooltip(__('messages.restore'))
                        ->modalHeading(__('messages.restore') . ' ' . __('messages.selected') . ' ' . __('messages.comment.comments'))
                        ->successNotificationTitle(__('messages.placeholder.comment_restored_successfully')),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->tooltip(__('messages.force_delete'))
                        ->modalHeading(__('messages.force_delete') . ' ' . __('messages.selected') . ' ' . __('messages.comment.comments'))
                        ->successNotificationTitle(__('messages.placeholder.comment_force_deleted_successfully')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePostComments::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.comments');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.comments');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_comment');
    }
}
