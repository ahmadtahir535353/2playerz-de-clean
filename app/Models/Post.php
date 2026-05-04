<?php

namespace App\Models;

use App\Exports\BulkPostExport;
use App\Filament\Resources\PostsResource\Pages\CreatePosts;
use App\Filament\Resources\PostsResource\Pages\EditPosts;
use App\Scopes\AuthoriseUserActivePostScope;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Jobs\PublishScheduledPost;

use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Events\LiveTickerUpdated;
use App\Events\LiveTickerDeleted;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;

use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $keywords
 * @property int $visibility
 * @property int $featured
 * @property int $breaking
 * @property int $slider
 * @property int $recommended
 * @property int $show_on_headline
 * @property int $show_registered_user
 * @property string|null $optional_url
 * @property string $tags
 * @property int $post_types
 * @property int $lang_id
 * @property int $category_id
 * @property int|null $sub_category_id
 * @property int $scheduled_post
 * @property string|null $scheduled_post_time
 * @property int $status
 * @property string|null $rss_link
 * @property int $is_rss
 * @property int|null $rss_id
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Collection|Comment[] $comment
 * @property-read int|null $comment_count
 * @property-read array $additional_image
 * @property-read array $post_file
 * @property-read array $post_file_name
 * @property-read string $post_image
 * @property-read mixed $type_name
 * @property-read Language $language
 * @property-read MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read PostArticle|null $postArticle
 * @property-read Collection|PostGallery[] $postGalleries
 * @property-read int|null $post_galleries_count
 * @property-read Collection|PostSortList[] $postSortLists
 * @property-read int|null $post_sort_lists_count
 * @property-read SubCategory|null $subCategory
 * @property-read User $user
 *
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereBreaking($value)
 * @method static Builder|Post whereCategoryId($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereCreatedBy($value)
 * @method static Builder|Post whereDescription($value)
 * @method static Builder|Post whereFeatured($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereIsRss($value)
 * @method static Builder|Post whereKeywords($value)
 * @method static Builder|Post whereLangId($value)
 * @method static Builder|Post whereOptionalUrl($value)
 * @method static Builder|Post wherePostTypes($value)
 * @method static Builder|Post whereRecommended($value)
 * @method static Builder|Post whereRssId($value)
 * @method static Builder|Post whereRssLink($value)
 * @method static Builder|Post whereScheduledPost($value)
 * @method static Builder|Post whereScheduledPostTime($value)
 * @method static Builder|Post whereShowOnHeadline($value)
 * @method static Builder|Post whereShowRegisteredUser($value)
 * @method static Builder|Post whereSlider($value)
 * @method static Builder|Post whereSlug($value)
 * @method static Builder|Post whereStatus($value)
 * @method static Builder|Post whereSubCategoryId($value)
 * @method static Builder|Post whereTags($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @method static Builder|Post whereVisibility($value)
 *
 * @mixin Eloquent
 *
 * @property-read mixed $uploaded_video
 * @property-read PostVideo|null $postVideo
 * @property Carbon|null $scheduled_delete_post_time
 * @property-read Collection|PostReactionEmoji[] $PostReaction
 * @property-read int|null $post_reaction_count
 * @property-read PostAudio|null $postAudios
 * @property-read RssFeed|null $rssFeed
 *
 * @method static Builder|Post whereScheduledDeletePostTime($value)
 */
class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'posts';

    protected $fillable = [
        'created_by',
        'title',
        'slug',
        'description',
        'focus_keyword',
        'keywords',
        'visibility',
        'featured',
        'breaking',
        'slider',
        'recommended',
        'show_registered_user',
        'tags',
        'optional_url',
        'additional_images ',
        'files',
        'lang_id',
        'category_id',
        'sub_category_id',
        'scheduled_post',
        'scheduled_post_time',
        'published_at',
        'status',
        'post_types',
        'section',
        'show_on_headline',
        'rss_link',
        'is_rss',
        'rss_id',
        'scheduled_delete_post_time',
        'scheduled_post_delete',
        'is_default',
        'comment_enabled',
        'views_count',
        'image_copyright',
        'created_at'
    ];

    protected $casts = [
        'created_by' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'focus_keyword' => 'string',
        'keywords' => 'string',
        'visibility' => 'integer',
        'featured' => 'integer',
        'breaking' => 'integer',
        'slider' => 'integer',
        'recommended' => 'integer',
        'show_registered_user' => 'integer',
        // 'tags' => 'array',
        'optional_url' => 'string',
        'lang_id' => 'integer',
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'scheduled_post' => 'integer',
        'scheduled_post_time' => 'datetime',
        'scheduled_delete_post_time' => 'datetime',
        'status' => 'integer',
        'post_types' => 'integer',
        'show_on_headline' => 'integer',
        'is_rss' => 'boolean',
        'rss_id' => 'integer',
        'rss_link' => 'string',
        'scheduled_post_delete' => 'integer',
        'comment_enabled' => 'boolean',
    ];

    const IMAGE_POST = 'post image';

    const FILE_POST = 'post file';

    const ADDITIONAL_IMAGES = 'additional images';

    const AUDIOS_POST = 'post_audios';

    const VISIBILITY_ACTIVE = 1;

    const VISIBILITY_DEACTIVE = 0;

    const SHOW_REGISTRED_USER_ACTIVE = 1;

    const SHOW_REGISTRED_USER_DEACTIVE = 0;

    const RECOMMENDED_ACTIVE = 1;

    const RECOMMENDED_DEACTIVE = 0;

    const STATUS_ACTIVE = 1;

    const STATUS_DRAFT = 0;
    const PUBLISHED = 1;
    const ALL = 2;

    const POST = [
        //  self::ALL => 'ALL',
        self::STATUS_DRAFT => 'Drafts',
        self::PUBLISHED => 'Published',
    ];
    const MAX = 'desc';
    const MIN = 'asc';
    const VIEWS = [
        //  self::ALL => 'ALL',
        self::MAX => 'Max',
        self::MIN => 'Min',
    ];
    const FEATURED_ACTIVE = 1;

    const FEATURED_DEACTIVE = 0;

    const RSS_POST = 1;

    const NOT_RSS_POST = 0;

    const HEADLINE_ACTIVE = 1;

    const HEADLINE_DEACTIVE = 0;

    const BREAKING_ACTIVE = 1;

    const BREAKING_DEACTIVE = 0;

    const SLIDER_ACTIVE = 1;

    const SLIDER_DEACTIVE = 0;

    const ARTICLE = 'article';
    const LIVETICKER = 'liveticker';
    const GALLERY = 'gallery';

    const SORT_LIST = 'sort_list';

    const OPEN_AI = 'open_ai';

    const TRIVIA_QUIZ = 'trivia_quiz';

    const PERSONALITY_QUIZ = 'personality_quiz';

    const VIDEO = 'video';

    const AI = 'AI';

    const AUDIO = 'audio';

    const POST_FORMAT = 'post_format';

    const OPEN_AI_CREATE = 'openAi/create';

    const ARTICLE_CREATE = 'article/create';

    const GALLERY_CREATE = 'gallery/create';

    const SORT_LIST_CREATE = 'sort_list/create';

    const TRIVIA_QUIZ_CREATE = 'trivia_quiz/create';

    const PERSONALITY_QUIZ_CREATE = 'personality_quiz/create';

    const VIDEO_CREATE = 'video/create';

    const AUDIO_CREATE = 'audio/create';
    const LIVETICKER_CREATE = 'liveticker/create';
    const ADD_ARTICLE = 'add_article';

    const ADD_AI = 'add_ai';

    const ADD_GALLERY = 'add_gallery';

    const ADD_AUDIO = 'add_audio';
    const ADD_LIVETICKER = 'add_liveticker';
    const ADD_VIDEO = 'add_video';

    const ADD_TRIVIA_QUIZE = 'add_trivia_quiz';

    const ADD_PERSONALITY_QUIZ = 'add_personality_quiz';

    const ADD_SORT_LIST = 'add_sort_list';

    const ARTICLE_TYPE_ACTIVE = 1;

    const GALLERY_TYPE_ACTIVE = 2;

    const SORTED_TYPE_ACTIVE = 3;

    const TRIVIA_TYPE_ACTIVE = 4;

    const PERSONALITY_TYPE_ACTIVE = 5;

    const VIDEO_TYPE_ACTIVE = 6;

    const AUDIO_TYPE_ACTIVE = 7;

    const POST_TYPE_DEACTIVA = 0;

    const OPEN_AI_ACTIVE = 8;

    const LIVETICKER_TYPE_ACTIVE = 9;

    const TYPE = [
        self::ARTICLE_TYPE_ACTIVE => 'Article',
        self::GALLERY_TYPE_ACTIVE => 'Gallery',
        self::SORTED_TYPE_ACTIVE => 'Sorted',
        self::VIDEO_TYPE_ACTIVE => 'Video',
        self::AUDIO_TYPE_ACTIVE => 'Audio',
        self::OPEN_AI_ACTIVE => 'AI',
        self::LIVETICKER_TYPE_ACTIVE => 'LiveTicker',
    ];

    const TEXT_DAVINCI_003 = 'text-davinci-003';

    const TEXT_CURIE_001 = 'text-curie-001';

    const TEXT_BABBAGE_001 = 'text-babbage-001';

    const TEXT_ADA_001 = 'text-ada-001';

    const TEXT_DAVINCI_002 = 'text-davinci-002';

    const TEXT_DAVINCI_001 = 'text-davinci-001';

    const DAVINCI_INSTRUCT_BETA = 'davinci-instruct-beta';

    const DAVINCI = 'davinci';

    const CURIE_INSTRUCT_BETA = 'curie-instruct-beta';

    const CURIE = 'curie';

    const BABBAGE = 'babbage';

    const ADA = 'ada';

    const CODE_DAVINCI_002 = 'code-davinci-002';

    const CODE_CUSHMAN_001 = 'code-cushman-001';

    const GPT_4 = 'gpt-4';

    const GPT_3 = 'gpt-3.5-turbo';

    const GPT_4_TURBO_PREVIEW = 'gpt-4-turbo-preview';

    const MODEL = [
        self::GPT_4 => 'gpt-4',
        self::GPT_3 => 'gpt-3.5-turbo',
        self::GPT_4_TURBO_PREVIEW => 'gpt-4-turbo-preview',
    ];

    const OFF = 1;

    const MOST_LIKELY = 2;

    const LEAST_LIKELY = 3;

    const FULL_SPECTRUN = 4;

    const SHOW_PROBABILITIES = [
        self::OFF => 'Off',
        self::MOST_LIKELY => 'Most Likely',
        self::LEAST_LIKELY => 'Least Likely',
        self::FULL_SPECTRUN => 'Full Spectrum',
    ];

    protected $with = ['media'];

    protected static function booted()
{
    static::saved(function ($post) {
        if ($post->scheduled_post == 1 && $post->scheduled_post_time) {
            $publishTime = \Carbon\Carbon::parse($post->scheduled_post_time);
            if ($publishTime->isFuture()) {
                
                PublishScheduledPost::dispatch($post->id)->delay($publishTime);
            } else {
                
                $post->update([
                    'status' => 1,
                    'published_at' => now(),
                ]);

                // Update user's last_seen_at and last_activity_at when scheduled post is published
                if ($post->visibility == 1) { // VISIBILITY_ACTIVE
                    $user = \App\Models\User::find($post->created_by);
                    if ($user) {
                        $user->update([
                            'last_seen_at' => now('Europe/Berlin'),
                            'last_activity_at' => now('Europe/Berlin')
                        ]);
                    }
                }
            }
        }
    });
}

protected static function handleRecordCreation(array $data): Model
{
    $data['status'] = $data['scheduled_post'] ? 2 : 0;
    $post = static::create($data);
    return $post;
}

protected static function handleRecordUpdate(Model $record, array $data): Model
{
    $data['status'] = $data['scheduled_post'] ? 2 : 0;
    $record->update($data);
    return $record;
}
    protected $appends = ['post_image', 'post_file', 'additional_image', 'type_name'];

    public function scopeSearchTitle(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term == '') {
            return $query;
        }
        // Replace any sequence of non-letters/digits with % (SQL wildcard)
        $pattern = '%' . preg_replace('/[^\p{L}\p{N}]+/u', '%', $term) . '%';
        // Collapse multiple % to one (defensive)
        $pattern = preg_replace('/%+/', '%', $pattern);
        return $query->where(function ($q) use ($pattern) {
            $q->where('title', 'like', $pattern)
              ->orWhere('description', 'like', $pattern);
        });
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    public function livetickerPosts()
    {
        return $this->hasMany(LivetickerPost::class)->orderBy('created_at', 'desc');
    }

    public function livetickerContent()
    {
        return $this->hasOne(LivetickerContent::class, 'post_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rssFeed(): BelongsTo
    {
        return $this->belongsTo(RssFeed::class, 'rss_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
   public function likes()
    {
        return $this->hasMany(Like::class, 'item_id');
    }

    public function analytics()
    {
        return $this->hasMany(Analytic::class);
    }

    /**
     * @var string[]
     */
    public static $rules = [
        'title' => 'required|max:190',
        'slug' => 'required|unique:posts,slug',
        'description' => 'required|regex:/^[^<>]+$/',
        'keywords' => 'required|max:190',
        'tags' => 'nullable',
        'lang_id' => 'required',
        'category_id' => 'required',
    ];

    public function getPostImageAttribute(): string
    {
        /** @var Media $media */
         $media = $this->getMedia(self::IMAGE_POST)->first();
        
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('front_web/images/default.jpg');
    }

    public function getPostFileAttribute(): array
    {
        /** @var Media $media */
        $medias = $this->getMedia(self::FILE_POST);
        $mediaUrl = [];
        foreach ($medias as $media) {
            if (! empty($media)) {
                $mediaUrl[] = $media->getFullUrl();
            } else {
                $mediaUrl = [asset('front_web/images/default.jpg')];
            }
        }

        return $mediaUrl;
    }

    public function getPostFileNameAttribute(): array
    {
        /** @var Media $media */
        $medias = $this->getMedia(self::FILE_POST);
        $mediaUrl = [];
        foreach ($medias as $media) {
            if (! empty($media)) {
                $mediaUrl[] = $media->file_name;
            }
        }

        return $mediaUrl;
    }

    public function getAdditionalImageAttribute(): array
    {
        /** @var Media $media */
        $medias = $this->getMedia(self::ADDITIONAL_IMAGES);
        $mediaUrl = [];
        foreach ($medias as $media) {
            if (! empty($media)) {
                $mediaUrl[] = $media->getFullUrl();
            } else {
                $mediaUrl = [asset('front_web/images/default.jpg')];
            }
        }

        return $mediaUrl;
    }

    public function getTypeNameAttribute($value): string
    {
        return self::TYPE[$this->post_types];
    }

    public function postArticle(): HasOne
    {
        return $this->hasOne(PostArticle::class);
    }

    public function postGalleries(): HasMany
    {
        return $this->hasMany(PostGallery::class);
    }

    // public function postGalleries()
    // {
    //     return $this->hasMany(PostGallery::class);
    // }

    // // For a single gallery
    // public function postGallery()
    // {
    //     return $this->hasOne(PostGallery::class);
    // }

    public function postSortLists(): HasMany
    {
        return $this->hasMany(PostSortList::class);
    }

    public function postVideo(): HasOne
    {
        return $this->hasOne(PostVideo::class);
    }

    public function postMultipleVideos(): HasMany
    {
        return $this->hasMany(PostVideo::class);
    }

    public function postAudios(): HasOne
    {
        return $this->hasOne(PostAudio::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AuthoriseUserActivePostScope());

        static::addGlobalScope(new LanguageScope());

        static::addGlobalScope(new PostDraftScope());

        static::deleting(function ($post) {
            $post->livetickerContent()?->delete();

            $post->livetickerPosts()->delete();
        });
    }

    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function PostReaction()
    {
        return $this->hasMany(PostReactionEmoji::class, 'post_id', 'id');
    }

    public static function getForm()
    {

        return [
            Section::make()
                ->schema([

                    Hidden::make('section')
                        ->default(function (Request $request) {
                            return $request->query('section');
                        })
                        ->dehydrated(false),
                    Hidden::make('post_types')
                        ->label('Post Types')
                        ->default(function (Get $get) {
                            if (request()->query('section') === 'article') {
                                return Post::ARTICLE_TYPE_ACTIVE;
                            } elseif (request()->query('section') === 'video') {
                                return Post::VIDEO_TYPE_ACTIVE;
                            } elseif (request()->query('section') === 'gallery') {
                                return Post::GALLERY_TYPE_ACTIVE;
                            } elseif (request()->query('section') === 'open_ai') {
                                return Post::OPEN_AI_ACTIVE;
                            } elseif (request()->query('section') === 'sort_list') {
                                return Post::SORTED_TYPE_ACTIVE;
                            } elseif (request()->query('section') === 'audio') {
                                return Post::AUDIO_TYPE_ACTIVE;
                            }elseif (request()->query('section') === 'liveticker') {
                                return Post::LIVETICKER_TYPE_ACTIVE;
                            }
                        }),
                    TextInput::make('title')
                        ->unique(ignorable: fn(?Post $record) => $record)
                        ->label(__('messages.common.title') . ':')
                        ->validationAttribute(__('messages.common.title'))
                        ->placeholder(__('messages.common.title'))
                        ->required()
                        ->maxLength(255)
                        ->live(debounce: 500)
                        ->autofocus()
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state) {
                            if ($operation == 'edit') {
                                $set('slug', Str::slug($state));
                            }
                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),
                    Hidden::make('ai_filters')
                        ->dehydrated(false),

                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make(__('messages.ai.generate_content'))
                            ->action(function (Forms\Get $get, Forms\Set $set) {
                                $postTitle = $get('title') ?? '';
                                $openAiModel = $get('ai_filters')['openai'] ?? 'gpt-4';
                                $Temperature = $get('ai_filters')['temperature'] ?? 0;
                                $MaximumLength = $get('ai_filters')['maximum_length'] ?? 500;
                                $InputTopPId = $get('ai_filters')['input_top'] ?? 0;
                                $InputBestOfId = $get('ai_filters')['input_best_of'] ?? 0;
                                $openAiKey = Setting::where('key', 'open_AI_key')->value('value');
                                if ($postTitle == null) {
                                    return Notification::make()
                                        ->danger()
                                        ->title(__('messages.bulk_post.title_required'))
                                        ->send();
                                }
                                if (empty($openAiKey)) {
                                    if (empty(config('services.open_ai.open_ai_key'))) {
                                        return Notification::make()
                                            ->danger()
                                            ->title(__('messages.placeholder.please_enter_open_ai_key_in_setting'))
                                            ->send();
                                    }
                                    $openAiKey = config('services.open_ai.open_ai_key');
                                }


                                try {
                                    $response = \Illuminate\Support\Facades\Http::withToken($openAiKey)
                                        ->withHeaders(['Content-Type' => 'application/json'])
                                        ->timeout(60) // Increase the timeout to 60 seconds
                                        ->retry(3, 1000) // Retry up to 3 times with a 1-second delay between attempts
                                        ->post('https://api.openai.com/v1/chat/completions', [
                                            'model' => $openAiModel,
                                            'messages' => [
                                                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                                                ['role' => 'user', 'content' => $postTitle],
                                            ],
                                            'temperature' => (float) $Temperature,
                                            'max_tokens' => (int) $MaximumLength,
                                            'top_p' => (float) $InputTopPId,
                                        ]);

                                    if ($response->successful()) {
                                        $content = $response->json()['choices'][0]['message']['content'] ?? '';
                                        $set('postArticle.article_content', $content);
                                    } else {
                                        throw new \Exception('Failed to fetch data from OpenAI API');
                                    }
                                } catch (\Exception $e) {
                                    // Handle the exception (you could log it, display a message, etc.)
                                    // For example:
                                    Log::error('Error fetching data from OpenAI API: ' . $e->getMessage());
                                }
                            }),
                        Forms\Components\Actions\Action::make('filter')
                            ->hiddenLabel()
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->modalHeading(__('messages.ai.ai_filter'))
                            ->modalWidth('sm')
                            ->action(function (Forms\Get $get, Forms\Set $set, $data) {
                                $set('ai_filters', $data);
                            })
                            ->slideOver()
                            ->form(function (Forms\Get $get) {
                                $filter = $get('ai_filters');
                                return [
                                    Select::make('openai')
                                        ->label(__('messages.ai.model'))
                                        ->options(\App\Models\Post::MODEL)
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['openai'])) ? $filter['openai'] : \App\Models\Post::GPT_4;
                                        })
                                        ->searchable()
                                        ->native(false),

                                    TextInput::make('temperature')
                                        ->label(__('messages.ai.temperature'))
                                        ->type('range')
                                        ->reactive()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(1)
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['temperature'])) ? $filter['temperature'] : 0;
                                        })
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $set('temperature_input_display', number_format($state, 2));
                                        }),

                                    Placeholder::make('temperature_input_display')
                                        ->label('')
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['temperature'])) ? $filter['temperature'] : 0;
                                        })
                                        ->content(fn(Get $get) => $get('temperature_input_display')),

                                    TextInput::make('maximum_length')
                                        ->label(__('messages.ai.maximum_length'))
                                        ->type('range')
                                        ->reactive()
                                        ->numeric()
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['maximum_length'])) ? $filter['maximum_length'] : 500;
                                        })
                                        ->step(1)
                                        ->minValue(1)
                                        ->maxValue(4000)
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $set('maximum_length_input_display', $state);
                                        }),

                                    Placeholder::make('maximum_length_input_display')
                                        ->label('')
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['maximum_length'])) ? $filter['maximum_length'] : 500;
                                        })
                                        ->content(fn(Get $get) => $get('maximum_length_input_display')),

                                    TextInput::make('input_top')
                                        ->label(__('messages.ai.top_p'))
                                        ->type('range')
                                        ->reactive()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(1)
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['input_top'])) ? $filter['input_top'] : 0;
                                        })
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $set('input_top_input_display', number_format($state, 2));
                                        }),

                                    Placeholder::make('input_top_input_display')
                                        ->label('')
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['input_top'])) ? $filter['input_top'] : 0;
                                        })
                                        ->content(fn(Get $get) => $get('input_top_input_display')),

                                    TextInput::make('input_best_of')
                                        ->label(__('messages.ai.best_of'))
                                        ->type('range')
                                        ->reactive()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(1)
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['input_best_of'])) ? $filter['input_best_of'] : 0;
                                        })
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $set('input_best_of_input_display', number_format($state, 2));
                                        }),

                                    Placeholder::make('input_best_of_input_display')
                                        ->label('')
                                        ->default(function (Get $get) use ($filter) {
                                            return (!empty($filter) && !empty($filter['input_best_of'])) ? $filter['input_best_of'] : 0;
                                        })
                                        ->content(fn(Get $get) => $get('input_best_of_input_display')),
                                ];
                            }),
                    ])
                        // ->hidden(fn (Get $get) => $get('section') !== 'open_ai'),
                        ->hidden(fn(Get $get) => ($get('section') ?? request()->query('section')) !== 'open_ai'),


                    TextInput::make('slug')
                        ->label(__('messages.common.slug') . ':')
                        ->validationAttribute(__('messages.common.slug'))
                        ->placeholder(__('messages.common.slug'))
                        ->required()
                        ->readOnly()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label(__('messages.post.short_description') . ':')
                        ->validationAttribute(__('messages.post.short_description'))
                        ->placeholder(__('messages.post.short_description'))
                        ->required()
                        ->rows(3)
                        ->maxLength(255),

                        TextInput::make('focus_keyword')
                        ->label(__('messages.post.focus_keyword') ?? 'Focus Keyword')
                        ->placeholder(__('messages.post.focus_keyword_placeholder') ?? 'Enter focus keyword for SEO analysis')
                        ->helperText(__('messages.post.focus_keyword_helper') ?? 'The main keyword you want to optimize for')
                        ->maxLength(255)
                        ->live(debounce: 500),

                    // SEO Analysis Box
                    \App\Filament\Forms\Components\SeoBox::make('seo_analysis')
                        ->label(__('messages.other_lang.seo_analysis.title'))
                        ->fieldNames([
                            'title' => 'title',
                            'content' => 'description',
                            'seo_title' => 'title',
                            'seo_description' => 'description',
                            'focus_keyword' => 'focus_keyword',
                        ])
                        ->columnSpan('full'),

                    TextInput::make('keywords')
                        ->label(__('messages.post.keywords') . ':')
                        ->validationAttribute(__('messages.post.keywords'))
                        ->placeholder(__('messages.post.keywords'))
                        ->required()
                        ->maxLength(255),
                    Toggle::make('visibility')
                        ->label(__('messages.post.visibility') . ':')
                        ->validationAttribute(__('messages.post.visibility'))
                        ->inlineLabel(true),
                    Toggle::make('featured')
                        ->label(__('messages.post.add_to_featured') . ':')
                        ->validationAttribute(__('messages.post.add_to_featured'))
                        ->inlineLabel(true),
                    Toggle::make('show_on_headline')
                        ->label(__('messages.post.add_on_headline') . ':')
                        ->validationAttribute(__('messages.post.add_on_headline'))
                        ->inlineLabel(true),
                    Toggle::make('breaking')
                        ->label(__('messages.post.add_to_breaking') . ':')
                        ->validationAttribute(__('messages.post.add_to_breaking'))
                        ->inlineLabel(true),
                    Toggle::make('slider')
                        ->label(__('messages.post.add_to_slider') . ':')
                        ->validationAttribute(__('messages.post.add_to_slider'))
                        ->inlineLabel(true),
                    Toggle::make('recommended')
                        ->label(__('messages.post.add_to_recommended') . ':')
                        ->validationAttribute(__('messages.post.add_to_recommended'))
                        ->inlineLabel(true),
                    Toggle::make('show_registered_user')
                        ->label(__('messages.post.show_registered_user') . ':')
                        ->validationAttribute(__('messages.post.show_registered_user'))
                        ->inlineLabel(true),
                    Toggle::make('comment_enabled')
                        ->label(__('messages.post.enable_comments') . ':')
                        ->validationAttribute(__('messages.post.enable_comments'))
                        ->inlineLabel(true)
                        ->default(true)
                        ->helperText(__('messages.post.comment_helper_text')),
                    // Textarea::make('tags')
                    //     ->label('Tags')
                    //     ->rows(2)
                    //     ->maxLength(255),
                    TagsInput::make('tags')
                        ->label(__('messages.post.tag') . ':')
                        ->validationAttribute(__('messages.post.tag'))
                        ->placeholder(__('messages.post.tag'))
                        ->reactive(),
                        
                    TextInput::make('optional_url')
                        ->label(__('messages.post.optional_url') . ':')
                        ->validationAttribute(__('messages.post.optional_url'))
                        ->placeholder(__('messages.post.optional_url'))
                        ->maxLength(255)
                        ->url(),
                   Section::make('')
                        ->relationship('postArticle')
                        ->schema([
                            \AmidEsfahani\FilamentTinyEditor\TinyEditor::make('article_content')
                                ->label(__('messages.post.article_content') . ':')
                                ->validationAttribute(__('messages.post.article_content'))
                                ->placeholder(__('messages.post.article_content'))
                                ->profile('custom') // Custom profile use karo
                                ->columnSpan('full'),
                        ])
                        ->hidden(fn(Get $get) => ($get('section') ?? request()->query('section')) !== 'article' && ($get('section') ?? request()->query('section')) !== 'open_ai'),

                   Section::make('Liveticker')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('liveticker_title')
                            ->label(__('messages.other_lang.liveticker_title'))
                            ->placeholder(__('messages.other_lang.liveticker_title_placeholder'))
                            ->maxLength(255)
                            ->afterStateHydrated(function (Set $set, ?Model $record) {
                                if ($record) {
                                    $set('liveticker_title', $record->livetickerContent->title ?? '');
                                }
                            })
                            ->dehydrateStateUsing(function ($state, ?Model $record) {
                                if ($record) {
                                    $record->livetickerContent()->updateOrCreate(
                                        [],
                                        ['title' => $state]
                                    );
                                }
                                return $state;
                            })
                            ->afterStateUpdated(function ($state, $component, ?Model $record) {
                                \Log::info('Liveticker title updated:', [
                                    'record_id' => $record?->id,
                                    'state'     => $state,
                                ]);
                            }),
                            Toggle::make('live_indicator_enabled')
                                ->label(__('messages.other_lang.live_indicator_enabled')) // e.g. “Show LIVE red dot”
                                ->helperText(__('messages.other_lang.helper_text_live_indicator_enabled'))
                                ->live()
                                ->afterStateHydrated(function (Set $set, ?Model $record) {
                                    if ($record) $set('live_indicator_enabled', (bool) optional($record->livetickerContent)->live_indicator_enabled);
                                })
                                ->dehydrateStateUsing(function ($state, ?Model $record) {
                                    if ($record) {
                                        $record->livetickerContent()->updateOrCreate([], [
                                            'live_indicator_enabled' => (bool) $state,
                                        ]);
                                    }
                                    return $state;
                                }),

                            // (c) LIVE dot until (datetime)
                           DateTimePicker::make('live_indicator_until')
                            ->label(__('messages.other_lang.live_indicator_until'))
                            ->seconds(false)
                            ->minutesStep(1)
                            ->timezone('Europe/Berlin')      // <-- pick & show in German time
                            ->displayFormat('d.m.Y H:i')     // optional, German-style
                            ->visible(fn (Get $get) => (bool) $get('live_indicator_enabled'))
                            ->required(fn (Get $get) => (bool) $get('live_indicator_enabled'))
                            ->rule('after:now')
                            // show value back to the form as Berlin time:
                            ->afterStateHydrated(function (Set $set, ?Model $record) {
                                if ($record && optional($record->livetickerContent)->live_indicator_until) {
                                    $set('live_indicator_until',
                                        $record->livetickerContent->live_indicator_until->clone()->setTimezone('Europe/Berlin')
                                    );
                                }
                            })
                            // store as UTC in DB:
                            ->dehydrateStateUsing(function ($state, ?Model $record) {
                                if ($record) {
                                    // $berlin = Carbon::parse($state, 'Europe/Berlin');
                                    // $utc    = $berlin->clone()->setTimezone('UTC');
                                    $record->livetickerContent()->updateOrCreate([], [
                                        'live_indicator_until' => $state,
                                    ]);
                                }
                                return $state;
                            }),
                        // Header editor
                        \AmidEsfahani\FilamentTinyEditor\TinyEditor::make('liveticker_header')
                            ->label(__('messages.other_lang.liveticker_header'))
                            ->profile('custom')
                            ->afterStateHydrated(function (Set $set, ?Model $record) {
                                if ($record) {
                                    $set('liveticker_header', $record->livetickerContent->header ?? '');
                                }
                            })
                            ->dehydrateStateUsing(function ($state, ?Model $record) {
                                if ($record) {
                                    $record->livetickerContent()->updateOrCreate(
                                        [], 
                                        ['header' => $state]
                                    );
                                }
                                return $state;
                            })
                            ->afterStateUpdated(function ($state, $component, ?Model $record) {
                                \Log::info('Header updated:', [
                                    'record_id' => $record?->id,
                                    'state'     => $state, 
                                ]);
                            })
                            ->columnSpan('full'),


                        // Repeater of liveticker posts
                        Repeater::make('livetickerPosts')
                            ->label(__('messages.other_lang.liveticker_posts'))
                            ->relationship('livetickerPosts')
                            ->deleteAction(function (\Filament\Forms\Components\Actions\Action $action) {
                                    return $action->before(function (array $arguments, \Filament\Forms\Components\Repeater $component) {
                                        $itemIndex = $arguments['item'] ?? null;
                                        if ($itemIndex === null) {
                                            return;
                                        }

                                        // Read the state of this repeater item
                                        $itemState = $component->getItemState($itemIndex);
                                        $childId = $itemState['id'] ?? null;

                                        if (! $childId) {
                                            return;
                                        }

                                        // Get parent record from Livewire
                                        $livewire = $component->getLivewire();
                                        $parent = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : ($livewire->record ?? null);

                                        if (! $parent) {
                                            return;
                                        }

                                        // Delete child from DB
                                        $child = $parent->livetickerPosts()->find($childId);
                                        if ($child) {
                                            $child->delete();
                                            event(new \App\Events\LiveTickerDeleted($parent->id, $childId));
                                        }
                                    });
                                })
                            ->schema([
                                Hidden::make('id')->reactive(),
                                Hidden::make('__id'),

                                \AmidEsfahani\FilamentTinyEditor\TinyEditor::make('content')
                                    ->label(__('messages.other_lang.live_update'))
                                    ->profile('custom')
                                    ->columnSpan('full'),
                                    
                                    Actions::make([
                                        Action::make('saveUpdate')
                                            ->label(__('messages.other_lang.save_this_update'))
                                            ->action(function (Get $get, Set $set, $livewire) {
                                                $parent = $livewire->getRecord();
                                                if (! $parent) {
                                                    Notification::make()
                                                        ->title(__('messages.other_lang.parent_record_missing'))
                                                        ->danger()
                                                        ->send();
                                                    return;
                                                }

                                                $id = $get('id');
                                                $content = $get('content');

                                                if ($id) {
                                                    // UPDATE existing post
                                                    $child = $parent->livetickerPosts()->find($id);
                                                    if ($child) {
                                                        $child->update(['content' => $content]);
                                                        event(new LiveTickerUpdated($parent->id, $child->id));
                                                    }
                                                } else {
                                                    // CREATE new post
                                                    $child = $parent->livetickerPosts()->create([
                                                        'content' => $content,
                                                    ]);
                                                    event(new LiveTickerUpdated($parent->id, $child->id));
                                                    // ✅ update current repeater item's id
                                                    $set('id', $child->getKey());
                                                }

                                                Notification::make()
                                                    ->title(__('messages.other_lang.saved_title'))
                                                    ->body(__('messages.other_lang.notification_body'))
                                                    ->success()
                                                    ->send();
                                            }),
                                        ])->hiddenOn('create')->columnSpan('full'),
                                    ])
                                ->orderable(false)
                                ->collapsible()
                                ->createItemButtonLabel(__('messages.other_lang.live_update_button')),

                        
                            \AmidEsfahani\FilamentTinyEditor\TinyEditor::make('liveticker_footer')
                                ->label(__('messages.other_lang.liveticker_footer'))
                                ->profile('custom')
                                ->afterStateHydrated(function (Set $set, ?Model $record) {
                                    if ($record) {
                                        $set('liveticker_footer', $record->livetickerContent->footer ?? '');
                                    }
                                })
                                ->dehydrateStateUsing(function ($state, ?Model $record) {
                                    if ($record) {
                                        $record->livetickerContent()->updateOrCreate(
                                            [], 
                                            ['footer' => $state]
                                        );
                                    }
                                    return $state;
                                })
                                ->afterStateUpdated(function ($state, $component, ?Model $record) {
                                    \Log::info('Footer updated:', [
                                        'record_id' => $record?->id,
                                        'state'     => $state, 
                                    ]);
                                })
                                ->columnSpan('full'),

                        ])
                    ->hidden(fn (Get $get) => ($get('section') ?? request()->query('section')) !== 'liveticker'),

                    Section::make('')
                        ->relationship('postAudios')
                        ->schema([
                            RichEditor::make('audio_content')
                                ->label(__('messages.post.audio_content') . ':')
                                ->validationAttribute(__('messages.post.audio_content'))
                                ->placeholder(__('messages.post.audio_content')),
                        ])->hidden(fn(Get $get) => ($get('section') ?? request()->query('section')) !== 'audio'),

                    Section::make('')
                        ->visible(function (Request $request, $operation, Set $set, Get $get) {
                            if ($operation == 'edit' && $get('section') == null) {
                                $set('section', $request->query('section'));
                            }
                            return $get('section') == 'gallery' ? true : false;
                        })
                        ->schema([
                            Repeater::make('postGalleries')
                                ->relationship('postGalleries')
                                ->label(__('messages.post.gallery_post_item'))
                                ->collapsible(true)
                                ->columns(3)
                                ->schema([
                                    Hidden::make('id'),
                                    TextInput::make('gallery_title')
                                        ->label(__('messages.common.title') . ':')
                                        ->validationAttribute(__('messages.common.title'))
                                        ->placeholder(__('messages.common.title'))
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    Group::make()->schema([
                                        SpatieMediaLibraryFileUpload::make('gallery_images')
                                            ->label(__('messages.post.image') . ':')
                                            ->collection(PostGallery::IMAGES),
                                        TextInput::make('image_description')
                                            ->label(__('messages.post.image_description') . ':')
                                            ->validationAttribute(__('messages.post.image_description'))
                                            ->placeholder(__('messages.post.image_description'))
                                            ->maxLength(255),
                                    ]),
                                    RichEditor::make('gallery_content')
                                        ->label('')
                                        ->placeholder(__('messages.post.gallery_content'))
                                        ->columnSpan(2),
                                ]),
                        ]),

                    Section::make('')
                        ->visible(function (Request $request, $operation, Set $set, Get $get) {
                            if ($operation == 'edit' && $get('section') == null) {
                                $set('section', $request->query('section'));
                            }
                            return $get('section') == 'sort_list' ? true : false;
                        })
                        ->schema([
                            Repeater::make('postSortLists')
                                ->relationship('postSortLists')
                                ->label(__('messages.post.sort_list_item'))
                                ->collapsible(true)
                                ->columns(3)
                                ->schema([
                                    Hidden::make('id'),
                                    TextInput::make('sort_list_title')
                                        ->label(__('messages.common.title') . ':')
                                        ->validationAttribute(__('messages.common.title'))
                                        ->placeholder(__('messages.common.title'))
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    Group::make()->schema([
                                        SpatieMediaLibraryFileUpload::make('sorted_list_image')
                                            ->label(__('messages.post.image'))
                                            ->collection(PostSortList::IMAGES),
                                        TextInput::make('image_description')
                                            ->label(__('messages.post.image_description') . ':')
                                            ->validationAttribute(__('messages.post.image_description'))
                                            ->placeholder(__('messages.post.image_description'))
                                            ->maxLength(255),
                                    ]),
                                    RichEditor::make('sort_list_content')
                                        ->label(__('messages.post.sort_list_content') . ':')
                                        ->validationAttribute(__('messages.post.sort_list_content'))
                                        ->placeholder(__('messages.post.sort_list_content'))
                                        ->required()
                                        ->columnSpan(2),
                                ]),
                            // ->hidden(fn (Get $get) => $get('section') !== 'sort_list'),

                        ]),

                ])->columnSpan(2)->columns(1),
                        
            Group::make()->schema([
                



                Section::make()
                    ->relationship('postVideo')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('thumbnailImage')
                            ->label(__('messages.post.video_thumbnail') . ':')
                            ->validationAttribute(__('messages.post.video_thumbnail'))
                            // ->required()
                            ->required(function (Get $get, Set $set, Request $request, $operation) {
                                if ($operation == 'edit') {
                                    $tab = EditPosts::$tab;
                                    if ($get('uploadVideo') ==  null) {
                                        $set('thumbnailImage', null);
                                    }
                                } else {
                                    $tab = CreatePosts::$tab;
                                }
                                if ($tab == 1) {
                                    $set('active_tab', 1);
                                }
                                return $tab || $get('active_tab') == 1;
                            })
                            ->collection(PostVideo::THUMBNAIL_PATH)
                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                if (!empty($state)) {
                                    $set('thumbnail_image_url', null);
                                }
                            }),
                        SpatieMediaLibraryFileUpload::make('uploadVideo')
                            ->collection(PostVideo::VIDEO_PATH)
                            ->disk(config('app.media_disk'))
                            ->label(__('messages.post.upload_video') . ':')
                            ->validationAttribute(__('messages.post.upload_video'))
                            ->acceptedFileTypes(['video/mp4', 'video/ogg', 'video/webm'])
                            ->rules('file|max:50000|mimes:mp4,ogg,webm,avi')
                            ->hidden()
                            ->dehydrated(false),
                        TextInput::make('thumbnail_image_url')
                            ->label(__('messages.post.or_add_url') . ' ' . __('messages.post.allowed_images_jpg_png_jpeg') . ':')
                            ->validationAttribute(__('messages.post.or_add_url') . ' ' . __('messages.post.allowed_images_jpg_png_jpeg'))
                            ->placeholder(__('messages.post.or_add_url') . ' ' . __('messages.post.allowed_images_jpg_png_jpeg'))
                            ->autocomplete()
                            ->url()
                            ->rule('regex:/^https?:\/\/.*\.(jpg|jpeg|png)$/i', __('messages.validation.invalid_image_url'))
                            ->live()
                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                if (!preg_match('/\.(jpeg|jpg|png)$/i', $state)) {
                                    $set('thumbnailImageView', null);
                                    return;
                                }
                                $response = Http::get($state);
                                if (!empty($state)) {
                                    $set('thumbnailImage', null);
                                }
                                if ($response->successful()) {
                                    $temporaryFilePath = 'temporary/' . basename($state);
                                    Storage::disk('local')->put($temporaryFilePath, $response->body());
                                    $temporaryFileFullPath = Storage::disk('local')->path($temporaryFilePath);
                                    $set('thumbnailImageView', Storage::url($temporaryFilePath));
                                } else {
                                    $set('thumbnailImageView', null);
                                }
                            }),
                        ViewField::make('thumbnailImageView')
                            ->label('Thumbnail Image Preview')
                            ->view('components.thumbnail-image')

                    ])
                    // ->hidden(fn (Get $get) => $get('section') !== 'video'),
                    ->hidden(fn(Get $get) => ($get('section') ?? request()->query('section')) !== 'video'),


                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('messages.post.image'))
                            ->required()
                            ->image()
                            ->collection(Post::IMAGE_POST)
                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, ?Post $record) {
                                if (!empty($state) && $record) {
                                    // Delete old main image from database and storage
                                    $oldMedia = $record->getMedia(Post::IMAGE_POST)->first();
                                    if ($oldMedia) {
                                        $oldMedia->delete(); // Deletes from database and storage
                                    }
                                }
                            }),
                        TextInput::make('image_copyright')
                            ->label(__('messages.other_lang.image_copyright_label'))
                            ->placeholder(__('messages.other_lang.copyright_placeholder'))
                            ->helperText(__('messages.other_lang.image_copyright_helper'))
                            ->maxLength(255),
                    ])
                    ->hidden(fn(Get $get) => ($get('section') ?? request()->query('section')) == 'video'),

                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('additional_images')
                            ->label(__('messages.post.additional'))
                            ->multiple()
                            ->image()
                            ->collection(Post::ADDITIONAL_IMAGES)
                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, ?Post $record) {
                                if (!empty($state) && $record) {
                                    // Delete all old additional images from database and storage
                                    $oldMedia = $record->getMedia(Post::ADDITIONAL_IMAGES);
                                    foreach ($oldMedia as $media) {
                                        $media->delete(); // Deletes from database and storage
                                    }
                                }
                            }),
                    ])
                    // ->hidden(fn (Get $get) => $get('section') !== 'article'),
                    ->hidden(fn(Get $get) => ($get('section') ?? request()->query('section')) !== 'article'),

                Section::make()
                    ->relationship('postAudios')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('audios')
                            ->label(__('messages.post.audio'))
                            ->multiple()
                            ->required()
                            ->acceptedFileTypes(['audio/*'])
                            ->collection(PostAudio::AUDIOS_POST),
                    ])->hidden(fn(Get $get) =>  $get('section') !== 'audio'),

                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('file')
                            ->label(__('messages.post.file'))
                            ->multiple()
                            // Prevent uploading executable scripts (e.g. .php) via "file" attachments
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/zip',
                                'application/x-zip-compressed',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'text/plain',
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->rules(['file', 'max:20480', 'mimes:pdf,zip,doc,docx,xls,xlsx,txt,jpg,jpeg,png,webp'])
                            ->collection(Post::FILE_POST),
                    ])
                    // ->hidden(fn (Get $get) => $get('section') !== 'article' && $get('section') !== 'video' && $get('section') !== 'audio'),
                    ->hidden(fn(Get $get) => ($get('section') ?? request()->query('section')) !== 'article' && ($get('section') ?? request()->query('section')) !== 'video' && ($get('section') ?? request()->query('section')) !== 'audio'),


                Section::make()
                    ->schema([
                        Select::make('created_by')
                            ->label(__('messages.common.created_by') . ':')
                            ->validationAttribute(__('messages.common.created_by'))
                            ->placeholder(__('messages.common.created_by'))
                            ->options(
                                User::where('type', User::STAFF)
                                    ->where('is_editor', 1)
                                    ->get()
                                    ->mapWithKeys(fn($user) => [$user->id => $user->first_name ?? '(No name)']) 
                            )
                            ->searchable()
                            ->required(fn () => auth()->user()->id === User::ADMIN)
                            ->hidden(function (Get $get) {
                                return auth()->user()->id !== User::ADMIN;
                            })
                            ->formatStateUsing(function ($state) {
                                return $state == 1 ? null : $state; 
                            }),
                         
                        Select::make('lang_id')
                            ->label(__('messages.page.language') . ':')
                            ->validationAttribute(__('messages.page.language'))
                            ->placeholder(__('messages.page.add_lang'))
                            ->options(getLanguage())
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('category_id', null);
                                $set('sub_category_id', null);
                            }),
                        Select::make('category_id')
                            ->label(__('messages.post.category') . ':')
                            ->validationAttribute(__('messages.post.category'))
                            ->placeholder(__('messages.common.select_category'))
                            ->options(function (Get $get) {
                                return Category::query()
                                    ->where('lang_id', $get('lang_id'))
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('sub_category_id', null);
                            }),
                        Select::make('sub_category_id')
                            ->label(__('messages.post.sub_category') . ':')
                            ->validationAttribute(__('messages.post.sub_category'))
                            ->placeholder(__('messages.common.select_subcategory'))
                            ->options(function (Get $get) {
                                return SubCategory::query()
                                    ->where('parent_category_id', $get('category_id'))
                                    ->pluck('name', 'id');
                            })
                            ->searchable(),
                           DatePicker::make('created_at')
                                ->label(__('messages.other_lang.change_date') . ':')
                                ->validationAttribute(__('messages.other_lang.change_date'))
                                ->placeholder(__('messages.common.select_date'))
                                ->default(now())
                                ->required()
                                ->visible(function ($context) {
                                    return $context === 'edit'; 
                                }),
                        Checkbox::make('scheduled_post')
                            ->label(__('messages.post.publish') . ' ' . __('messages.post.scheduled_post') . ':')
                            ->validationAttribute(__('messages.post.scheduled_post'))
                            ->inlineLabel(true)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $set('status', $state ? 2 : 0); // 2 for scheduled, 0 for draft
                            }),
                        DateTimePicker::make('scheduled_post_time')
                            ->label('')
                            ->visible(fn($get) => $get('scheduled_post'))
                            ->requiredIf('scheduled_post', fn($get) => $get('scheduled_post'))
                            ->timezone('UTC') // Default UTC, user time zone se convert hoga model mein
                            ->default(now()),
       

                        Checkbox::make('scheduled_post_delete')
                            ->label(__('messages.delete') . ' ' . __('messages.post.scheduled_post') . ':')
                            ->validationAttribute(__('messages.post.scheduled_post'))
                            ->inlineLabel(true)
                            ->live(),
                        DatePicker::make('scheduled_delete_post_time')
                            ->label('')
                            ->visible(fn($get) => $get('scheduled_post_delete')),

                        Placeholder::make('')->content(__('messages.placeholder.clicking_on_submit_it_will_publish_your_post')),

                        Toggle::make('status')
                            ->label(__('messages.post.draft') . ':'),
                        // ->hidden($form->getOperation() == 'edit'),
                    ])
                    
                        ]),
                 Section::make('')
                    ->relationship('postVideo')
                    ->schema([
                        Tabs::make('urls')
                            ->extraAttributes(['wire:click' => 'demoClick'])
                            ->tabs([
                                Tab::make('url_video')
                                    ->label(__('messages.post.get_video'))
                                    ->schema([
                                        TextInput::make('video_url')
                                            ->label(__('messages.post.video_url') . ':')
                                            ->validationAttribute(__('messages.post.video_url'))
                                            ->placeholder(__('messages.post.video_url'))
                                            ->live()
                                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                                if (!empty($state)) {
                                                    $videoId = Str::of($state)->after('watch?v=')->before('&'); // Extract the video ID
                                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}?feature=oembed";
                                                    $set('video_embed_code', $embedUrl);
                                                    $set('thumbnail_image_url', "https://i.ytimg.com/vi/{$videoId}/hq720.jpg");
                                                } else {
                                                    $set('video_embed_code', null);
                                                    $set('thumbnail_image_url', null);
                                                }
                                            })
                                            ->required(function (Get $get, Set $set, Request $request, $operation) {
                                                if ($operation == 'edit') {
                                                    $tab = EditPosts::$tab;
                                                    if ($get('uploadVideo') !=  null) {
                                                        return false;
                                                    }
                                                } else {
                                                    $tab = CreatePosts::$tab;
                                                }
                                                if ($tab == 0 && $get('uploadVideo') == null) {
                                                    $set('active_tab', 0);
                                                }
                                                return $tab || $get('active_tab') == 0;
                                            })
                                            ->url(),

                                        TextInput::make('video_embed_code')
                                            ->label(__('messages.post.video_embed_code') . ':')
                                            ->validationAttribute(__('messages.post.video_embed_code'))
                                            ->placeholder(__('messages.post.video_embed_code'))
                                            // ->required()
                                            ->live()
                                            ->required(function (Get $get, Set $set, Request $request, $operation) {
                                                // return $request->query('tab') == "-get-video-from-url-tab";
                                                if ($operation == 'edit') {
                                                    $tab = EditPosts::$tab;
                                                    if ($get('uploadVideo') !=  null) {
                                                        return false;
                                                    }
                                                } else {
                                                    $tab = CreatePosts::$tab;
                                                }
                                                if ($tab == 0 && $get('uploadVideo') == null) {
                                                    $set('active_tab', 0);
                                                }
                                                return $tab || $get('active_tab') == 0;
                                            })
                                            ->readOnly()
                                            ->url(),

                                        ViewField::make('video_embed')
                                            ->view('components.video-embed')
                                            ->label('Video Preview'),
                                    ]),

                                Tab::make('upload_video')
                                    ->label(__('messages.post.upload_video'))
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('uploadVideo')
                                            ->collection(PostVideo::VIDEO_PATH)
                                            ->disk(config('app.media_disk'))
                                            ->label(__('messages.post.upload_video') . ':')
                                            ->validationAttribute(__('messages.post.upload_video'))
                                            ->acceptedFileTypes(['video/mp4', 'video/ogg', 'video/webm'])
                                            ->maxSize(50000)
                                            // ->required()
                                            ->required(function (Get $get, Set $set, Request $request, $operation) {
                                                if ($operation == 'edit') {
                                                    $tab = EditPosts::$tab;
                                                    // if($get('uploadVideo') !=  null){
                                                    //     return true
                                                    // }
                                                } else {
                                                    $tab = CreatePosts::$tab;
                                                }
                                                if ($tab == 1) {
                                                    $set('active_tab', 1);
                                                }
                                                return $tab || $get('active_tab') == 1;
                                            })
                                            ->hidden(function (Get $get, Set $set, Request $request, $operation) {
                                                if ($operation == 'edit') {
                                                    $tab = EditPosts::$tab;
                                                } else {
                                                    $tab = CreatePosts::$tab;
                                                }
                                                if ($tab == 1) {
                                                    $set('active_tab', 1);
                                                }
                                                return $tab && $get('active_tab') !== 1;
                                            }),
                                    ]),

                            ])
                            ->persistTabInQueryString()  // Persist active tab in query string
                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                // Update the active_tab field when the tab changes
                                // $set('active_tab', $state);
                            }),

                        // Hidden field to store the active tab state
                        Hidden::make('active_tab')
                            ->live()
                            ->reactive()
                            ->dehydrated(false),
                            \AmidEsfahani\FilamentTinyEditor\TinyEditor::make('video_content')
                                ->label(__('messages.post.video_content') . ':')
                                ->validationAttribute(__('messages.post.video_content'))
                                ->placeholder(__('messages.post.video_content'))
                                ->profile('custom') // optional: use a TinyMCE profile if defined
                                ->columnSpan('full'),
                    ])
                    ->hidden(fn(Get $get) => ($get('section') ?? request()->query('section')) !== 'video'),

            
                    ];
    }

    public static function getBulkPostForm()
    {
        return [
            Group::make()
                ->schema([
                    Section::make(__('messages.bulk_post.help_documents'))
                        ->description(__('messages.bulk_post.you_can_use_csv_file'))
                        ->columns(3)
                        ->schema([
                            Actions::make([
                                Action::make('Category ID List')
                                    ->label(__('messages.bulk_post.category_ids_list'))
                                    ->modalHeading(__('messages.bulk_post.category_ids_list'))
                                    ->modalSubmitAction(false)
                                    ->modalCancelAction(false)
                                    ->infolist(function () {
                                        $languages = Language::with('categories.subCategories')->get();

                                        $languageSection = [];
                                        $categorySection = [];
                                        $subCategorySection = [];

                                        foreach ($languages as $language) {

                                            foreach ($language->categories as $category) {

                                                foreach ($category->subCategories as $subCategory) {
                                                    $languageSection[] = TextEntry::make("language_{$language->id}")
                                                        ->label($language->name . ' ' . __('messages.bulk_post.id') . ' = ' . $language->id);
                                                    $categorySection[] = TextEntry::make("category_{$category->id}")
                                                        ->label($category->name . ' ' . __('messages.bulk_post.id') . ' = ' . $category->id);
                                                    $subCategorySection[] = TextEntry::make("subcategory_{$subCategory->id}")
                                                        ->label($subCategory->name . ' ' . __('messages.bulk_post.id') . ' = ' . $subCategory->id);
                                                }
                                            }
                                        }

                                        // Create a dummy Post model instance
                                        $dummyPost = new Post();

                                        return Infolist::make()
                                            ->record($dummyPost)
                                            ->schema([
                                                ComponentsSection::make()
                                                    ->heading(__('messages.bulk_post.languages'))
                                                    ->schema($languageSection)
                                                    ->columnSpan(1),
                                                ComponentsSection::make()
                                                    ->heading(__('messages.bulk_post.categories'))
                                                    ->schema($categorySection)
                                                    ->columnSpan(1),
                                                ComponentsSection::make()
                                                    ->heading(__('messages.bulk_post.sub_categories'))
                                                    ->schema($subCategorySection)
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(3);
                                    }),
                            ]),
                            Actions::make([
                                Action::make('Download csv Template')
                                    ->label(__('messages.bulk_post.download_csv_template'))
                                    ->action(function () {
                                        $users = [
                                            [
                                                'id' => 1,
                                                'name' => 'Hardik',
                                                'email' => 'hardik@gmail.com',
                                                'image' => 'https://cdn.ielts.net/wp-content/uploads/2024/09/space_exploration_science-66de93.webp',
                                                'lang_id' => 1,
                                                'category_id' => 1,
                                                'sub_category' => 4,
                                                'tag' => 'test',
                                                'visibility' => 1,
                                            ],
                                            [
                                                'id' => 2,
                                                'name' => 'Hardik',
                                                'email' => 'hardik@gmail.com',
                                                'image' => 'https://media.istockphoto.com/id/182062885/photo/space-station-in-earth-orbit.jpg?s=612x612&w=0&k=20&c=F_P2YJ3QDbSW2n6dWkh6JNYeQGI1-2q-wOBk9-sw_Xo=',
                                                'lang_id' => 1,
                                                'category_id' => 2,
                                                'sub_category' => 3,
                                                'tag' => 'test',
                                                'visibility' => 1,
                                            ],
                                            [
                                                'id' => 3,
                                                'name' => 'Hardik',
                                                'email' => 'hardik@gmail.com',
                                                'image' => 'https://media.istockphoto.com/id/1353874144/photo/astronaut-in-outer-space-spaceman-with-starry-and-galactic-background-sci-fi-digital-wallpaper.jpg?s=612x612&w=0&k=20&c=v66dk0cM4PWc0uOV7uVtaYWQv57deKvWjBBS6LgeHxQ=',
                                                'lang_id' => 2,
                                                'category_id' => 4,
                                                'sub_category' => 5,
                                                'tag' => 'test',
                                                'visibility' => 1,
                                            ],
                                        ];

                                        return Excel::download(new BulkPostExport($users), 'csv_template.csv');
                                    }),
                            ]),
                            Actions::make([
                                Action::make('Documentation')
                                    ->label(__('messages.bulk_post.documentation'))
                                    ->modalHeading(__('messages.bulk_post.documentation'))
                                    ->modalSubmitAction(false)
                                    ->modalCancelAction(false)
                                    ->infolist(function () {
                                        return Infolist::make()
                                            ->record(new Post())
                                            ->schema([
                                                ComponentsSection::make()
                                                    ->columns(3)
                                                    ->schema([
                                                        ComponentsGroup::make()
                                                            ->columnSpanFull()
                                                            ->schema([
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.menu.title'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.string'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                            <b>' . __('messages.required') . '</b> <br>
                                                                            <span>' . __('messages.bulk_post.example') . ': Test Title </span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.post.description'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.longText'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                    <b>' . __('messages.required') . '</b> <br>
                                                                    <span>' . __('messages.bulk_post.example') . ': Test description About this post </span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.post.keywords'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.string'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                    <b>' . __('messages.required') . '</b> <br>
                                                                    <span>' . __('messages.bulk_post.example') . ': examination, careful, goals </span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.post.image'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.string'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                    <b>' . __('messages.required') . '</b> <br>
                                                                    <span>' . __('messages.bulk_post.example') . ': https://infynews.nyc3.digitaloceanspaces.com/post%20image/608/oxford-1.jpg </span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.bulk_post.langid'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.integer'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                                    <b>' . __('messages.required') . '</b> <br>
                                                                                    <span>' . __('messages.bulk_post.example') . ': 1 </span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.bulk_post.categoryid'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.integer'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                                    <b>' . __('messages.required') . '</b> <br>
                                                                                    <span>' . __('messages.bulk_post.example') . ': 1 </span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.bulk_post.subcategoryid'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.integer'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                <b>' . __('messages.bulk_post.Optional') . '</b> <br>
                                                                <span>' . __('messages.bulk_post.example') . ': 1 </span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.common.tags'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.string'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                    <b>' . __('messages.required') . '</b> <br>
                                                                    <span>' . __('messages.bulk_post.example') . ': advantages, power </span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),
                                                                ComponentsSection::make()
                                                                    ->columns(3)
                                                                    ->schema([
                                                                        TextEntry::make(__('messages.post.visibility'))->columns(1),
                                                                        TextEntry::make(__('messages.bulk_post.data_type') . ':' . __('messages.bulk_post.boolean'))
                                                                            ->default(function (Post $record = null) {
                                                                                $html = '
                                                                    <b>' . __('messages.required') . '</b> <br>
                                                                    <span>1 OR 0</span>';
                                                                                return new HtmlString($html);
                                                                            })->columns(2)->columnSpan(2),
                                                                    ]),

                                                            ])
                                                    ]),
                                            ]);
                                    }),
                            ]),
                            FileUpload::make('bulk_post')
                                ->label(__('messages.bulk_post.upload_csv_File') . ':')
                                ->validationAttribute(__('messages.bulk_post.upload_csv_File'))
                                ->disk('public')
                                ->acceptedFileTypes([
                                    'text/csv',
                                    'text/plain',
                                    'application/csv',
                                    'application/vnd.ms-excel',
                                ])
                                ->rules(['file', 'max:20480', 'mimes:csv,txt'])
                                ->required()
                                ->columnSpanFull(),
                        ])
                ])->columns(3),
        ];
    }
}
