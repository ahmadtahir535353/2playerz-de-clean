<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
@foreach($articles as $article)
    @php
        // Make absolute URL for each article:
        $loc = route('detailPage', $article->slug);
        // Use scheduled_post_time if exists, otherwise created_at as published date
        $publishDate = $article->scheduled_post_time ?? $article->created_at;
        // Ensure date is not in future and format properly
        $now = \Carbon\Carbon::now();
        if ($publishDate && $publishDate->gt($now)) {
            // If future date, use current time instead
            $publishDate = $now;
        }
        $pubDate = optional($publishDate)->toIso8601String();
    @endphp
    <url>
        <loc>{{ htmlspecialchars($loc, ENT_XML1, 'UTF-8') }}</loc>
        <news:news>
            <news:publication>
                <news:name>{{ htmlspecialchars($publicationName, ENT_XML1, 'UTF-8') }}</news:name>
                <news:language>{{ htmlspecialchars($language, ENT_XML1, 'UTF-8') }}</news:language>
            </news:publication>
            <news:publication_date>{{ $pubDate }}</news:publication_date>
            <news:title><![CDATA[{{ $article->title }}]]></news:title>
        </news:news>
    </url>
@endforeach
</urlset>

