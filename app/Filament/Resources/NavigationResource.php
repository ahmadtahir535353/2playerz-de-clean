<?php

namespace App\Filament\Resources;

use App\Enums\Sidebar;
use App\Filament\Resources\NavigationResource\Pages;
use App\Filament\Resources\NavigationResource\RelationManagers;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Models\Category;
use App\Models\Language;
use App\Models\Navigation;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class NavigationResource extends Resource
{
    protected static ?string $model = Navigation::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = Sidebar::NAVIGATION->value;

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
        $table = $table->modifyQueryUsing(function ($query) {
            $query->with(['navigationable'])
                ->whereHas('navigationable', function ($q) {
                    $q->where('show_in_menu', 1)
                        ->where(function ($query) {
                            $query->where('lang_id', Language::where('iso_code', Session::get('languageChange'))->first()->id ?? 1)
                                ->orWhereNull('lang_id');
                        });
                })
                ->whereNull('parent_id')
                ->orderBy('order_id');
        });

        return $table
            ->columns([
                ViewColumn::make('navigationable')
                    ->view('filament.tables.columns.navigation-menu')
            ])
            ->defaultSort('order_id')
            ->reorderable('order_id')
            ->filters([]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNavigations::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.navigation');
    }

    public static function getmodelLabel(): string
    {
        return __('messages.navigation');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermissionTo('manage_navigation');
    }
}
