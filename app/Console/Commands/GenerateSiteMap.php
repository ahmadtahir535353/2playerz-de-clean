<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Scopes\LanguageScope;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url as SitemapUrl;

class GenerateSiteMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sitemap = Sitemap::create()
            ->add( SitemapUrl::create(url('/'))
                    ->setLastModificationDate(now()));

        $posts = Post::withoutGlobalScope(LanguageScope::class)
            ->where('status', '=', 1)->whereVisibility(1)
            ->get(['slug', 'updated_at']);
        foreach ($posts as $post) {
            $sitemap->add(SitemapUrl::create(url('/p/' . $post->slug))
            ->setLastModificationDate($post->updated_at));
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $content = file_get_contents(public_path('robots.txt'));

        $content = str_replace('{{URL}}', URL::to('/'), $content);

        file_put_contents(public_path('robots.txt'), $content);

        return Command::SUCCESS;
    }
}
