<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentReportResource\Pages;
use App\Filament\Resources\CommentReportResource\RelationManagers;
use App\Models\CommentReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CommentReportResource extends Resource
{
    protected static ?string $model = CommentReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('messages.comment.comment_reports');
    }

    public static function getNavigationBadge(): ?string
    {
        // Show only unread (not viewed) reports count
        $count = static::getModel()::whereNull('viewed_at')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        // Badge color based on unread reports count
        $count = static::getModel()::whereNull('viewed_at')->count();
        
        if ($count > 10) {
            return 'danger'; // Red for many reports
        } elseif ($count > 5) {
            return 'warning'; // Orange for several reports
        } elseif ($count > 0) {
            return 'info'; // Blue for few reports
        }
        
        return null;
    }

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
            $query = $query->with(['post', 'reportedBy', 'reportedUser', 'comment']);
        });

        return $table
            ->defaultSort('id', 'desc')
            ->recordClasses(fn ($record) => $record->viewed_at === null ? 'bg-yellow-50 dark:bg-yellow-900/10 border-l-4 border-yellow-500' : null)
            ->columns([
                TextColumn::make('post.title')
                    ->label(__('messages.post.posts'))
                    ->limit(50)
                    ->sortable()
                    ->searchable()
                    ->url(function ($record) {
                        return route('detailPage', [$record->post->slug ?? '']);
                    })
                    ->openUrlInNewTab(),
                TextColumn::make('reportedBy.username')
                    ->label(__('messages.staff.username'))
                    ->sortable()
                    ->searchable()
                    ->url(function ($record) {
                        return route('user.public.profile', $record->reportedBy->username ?? $record->reported_by_user_id);
                    })
                    ->openUrlInNewTab(),
                TextColumn::make('report_reason')
                    ->label(__('messages.comment.report_reason_label'))
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('reportedUser.username')
                    ->label(__('messages.comment.reported_user'))
                    ->sortable()
                    ->searchable()
                    ->url(function ($record) {
                        return route('user.public.profile', $record->reportedUser->username ?? $record->reported_user_id);
                    })
                    ->openUrlInNewTab(),
                TextColumn::make('comment.comment')
                    ->label(__('messages.comment.reported_comment'))
                    ->limit(50)
                    ->wrap()
                    ->url(function ($record) {
                        $baseUrl = route('detailPage', [$record->post->slug ?? '']);
                        return $baseUrl . '#comment-' . $record->comment_id;
                    })
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.comment.comment_reports'))
                    ->successNotificationTitle(__('messages.comment.comment_report_deleted_successfully')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCommentReports::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('messages.comment.comment_reports');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_comment');
    }
}
