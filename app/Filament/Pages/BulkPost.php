<?php

namespace App\Filament\Pages;

use App\Enums\Sidebar;
use App\Models\Language;
use App\Models\Post;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Forms\Components\Markdown;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BulkPostExport;
use App\Http\Middleware\CheckPaddingSubscription;
use App\Imports\BulkPostImport;
use App\Models\Plan;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Redirect;

class BulkPost extends Page
{
    public array $data = [
        'bulk_post' => null,
    ];
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static string $view = 'filament.pages.bulk-post';

    protected static ?int $navigationSort = Sidebar::BULK_POST->value;

    public static function getNavigationLabel(): string
    {
        return __('messages.bulk_post.bulk_post');
    }

    public function getTitle(): string
    {
        return __('messages.bulk_post.bulk_post_upload');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermissionTo('manage_all_post');
    }

    protected static string|array $routeMiddleware = [
        CheckPaddingSubscription::class,
    ];

    public function mount()
    {
        if (Auth::user()->hasRole('customer')) {

            $count = Post::whereCreatedBy(getLogInUserId())->count();
            $invoiceLimit = currentActiveSubscription()->no_of_post;
            $frequency = currentActiveSubscription()->plan_frequency;
            if ($invoiceLimit <= $count && ($frequency != Plan::UNLIMITED)) {
                Notification::make()
                    ->danger()
                    ->title(__('messages.placeholder.your_plan_is_expired_Please_choose_a_plan_to_continue_the_services'))
                    ->send();

                return Redirect::to(url()->previous());
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(Post::getBulkPostForm())->statePath('data');
    }

    public function save()
    {

        $formState = $this->form->getState();
        $filePath = $formState['bulk_post'] ?? null;
        try {
            if ($filePath && Storage::disk('local')->exists($filePath)) {
                $import = new BulkPostImport();
                $path = Storage::disk('local')->path($filePath);
                $excel = Excel::import($import, $path, null, \Maatwebsite\Excel\Excel::CSV);
                Storage::disk('local')->delete($filePath);
                $errors = $import->getErrors();
                if ($errors) {
                    foreach ($errors as $key => $error) {
                        if ($error instanceof MessageBag) {
                            foreach ($error->all() as $message) {
                                Notification::make()
                                    ->danger()
                                    ->title($message)
                                    ->send();
                                    return redirect()->route('filament.admin.pages.bulk-post');

                            }
                        } else {
                            foreach ($error as $key => $value) {
                                Notification::make()
                                    ->danger()
                                    ->title($value)
                                    ->send();
                                    return redirect()->route('filament.admin.pages.bulk-post');

                            }
                        }
                    }
                } else {
                    Notification::make()
                        ->success()
                        ->title(__('messages.bulk_post.bulk_post_upload'))
                        ->send();
                        return redirect()->route('filament.admin.pages.bulk-post');

                    if (auth()->user()->hasRole('customer')) {
                        return redirect()->route('filament.customer.resources.posts.index');
                    } else {
                        return redirect()->route('filament.admin.resources.posts.index');
                    }
                }
            } else {
                Notification::make()
                    ->danger()
                    ->title('The uploaded file is not valid.', $formState)
                    ->send();
                    return redirect()->route('filament.admin.pages.bulk-post');

            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title($e->getMessage())
                ->send();
            return redirect()->route('filament.admin.pages.bulk-post');
        }
    }
}
