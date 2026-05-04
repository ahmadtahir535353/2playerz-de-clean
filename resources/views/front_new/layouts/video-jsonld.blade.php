@php
    use Illuminate\Support\Str;

    // Get video/post data
    $video = $postDetail ?? $post ?? null;
    
    if (!$video || !isset($video->postVideo)) {
        return; // Exit if no video found
    }

    $orgName   = '2Playerz';
    $logoUrl   = asset('uploads/logo/504/01JTHQA9R70QTFSFVR2SA469E3-old.png');
    $pageUrl   = route('detailPage', $video->slug);
    
    // Get video thumbnail
    $imageUrl = null;
    if (!empty($video->postVideo->thumbnail_image_url)) {
        $imageUrl = $video->postVideo->thumbnail_image_url;
    } elseif (!empty($video->postVideo->uploaded_thumb)) {
        $imageUrl = $video->postVideo->uploaded_thumb;
    } elseif (!empty($video->post_image)) {
        $imageUrl = $video->post_image;
    }

    // Ensure image URL is absolute
    if ($imageUrl && !Str::startsWith($imageUrl, ['http://', 'https://'])) {
        $imageUrl = asset($imageUrl);
    }

    // Get video URL/embed
    $videoUrl = $video->postVideo->video_url ?? null;
    $embedUrl = $video->postVideo->video_embed_code ?? $video->postVideo->video_content ?? null;
    
    // Extract YouTube video ID if embed URL exists
    $youtubeId = null;
    if ($embedUrl) {
        if (preg_match('/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/', $embedUrl, $matches)) {
            $youtubeId = $matches[1];
            $videoUrl = $videoUrl ?? "https://www.youtube.com/watch?v={$youtubeId}";
        } elseif (preg_match('/youtu\.be\/([A-Za-z0-9_-]{11})/', $embedUrl, $matches)) {
            $youtubeId = $matches[1];
            $videoUrl = $videoUrl ?? "https://www.youtube.com/watch?v={$youtubeId}";
        }
    }

    // Description fallback
    $desc = $video->description 
        ? strip_tags($video->description)
        : Str::limit($video->title ?? '', 160);

    // Get author information
    $authorName = $video->user->full_name ?? ($video->user->username ?? 'Staff');
    $authorUrl = isset($video->user) && $video->user->id 
        ? route('user.public.profile', $video->user->username ?? $video->user->id)
        : url('/');

    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'VideoObject',
        'name' => Str::limit($video->title ?? '', 110),
        'description' => $desc,
        'thumbnailUrl' => $imageUrl,
        'uploadDate' => optional($video->published_at)->toIso8601String() ?? optional($video->created_at)->toIso8601String(),
        'contentUrl' => $videoUrl,
        'embedUrl' => $embedUrl,
        'author' => [
            '@type' => 'Person',
            'name'  => $authorName,
            'url'   => $authorUrl,
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name'  => $orgName,
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => $logoUrl,
            ],
        ],
    ];

    // Add mainEntityOfPage
    $data['mainEntityOfPage'] = [
        '@type' => 'WebPage',
        '@id'   => $pageUrl,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
</script>

