<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Post;

class ProxyController extends Controller
{
    public function fetchUrl(Request $request)
    {
        $url = $request->input('url');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }

        // 1) Fast DB shortcut for /p/{slug}
        if (Str::contains($url, '2playerz.de') && Str::contains($url, '/p/')) {
            $parts = array_values(array_filter(explode('/', parse_url($url, PHP_URL_PATH))));
            $slug  = end($parts);

            $post = Post::where('slug', $slug)
                ->with(['postMultipleVideos', 'postVideo', 'postVideo.media', 'media'])
                ->first();

            if ($post) {
                // Handle VIDEO_TYPE_ACTIVE posts
                if ($post->post_types == \App\Models\Post::VIDEO_TYPE_ACTIVE && $post->postVideo) {
                    $thumbnail = null;
                    
                    // Priority 1: thumbnail_image_url
                    if (!empty($post->postVideo->thumbnail_image_url)) {
                        $thumbnail = $post->postVideo->thumbnail_image_url;
                    }
                    // Priority 2: uploaded_thumb
                    elseif (!empty($post->postVideo->uploaded_thumb)) {
                        $thumbnail = $post->postVideo->uploaded_thumb;
                    }
                    // Priority 3: Extract from video_content if YouTube
                    elseif (!empty($post->postVideo->video_content)) {
                        if (preg_match('/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/', $post->postVideo->video_content, $m)) {
                            $thumbnail = $this->getYoutubeThumbnail($m[1]);
                        } elseif (preg_match('/youtu\.be\/([A-Za-z0-9_-]{11})/', $post->postVideo->video_content, $m)) {
                            $thumbnail = $this->getYoutubeThumbnail($m[1]);
                        }
                    }
                    // Priority 4: post_image as fallback
                    if (!$thumbnail && !empty($post->post_image)) {
                        $thumbnail = $post->post_image;
                    }

                    return response()->json([
                        'title'       => trim($post->title) ?: 'No Title',
                        'image'       => $thumbnail ?: asset('front_web/images/default.jpg'),
                        'description' => Str::limit(strip_tags($post->description), 190),
                        'video_embed' => $post->postVideo->video_content ?? '',
                    ]);
                }
                
                // Handle old postMultipleVideos system
                if ($post->postMultipleVideos && $post->postMultipleVideos->isNotEmpty()) {
                    $code = $post->postMultipleVideos->first()->video_embed_code ?? '';

                    if (preg_match('/([A-Za-z0-9_-]{11})/', $code, $m)) {
                        $id = $m[1];

                        return response()->json([
                            'title'       => trim($post->title) ?: 'No Title',
                            'image'       => $this->getYoutubeThumbnail($id),
                            'description' => Str::limit(
                                strip_tags(
                                    $post->postMultipleVideos->first()->video_content
                                    ?: $post->description
                                ),
                                190
                            ),
                            'video_embed' => $code,
                        ]);
                    }
                }
                
                // Handle regular article posts (and other post types)
                $image = null;
                if (!empty($post->post_image)) {
                    $image = $post->post_image;
                } elseif ($post->media && $post->media->isNotEmpty()) {
                    $image = $post->media->first()->getUrl();
                }

                return response()->json([
                    'title'       => trim($post->title) ?: 'No Title',
                    'image'       => $image ?: asset('front_web/images/default.jpg'),
                    'description' => Str::limit(strip_tags($post->description), 190),
                    'video_embed' => '',
                ]);
            }
        }

        // 2) Generic scraper
        try {
            $html   = Http::get($url)->body();
            $parser = new \DOMDocument();
            @$parser->loadHTML($html);

            // <title>
            $titleNodes = $parser->getElementsByTagName('title');
            $title      = $titleNodes->length ? $titleNodes->item(0)->nodeValue : 'No Title';

            // meta tags
            $image = '';
            $desc  = '';
            foreach ($parser->getElementsByTagName('meta') as $m) {
                if ($m->getAttribute('property') === 'og:image') {
                    $image = $m->getAttribute('content');
                }
                if ($m->getAttribute('property') === 'og:description') {
                    $desc = $m->getAttribute('content');
                }
            }

            // iframe → video embed
            $videoEmbed = '';
            foreach ($parser->getElementsByTagName('iframe') as $iframe) {
                $src = $iframe->getAttribute('src');
                if (Str::contains($src, ['youtube.com', 'youtu.be', 'vimeo.com', 'player.vimeo.com'])) {
                    $videoEmbed = $parser->saveHTML($iframe);
                    break;
                }
            }

            // fallback image
            if (!$image && $parser->getElementsByTagName('img')->length) {
                $image = $parser->getElementsByTagName('img')->item(0)->getAttribute('src');
            }
            if (!$desc && $parser->getElementsByTagName('p')->length) {
                $desc = $parser->getElementsByTagName('p')->item(0)->nodeValue;
            }

            return response()->json([
                'title'       => $title,
                'image'       => $image ?: '',
                'description' => Str::limit(trim($desc), 190) ?: 'No Description',
                'video_embed' => $videoEmbed,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to fetch URL'], 500);
        }
    }
    
    private function getYoutubeThumbnail($id)
    {
        
        $thumbs = [
            "https://i.ytimg.com/vi_webp/{$id}/maxresdefault.webp",
            "https://i.ytimg.com/vi/{$id}/maxresdefault.jpg",
            "https://i.ytimg.com/vi/{$id}/sddefault.jpg",
            "https://i.ytimg.com/vi/{$id}/hqdefault.jpg",
        ];
    
        foreach ($thumbs as $thumb) {
            if (@getimagesize($thumb)) {
                return $thumb;
            }
        }
    
        
        return "https://2playerz.de/front_web/images/default.jpg";
    }

}
