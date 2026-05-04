<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Exports\NewsLetterExporter;
use App\Filament\Resources\NewsLetterResource\Pages;
use App\Filament\Resources\NewsLetterResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\BulkMail;
use App\Models\NewsLetter;
use App\Models\Subscriber;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction as TablesExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class NewsLetterResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?int $navigationSort = Sidebar::NEWS_LATTERS->value;

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
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('email')->searchable()->label(__('messages.emails.email')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.news_letters'))
                    ->successNotificationTitle(__('messages.placeholder.subscriber_delete_successfully')),
            ])
            ->headerActions([
                TablesExportBulkAction::make()
                ->exports([
                    ExcelExport::make()
                    ->withColumns([
                        Column::make('email'),
                    ])->withWriterType(\Maatwebsite\Excel\Excel::CSV),
                ])->label(__('messages.export')),
                BulkAction::make(__('messages.send_mail'))
                    ->form([
                        TextInput::make('subject')
                            ->label(__('messages.mails.mail_subject') . ':')
                            ->required()
                            ->maxLength(255),
                        RichEditor::make('body')
                            ->label(__('messages.mails.mail_content') . ':')
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        foreach ($livewire->selectedTableRecords as $key => $value) {
                            $subscriberemail = Subscriber::find($value)->email;
                            BulkMail::create([
                                'email' => $subscriberemail,
                                'body' => $data['body'],
                                'subject' => $data['subject'],
                            ]);
                        }
                    })
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.news_letters'))
                        ->successNotificationTitle(__('messages.placeholder.subscriber_delete_successfully')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNewsLetters::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.news_letters');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.news_letters');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_news_letter');
    }
}
