<?php

namespace App\Filament\Resources\PostsResource\Pages;

use App\Filament\Resources\PostsResource;
use Filament\Resources\Pages\Page;

class PostFormat extends Page
{
    protected static string $resource = PostsResource::class;

    protected static string $view = 'filament.resources.posts-resource.pages.post-format';

    public function getTitle(): string
    {
        return __('messages.post.choose_post_format');
    }
}
