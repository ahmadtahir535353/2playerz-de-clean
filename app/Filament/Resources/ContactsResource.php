<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\ContactsResource\Pages;
use App\Filament\Resources\ContactsResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Contact;
use App\Models\Contacts;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction as TablesExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ContactsResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?int $navigationSort = Sidebar::CONTACTS->value;

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction(false)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.user.full_name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('messages.emails.email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('messages.phone'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth('lg')->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(__('messages.delete') . ' ' . __('messages.common.contact'))
                    ->successNotificationTitle(__('messages.placeholder.contact_deleted_successfully')),
            ])
            ->headerActions([
                TablesExportBulkAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('name'),
                        Column::make('email'),
                        Column::make('phone'),
                    ])->withWriterType(\Maatwebsite\Excel\Excel::CSV),
                ]),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->actionsAlignment(function () {
                return Session::get('locale') == 'ar' ? 'left' : 'right';
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->tooltip(__('messages.delete'))
                        ->modalHeading(__('messages.delete') . ' ' . __('messages.selected') . ' ' . __('messages.contacts'))
                        ->successNotificationTitle(__('messages.placeholder.contact_deleted_successfully')),
                ]),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                TextEntry::make('name')->label(__('messages.user.full_name')),
                TextEntry::make('email')->label(__('messages.emails.email')),
                TextEntry::make('phone')->label(__('messages.phone')),
                TextEntry::make('message')->label(__('messages.emails.message')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContacts::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.contacts');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.contacts');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_contacts');
    }
}
