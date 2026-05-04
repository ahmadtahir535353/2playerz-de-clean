@php
    use Illuminate\Support\Str;

    // Get article/post data - adjust variable name if needed
    $article = $postDetail ?? $post ?? null;
    
    if (!$article) {
        return; // Exit if no article found
    }

    // Show for News category articles and Liveticker posts ONLY
    $isNewsCategory = false;
    $isLiveticker = false;
    
    // Check if it's a News category article
    if (isset($article->category)) {
        $categoryName = strtolower(trim($article->category->name ?? ''));
        $categorySlug = strtolower(trim($article->category->slug ?? ''));
        $isNewsCategory = ($categoryName === 'news' || $categorySlug === 'news');
    }
    
    // Check if it's a liveticker post (post_types == 9)
    if (isset($article->post_types)) {
        $isLiveticker = ($article->post_types == \App\Models\Post::LIVETICKER_TYPE_ACTIVE);
    }
    
    // Only show NewsArticle schema for News category articles or Liveticker posts
    // Exit if it's neither News nor Liveticker
    if (!$isNewsCategory && !$isLiveticker) {
        return; // Exit - don't show NewsArticle schema for non-news articles
    }

    $orgName   = '2Playerz'; // Organization name
    $logoUrl   = asset('uploads/logo/504/01JTHQA9R70QTFSFVR2SA469E3-old.png'); // Logo path from your project
    $pageUrl   = route('detailPage', $article->slug);
    $imageUrl  = $article->post_image ?? null;

    // Ensure image URL is absolute
    if ($imageUrl && !Str::startsWith($imageUrl, ['http://', 'https://'])) {
        $imageUrl = asset($imageUrl);
    }

    // Description fallback - use description field or excerpt from article_content
    $desc = $article->description 
        ? strip_tags($article->description)
        : ($article->postArticle->article_content ?? null 
            ? Str::limit(strip_tags($article->postArticle->article_content), 160)
            : Str::limit($article->title ?? '', 160));

    // Get author information
    $authorName = $article->user->full_name ?? ($article->user->username ?? 'Staff');
    $authorUrl = isset($article->user) && $article->user->id 
        ? route('user.public.profile', $article->user->username ?? $article->user->id)
        : url('/');

    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'NewsArticle',
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id'   => $pageUrl,
        ],
        'headline' => Str::limit($article->title ?? '', 110), // Google recommends <=110
        // Multiple images are allowed; keep at least one ≥1200px wide if possible
        'image'    => $imageUrl ? [$imageUrl] : [],
        'datePublished' => optional($article->published_at)->toIso8601String() ?? optional($article->created_at)->toIso8601String(),
        'dateModified'  => optional($article->updated_at ?: $article->published_at ?: $article->created_at)->toIso8601String(),
        'author' => [
            [
                '@type' => 'Person',
                'name'  => $authorName,
                'url'   => $authorUrl,
            ],
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name'  => $orgName,
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => $logoUrl,
            ],
        ],
        'description' => $desc,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
</script>

