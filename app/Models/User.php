<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\UserEmailVerification;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid; 
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements FilamentUser, HasName, HasMedia, MustVerifyEmail, HasAvatar
{
    use HasFactory, Notifiable, InteractsWithMedia, HasRoles;

    protected $table = 'users';

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact',
        'dob',
        'gender',
        'status',
        'password',
        'language',
        'dark_mode',
        'blood_group',
        'type',
        'email_verified_at',
        'about_us',
        'username',
        'last_seen_at',
        'last_activity_at',
        'is_default',
        'favorite_game',
        'interest',
        'city',
        'hardware',
        'last_seen_at',
        'psn_id',
        'xbox_live_id',
        'is_editor', 
        'is_moderator',
        'password_plain',
        'fcm_token',
        'who_can_send_messages',
        'message_notification_preference',
        'visitor_count',
        'comment_points',
    ];

    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string',
        'password' => 'hashed',
        'dob' => 'date',
        'contact' => 'string',
        'gender' => 'integer',
        'status' => 'integer',
        'dark_mode' => 'integer',
        'type' => 'integer',
        'remember_token' => 'string',
        'language' => 'string',
        'blood_group' => 'string',
        'last_seen_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'password_plain' => 'string',
        'fcm_token' => 'string',
    ];

    const PROFILE = 'profile';

    const COVER_IMG = 'cover_img';

    const NEWS_IMAGE = 'news-image';

    const ADMIN = 1;

    const STAFF = 2;

    const TYPE = [
        self::ADMIN => 'Admin',
        self::STAFF => 'Staff',
    ];

    protected $with = ['media'];

    protected $appends = ['full_name', 'profile_image', 'role_name', 'cover_image'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    const MALE = 1;

    const FEMALE = 2;

    const GENDER = [
        self::MALE => 'Male',
        self::FEMALE => 'Female',
    ];

    public static $rules = [
        'first_name' => 'required|max:190',
        'last_name' => 'required|max:190',
        'email' => 'required|max:160|email:filter|unique:users,email',
        'password' => 'required|same:password_confirmation|min:6|max:190',
        'dob' => 'nullable|date',
        'contact' => 'required|numeric',
        'experience' => 'nullable|numeric',
        'specializations' => 'required',
        'gender' => 'required',
        'status' => 'nullable'
    ];

    public static function getGenderType(): array
    {
        $genderType[self::MALE] = Lang::get('messages.staff.' . self::MALE);

        $genderType[self::FEMALE] = Lang::get('messages.staff.' . self::FEMALE);

        return $genderType ?? [];
    }

    public function getProfileImageAttribute(): string
    {
        /** @var Media $media */
        $media = $this->getMedia(self::PROFILE)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('assets/image/avatar.png');
    }

    public function getProfileUrlAttribute()
    {
        if ($this->hasMedia(self::PROFILE)) {
            return $this->getFirstMediaUrl(self::PROFILE);
        }

        return asset('assets/image/avatar.png');
    }

    public function getCoverImageAttribute()
    {
        /** @var Media $media */
        $media = $this->getMedia(self::COVER_IMG)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('assets/image/post-image/post-17.jpg');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(self::PROFILE);
    }

    public function getRoleNameAttribute()
    {
        $role = $this->roles()->first();

        if (! empty($role)) {
            return $role->display_name;
        }
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'id', 'user_id')
            ->where('status', Subscription::ACTIVE);
    }

    // public function getLevelAttribute(): string
    // {
    //     $points = $this->comment_points;

    //     if ($points >= 10000) return __('messages.other_lang.legend');
    //     if ($points >= 5000) return __('messages.other_lang.pro');
    //     if ($points >= 1000) return __('messages.other_lang.intermediate');
    //     if ($points >= 100) return __('messages.other_lang.beginner');

    //     return 'Newbie';
    // }
    public function getLevelAttribute(): string
    {
        $points = $this->comment_points;

        return \App\Models\PlayerzLevel::where('min_points', '<=', $points)
                    ->orderByDesc('min_points')
                    ->first()
                    ->name ?? 'Newbie';
    }

    public function getLevelObjectAttribute()
    {
        $points = $this->comment_points ?? 0;
        
        // If points are 0 or less, return the lowest level (Newbie with min_points = 1)
        if ($points <= 0) {
            return \App\Models\PlayerzLevel::where('min_points', '<=', 1)
                        ->orderByDesc('min_points')
                        ->first();
        }

        return \App\Models\PlayerzLevel::where('min_points', '<=', $points)
                    ->orderByDesc('min_points')
                    ->first();
    }



    // app/Models/User.php
        public function isOnline()
        {
            return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(2));
        }


    // public function canAccessPanel(Panel $panel): bool
    // {
    //     return $this->hasRole('admin|staff|customer');
    //     // return true;
    // }

    public function sendEmailVerificationNotification()
    {
        $mailData = MailSetting::first();

        if (! $mailData) {
            Log::warning('Registration verification email not sent: no mail settings configured', [
                'user_id' => $this->id,
                'to' => $this->email,
            ]);
            return;
        }

        $protocol = MailSetting::TYPE[$mailData->mail_protocol];
        $host = $mailData->mail_host;

        if ($mailData->mail_protocol == MailSetting::MAIL_LOG) {
            $protocol = 'log';
            $host = 'mailhog';
        }

        if ($mailData->mail_protocol == MailSetting::SMTP) {
            $protocol = 'smtp';
        }

        if ($mailData->mail_protocol == MailSetting::SENDGRID) {
            $protocol = 'sendgrid';
        }

        $mailConfigSafe = [
            'mail_protocol' => $protocol,
            'mail_host' => $host,
            'mail_port' => $mailData->mail_port,
            'encryption' => $mailData->encryption ?? null,
            'from_address' => $mailData->reply_to ?? null,
            'from_name' => $mailData->mail_title ?? null,
        ];

        config(
            [
                'mail.default' => $protocol,
                "mail.mailers.$protocol.transport" => $protocol,
                "mail.mailers.$protocol.host" => $host,
                "mail.mailers.$protocol.port" => $mailData->mail_port,
                "mail.mailers.$protocol.encryption" => MailSetting::ENCRYPTION_TYPE[$mailData->encryption],
                "mail.mailers.$protocol.username" => $mailData->mail_username,
                "mail.mailers.$protocol.password" => $mailData->mail_password,
                "mail.mailers.$protocol.timeout" => 10, // Set timeout to 10 seconds for faster connection on shared hosting
                'mail.from.address' => $mailData->reply_to,
                'mail.from.name' => $mailData->mail_title,
            ]
        );

        Log::info('Sending registration verification email', [
            'user_id' => $this->id,
            'to' => $this->email,
            'mail_config' => $mailConfigSafe,
        ]);

        try {
            // Generate verification URL using reflection to call protected method
            $notification = new \App\Notifications\UserEmailVerification();
            $reflection = new \ReflectionClass($notification);
            $method = $reflection->getMethod('verificationUrl');
            $method->setAccessible(true);
            $verificationUrl = $method->invoke($notification, $this);

            \Illuminate\Support\Facades\Mail::to($this->email)
                ->send(new \App\Mail\VerifyEmailMail($this, $verificationUrl));

            // If you see this log but email not in inbox: check spam, or Gmail is rejecting (SPF/DKIM/reputation)
            Log::info('Registration verification email sent successfully (SMTP accepted)', [
                'user_id' => $this->id,
                'to' => $this->email,
            ]);
        } catch (\Throwable $e) {
            Log::error('Registration verification email failed', [
                'user_id' => $this->id,
                'to' => $this->email,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'mail_config' => $mailConfigSafe,
            ]);
            // Don't rethrow: registration still succeeds; user can resend verification email later
        }
    }

    public static function getForm($form)
{
    
    return [
        Section::make()
            ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->label(__('messages.staff.first_name') . ':')
                    ->validationAttribute(__('messages.staff.first_name'))
                    ->placeholder(__('messages.staff.first_name')),
                TextInput::make('last_name')
                    ->required()
                    ->label(__('messages.staff.last_name') . ':')
                    ->validationAttribute(__('messages.staff.last_name'))
                    ->placeholder(__('messages.staff.last_name')),
                TextInput::make('email')
                    ->required()
                    ->label(__('messages.staff.email') . ':')
                    ->validationAttribute(__('messages.staff.email'))
                    ->placeholder(__('messages.staff.email'))
                    ->email()
                    ->unique(ignorable: fn(?User $record) => $record),
                TextInput::make('contact')
                    ->required()
                    ->tel()
                    ->rules(['required', 'regex:/^[0-9]{10}$/'])
                    ->label(__('messages.staff.contact_no') . ':')
                    ->validationAttribute(__('messages.staff.contact_no'))
                    ->placeholder(__('messages.staff.contact_no')),
                TextInput::make('username')
                    ->required()
                    ->label(__('messages.staff.username') . ':')
                    ->validationAttribute(__('messages.staff.username'))
                    ->placeholder(__('messages.staff.username'))
                    ->autocomplete(false)
                    ->unique(ignorable: fn(?User $record) => $record),
                Select::make('roles')
                    ->label(__('messages.staff.role') . ':')
                    ->validationAttribute(__('messages.staff.role'))
                    ->placeholder(__('messages.staff.role'))
                    ->required()
                    ->searchable()
                    ->options(Role::whereNotIn('name', ['customer'])->pluck('name', 'id'))
                    ->saveRelationshipsUsing(function (Model $record, $state) {
                        $record->roles()->sync($state);
                    })
                    ->native(false)
                    ->preload()
                    ->hidden($form->getOperation() == 'edit'),
                TextInput::make('password')
                    ->label(__('messages.staff.password') . ':')
                    ->validationAttribute(__('messages.staff.password'))
                    ->placeholder(__('messages.staff.password'))
                    ->password()
                    ->required()
                    ->revealable()
                    ->autocomplete(false)
                    ->maxLength(255)
                    ->rules(['min:8'])
                    ->visible(function (?string $operation) {
                        return $operation == 'create';
                    }),
                TextInput::make('password_confirmation')
                    ->label(__('messages.user.confirm_password') . ':')
                    ->validationAttribute(__('messages.user.confirm_password'))
                    ->placeholder(__('messages.user.confirm_password'))
                    ->password()
                    ->required()
                    ->revealable()
                    ->same('password')
                    ->maxLength(255)
                    ->rules(['min:8'])
                    ->visible(function (?string $operation) {
                        return $operation == 'create';
                    }),
                TextInput::make('password_plain')
                    ->label(__('messages.staff.password') . ':')
                    ->validationAttribute(__('messages.staff.password'))
                    ->placeholder(__('messages.staff.password'))
                    ->default(function (?Model $record) {
                        return $record?->password_plain ?? '';
                    })
                    ->visible(function (?string $operation) {
                        return $operation == 'edit';
                    })
                    ->dehydrated(true),

                Fieldset::make('Label')
                    ->label('')
                    ->schema([
                        Radio::make('gender')
                            ->label(__('messages.staff.gender') . ':')
                            ->validationAttribute(__('messages.staff.gender'))
                            ->columns(2)
                            ->required()
                            ->options(User::getGenderType())
                            ->default(User::MALE),
                        Toggle::make('status')
                            ->label(__('messages.status') . ':')
                            ->validationAttribute(__('messages.status'))
                            ->inline(false)
                            ->default(true),
                    ])->columns(2)->columnSpan(1),

                Textarea::make('about_us')
                    ->rows(3)
                    ->label(__('messages.staff.about_us') . ':')
                    ->validationAttribute(__('messages.staff.about_us'))
                    ->placeholder(__('messages.staff.about_us'))
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('profile')
                    ->label(__('messages.staff.profile') . ':')
                    ->reorderable()
                    ->image()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->rules(['image', 'max:5120', 'mimes:jpg,jpeg,png,webp'])
                    ->disk(config('app.media_disk'))
                    ->collection(Staff::PROFILE),
                Fieldset::make('Roles')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_editor')
                                    ->label(__('messages.other_lang.editor') . ':')
                                    ->default(false)
                                    ->live() // Real-time updates ke liye
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state) {
                                            $set('is_moderator', false); // Editor on ho to Moderator off ho jaye
                                        } else if (!$state && !$get('is_moderator')) {
                                            $set('is_editor', false); // Agar dono off hain to clear karo
                                        }
                                    }),
                                Toggle::make('is_moderator')
                                    ->label(__('messages.other_lang.moderator') . ':')
                                    ->default(false)
                                    ->live() // Real-time updates ke liye
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state) {
                                            $set('is_editor', false); // Moderator on ho to Editor off ho jaye
                                        } else if (!$state && !$get('is_editor')) {
                                            $set('is_moderator', false); // Agar dono off hain to clear karo
                                        }
                                    }),
                            ]),
                    ])->columns(1),
                SpatieMediaLibraryFileUpload::make('cover_img')
                    ->label(__('messages.staff.cover_image') . ':')
                    ->reorderable()
                    ->image()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->rules(['image', 'max:5120', 'mimes:jpg,jpeg,png,webp'])
                    ->disk(config('app.media_disk'))
                    ->collection(Staff::COVER_IMG),
                Hidden::make('type')->default(User::STAFF),
            ])->columns(2)
    ];
}

    /**
     * Get profile visitors (users who visited this profile)
     */
    public function profileVisitors()
    {
        return $this->hasMany(ProfileVisitor::class, 'profile_owner_id');
    }

    /**
     * Get visits made by this user to other profiles
     */
    public function profileVisits()
    {
        return $this->hasMany(ProfileVisitor::class, 'visitor_id');
    }

    /**
     * Get recent profile visitors (logged-in users only)
     */
    public function recentProfileVisitors()
    {
        return $this->profileVisitors()
            ->whereNotNull('visitor_id')
            ->with('visitor')
            ->orderBy('visited_at', 'desc');
    }

    /**
     * Users that this user has blocked (blocker_id = this user).
     */
    public function blockedUsers()
    {
        return $this->hasMany(UserBlock::class, 'blocker_id');
    }

    /**
     * Users that have blocked this user (blocked_id = this user).
     */
    public function blockedByUsers()
    {
        return $this->hasMany(UserBlock::class, 'blocked_id');
    }

    /**
     * Check if this user has blocked the given user (by id or User model).
     */
    public function hasBlocked($user): bool
    {
        $userId = $user instanceof User ? $user->id : (int) $user;
        return UserBlock::where('blocker_id', $this->id)->where('blocked_id', $userId)->exists();
    }

    /**
     * Check if this user is blocked by the given user (by id or User model).
     */
    public function isBlockedBy($user): bool
    {
        $userId = $user instanceof User ? $user->id : (int) $user;
        return UserBlock::where('blocker_id', $userId)->where('blocked_id', $this->id)->exists();
    }

    /**
     * Check if there is any block between this user and the given user (either direction).
     */
    public function hasBlockRelationWith($user): bool
    {
        return UserBlock::isBlockedBetween($this->id, $user instanceof User ? $user->id : (int) $user);
    }

    /**
     * Check if a user can send messages to this user
     */
    public function canReceiveMessagesFrom(User $sender): bool
    {
        // Block: no messaging in either direction
        if (UserBlock::isBlockedBetween($this->id, $sender->id)) {
            return false;
        }
        switch ($this->who_can_send_messages) {
            case 'all':
                return true;
            case 'following':
                return $this->isFollowing($sender);
            case 'nobody':
                return false;
            default:
                return true;
        }
    }

    /**
     * Check if this user is following another user
     */
    public function isFollowing(User $user): bool
    {
        return \App\Models\Followers::where('following', $this->id)
            ->where('followers', $user->id)
            ->exists();
    }

    /**
     * Check if messaging is disabled for this user
     */
    public function isMessagingDisabled(): bool
    {
        return $this->who_can_send_messages === 'nobody';
    }

    /**
     * Get the notification preference for messages
     */
    public function getMessageNotificationPreference(): string
    {
        return $this->message_notification_preference ?? 'email_and_notification';
    }

    /**
     * Wishlist entries (games) for this user
     */
    public function wishlistItems()
    {
        return $this->hasMany(UserGameWishlist::class)->orderBy('created_at', 'desc');
    }

    /**
     * Game releases on this user's wishlist (via pivot)
     */
    public function wishlistGames()
    {
        return $this->belongsToMany(GameRelease::class, 'user_game_wishlist', 'user_id', 'game_release_id')
            ->withPivot('highlighted')
            ->withTimestamps();
    }

    /**
     * Comments by this user (for ranking withCount).
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class, 'user_id');
    }

    /**
     * Scope: only users that should appear in ranking (exclude admin, editors, moderators).
     */
    public function scopeRankingOnly($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('type')->orWhere('type', '!=', self::ADMIN);
            })
            ->where(function ($q) {
                $q->whereNull('is_editor')->orWhere('is_editor', 0);
            })
            ->where(function ($q) {
                $q->whereNull('is_moderator')->orWhere('is_moderator', 0);
            });
    }
}
