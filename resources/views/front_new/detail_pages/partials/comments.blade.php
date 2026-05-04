@foreach ($comments as $comment)
@php
    $commentAuthorId = $comment->user_id ?? optional($comment->users)->id ?? 0;
    $commentAuthorIdentifier = optional($comment->users)->username ?? $commentAuthorId;
    $commentAuthorName = $comment->users ? ($comment->users->username ?? trim(($comment->users->first_name ?? '') . ' ' . ($comment->users->last_name ?? ''))) : ($comment->name ?: 'Anonymous');
    $isCommentAuthorBlocked = $commentAuthorId && auth()->check() && \App\Models\UserBlock::isBlockedBetween(auth()->id(), $commentAuthorId);
@endphp
<div class="p-md-4 py-4" style="border-bottom: 1px solid #e8e8e8;">
    <div class="media d-flex card-view-{{ $comment->id }} mt-2" id="comment-{{ $comment->id }}">
        <div class="media-img {{ getFrontSelectLanguageIsoCode() == 'ar' ? 'ms-4' : 'me-4' }} rounded-10">
            <img src="{{ $isCommentAuthorBlocked ? asset('web/media/avatars/150-2.jpg') : (isset($comment->users->profile_image) ? $comment->users->profile_image : asset('web/media/avatars/150-2.jpg')) }}" alt="" class="w-100 h-100 rounded-10">
        </div>
        <div class="media-body comment-content w-100">
            <div class="media-title d-flex flex-wrap justify-content-between">
                <h5 class="mt-0 text-black fs-16 mb-1 user-name">
                    @if($isCommentAuthorBlocked)
                        <span class="text-black">{{ $commentAuthorName }}</span>
                    @else
                    <a href="{{ route('user.public.profile', $commentAuthorIdentifier) }}"
                        class="profile-link text-black hover:underline"
                        data-user-identifier="{{ $commentAuthorIdentifier }}">
                        {{ $commentAuthorName }}
                    </a>
                    @endif
                   
                    @php
                        $showStaffMarkers = \App\Models\Setting::where('key', 'show_staff_markers')->value('value') ?? true;
                    @endphp
                    @if ($showStaffMarkers && $comment->users)
                        @if ($comment->users->is_editor)
                            <span style="background-color: #B051B0; color: white; padding: 2px 5px; margin-left: 5px;">Redakteur/in</span>
                        @elseif ($comment->users->is_moderator)
                            <span style="background-color: green; color: white; padding: 2px 5px; margin-left: 5px;">Moderator/in</span>
                        @endif
                    @endif
                    
                    <!-- @php
                        $canEditDelete = now()->diffInSeconds($comment->created_at) < 60;
                    @endphp -->

                    <!-- @if (Auth::check() && $comment->user_id == getLogInUser()->id && $canEditDelete)
                        <div class="fs-12 text-muted" id="edit-delete-timer-{{ $comment->id }}">
                            ⏳ <span data-start-time="{{ $comment->created_at->timestamp }}" data-comment-id="{{ $comment->id }}">60</span>s {{ __('messages.other_lang.left_to_edit_or_delete') }}
                        </div>
                    @endif -->

                    @if ($comment->users)
                        <div class="fs-14 mt-1" style="color:#B051B0">
                            {{ __('messages.other_lang.player_points') }}: 
                            <strong class="text-gray">{{ $comment->users->comment_points ?? 0 }}</strong>
                        </div>
                        @php
                            $user = $comment->users;
                            $levelObj = null;
                            $badgeBgColor = '#1e40af';
                            $badgeTextColor = '#93c5fd';
                            $levelName = 'Newbie';
                            
                            if ($user) {
                                try {
                                    $levelObj = $user->level_object;
                                    if ($levelObj) {
                                        // Use null coalescing to handle null/empty values properly
                                        $badgeBgColor = !empty($levelObj->badge_color) ? $levelObj->badge_color : '#1e40af';
                                        $badgeTextColor = !empty($levelObj->badge_text_color) ? $levelObj->badge_text_color : '#93c5fd';
                                    }
                                    $levelName = $user->level ?? 'Newbie';
                                } catch (\Exception $e) {
                                    // Fallback to defaults
                                }
                            }
                        @endphp
                        <div class="fs-14 mt-1">
                            <span class="level-badge" style="display: inline-block; background-color: {{ $badgeBgColor }}; color: {{ $badgeTextColor }}; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; line-height: 1.4;">{{ $levelName }}</span>
                        </div>
                    @endif

                    
                </h5>
                <!-- <div>
                    @php
                            $canEditDelete = now()->diffInSeconds($comment->created_at) < 60;
                    @endphp
                    @if (Auth::check() && $comment->user_id == getLogInUser()->id)
                        <button class="delete-btn fs-14 text-danger delete-comment-btn" data-id="{{ $comment['id'] }}">
                            <i class="fa fa-trash-can"></i> {{ __('messages.delete') }}
                        </button>

                        <button class="edit-btn fs-14 text-primary ms-2 edit-comment-btn" style="background-color: transparent; border: none; cursor: pointer;" data-id="{{ $comment['id'] }}" onclick="toggleEdit({{ $comment->id }})">
                            <i class="fa fa-pen"></i> {{ __('messages.other_lang.edit') }}
                        </button>
                    @endif
                </div>     -->                               
            </div>
            <span class="text-gray fs-14 reply-time">{{ $comment->created_at->diffForHumans() }}</span>
            @if ($comment->edited_at && !$comment->deleted_at)
                <span class="ms-2" style="cursor: pointer;" title="{{ __('messages.other_lang.edited') }}">
                    <i class="fa fa-pen" style="color: grey;"></i>
                </span>
            @endif            
            <p class="fs-14 text-gray mt-1 comment-msg">
                @if($comment->deleted_at)
                    <span style="color: #B051B0;">{{ __('messages.comment.deleted_comment_message') }} <a href="/terms-conditions" target="_blank" style="color: #B051B0; text-decoration: underline;">{{ __('messages.comment.terms_and_conditions') }}</a>.</span>
                @elseif((int) ($comment->status ?? 1) !== 1)
                    <span class="fst-italic text-muted">{{ __('messages.comment.comment_removed_by_admin') }}</span>
                @else
                    {!! $comment->comment !!}
                @endif
            </p>
          

            <!-- <section id="answer_{{$comment->id}}" class="post-comment-section bg-light d-none px-30 py-4">
                <h5 class="fs-16 text-black fw-6 mb-3">{{ __('messages.comment.post_a_comment') }}</h5>
                <form id="commentForm">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <input type="hidden" name="user_id" value="{{ isset(getLogInUser()->id) ? getLogInUser()->id : null }}">
                    <div class="row">
                        @if (!Auth::check())
                        <div class="col-md-12">
                            <a class="btn btn-primary" href="{{url('login')}}">Zum kommentieren einloggen</a>
                        </div>
                        @else
                        <div class="col-12">
                            <p class="lead emoji-picker-container">
                                <textarea class="form-control textarea-control fs-14 text-gray" name="comment" id="comment" style="color:rgb(123, 123, 123) !important" rows="3" placeholder="{{ __('messages.comment.type_your_comments') }}" required></textarea>
                            </p>
                        </div>
                        <div class="col-12 mb-2">
                            @if ($showCaptcha == '1')
                            <input type="hidden" value="{{ $settings['show_captcha'] }}" id="googleCaptch">
                            <div class="form-group mb-1">
                                <div class="g-recaptcha" id="gRecaptchaContainerPostDetails" data-sitekey="{{ $settings['site_key'] }}"></div>
                                <div id="g-recaptcha-error"></div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary comment-btn">{{ __('messages.common.submit') }}</button>
                    @endif
                </form>
            </section> -->
            <section id="edit_box_{{ $comment->id }}" class="my-4 d-none">
                <div class="comment-box position-relative bg-light p-3 rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <img src="{{ getLogInUser()->profile_image ?? asset('assets/image/avatar.png') }}" alt="User Avatar" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <div class="user-info">{{ getLogInUser()->username ?? 'Gast' }}</div>
                                <small class="text-muted">{{ __('messages.other_lang.edit_your_comment') }}</small>
                            </div>
                        </div>
                        <div class="close-btn" role="button" onclick="closeEditBox({{ $comment->id }})" style="cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-x-icon lucide-x">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </div>
                    </div>

                    @if(Auth::check())
                        <form onsubmit="submitEdit(event, {{ $comment->id }})">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                            <textarea name="comment" id="edit_comment_text_{{ $comment->id }}" class="form-control mb-3"
                                style="color: black !important;" rows="4" required>{{ $comment->comment }}</textarea>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ __('messages.common.save') }}</button>
                            </div>
                        </form>
                    @else
                        <div class="text-center">
                            <a href="{{ url('/login') }}" class="btn btn-sm btn-primary">Zum kommentieren einloggen</a>
                        </div>
                    @endif
                </div>
            </section>


            <section id="answer_{{$comment->id}}" class="my-4 d-none">
                <div class="comment-box position-relative bg-light p-3 rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <img src="{{ getLogInUser()->profile_image ?? asset('assets/image/avatar.png') }}" alt="User Avatar" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <div class="user-info">{{ getLogInUser()->username ?? 'Gast' }}</div>
                                <small class="text-muted">{{ __('messages.comment.post_a_comment') }}</small>
                            </div>
                        </div>
                        <div class="close-btn" role="button" onclick="toggleReply({{$comment->id}})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-x-icon lucide-x">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </div>
                    </div>

                    <form id="commentForm" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <input type="hidden" name="user_id" value="{{ getLogInUser()->id ?? null }}">

                        @if (!Auth::check())
                            <div class="text-center">
                                <a class="btn btn-primary" href="{{ url('login') }}">Zum kommentieren einloggen</a>
                            </div>
                        @else
                            <textarea name="comment" id="comment-reply-{{ $comment->id }}" class="form-control mb-3 emoji-textarea-reply" style="color : black !important" rows="4"
                                placeholder="{{ __('messages.comment.type_your_comments') }}" required></textarea>

                            @if ($showCaptcha == '1')
                                <input type="hidden" value="{{ $settings['show_captcha'] }}" id="googleCaptch">
                                <div class="form-group mb-2">
                                    <div class="g-recaptcha" id="gRecaptchaContainerPostDetails"
                                        data-sitekey="{{ $settings['site_key'] }}"></div>
                                    <div id="g-recaptcha-error"></div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary comment-btn">{{ __('messages.common.submit') }}</button>
                            </div>
                        @endif
                    </form>
                </div>
            </section>

             <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <small>
              @php $commentIsHidden = (int) ($comment->status ?? 1) !== 1; @endphp
              @if ($commentIsHidden)
                {{-- Hidden by admin: no like/edit, only reply link so thread can continue --}}
                <span style="color: #B051B0; margin-right: 10px !important;">
                    <i class="fa fa-thumbs-up" style="color: #B051B0;"></i>
                    <span style="color: #B051B0; margin-left: 4px;" class="like-count">{{ $comment->likes_count ?? 0 }}</span>
                </span>
            @elseif (Auth::check() && $comment->user_id == getLogInUser()->id)
                <span style="color: #B051B0; margin-right: 10px !important;">
                    <i class="fa fa-thumbs-up" style="color: #B051B0;"></i>
                    <span style="color: #B051B0; margin-left: 4px;" class="like-count">{{ $comment->likes_count }}</span>
                    <!-- <small class="text-gray fs-14">(You cannot like your own comment)</small> -->
                </span>
            @elseif ($isCommentAuthorBlocked)
                {{-- Blocked user's comment: show count only, no like and no reply --}}
                <span style="color: #B051B0; margin-right: 10px !important;">
                    <i class="fa fa-thumbs-up" style="color: #B051B0;"></i>
                    <span style="color: #B051B0; margin-left: 4px;" class="like-count">{{ $comment->likes_count }}</span>
                </span>
            @else
                <span class="like-btn"
                    style="cursor: pointer; margin-right: 10px !important;"
                    data-id="{{ $comment->id }}"
                    data-type="comment"
                    data-auth="{{ auth()->check() ? '1' : '0' }}">

                    <i class="fa fa-thumbs-up" id="current_like_icon_{{ $comment->id }}" style="{{ !empty($comment->user_liked) && $comment->user_liked ? 'color: #B051B0; fill: #B051B0;' : '' }}"></i>
                    <span style="color: #B051B0; margin-left: 4px;" class="like-count">{{ $comment->likes_count }}</span>
                </span>
            @endif
            @if (!$isCommentAuthorBlocked)
            <a href="javascript:void(0)" onclick="toggleReply({{$comment->id}})">
                <i class="fa fa-share"></i> {{ __('messages.other_lang.answer') }}
            </a>
            @endif
            @if (!$commentIsHidden && Auth::check() && $comment->user_id == getLogInUser()->id && !$comment->deleted_at)
                <a href="javascript:void(0)" class="ms-2 edit-comment-link" onclick="toggleEdit({{ $comment->id }})" style="color: #B051B0; text-decoration: none;">
                    <i class="fa fa-pen" style="color: #B051B0;"></i> {{ __('messages.other_lang.edit') }}
                </a>
            @endif
        </small>
        <small>
            @if (Auth::check() && $comment->user_id != getLogInUser()->id)
                <a href="javascript:void(0)" class="ms-2 report-comment-link" onclick="openReportModal({{ $comment->id }}, {{ $comment->post_id }}, {{ $comment->user_id }})" style="color: #B051B0; text-decoration: none;">
                    <i class="fa fa-ban" style="color: #B051B0;"></i> {{ __('messages.comment.report_comment') }}
                </a>
            @endif
        </small>
        </div>

        
            <!-- <br>
            <br> -->
        </div>
    </div>
    {{--reply section--}}
    @include('front_new.detail_pages.comment', ['id' => $comment->id, 'post_id' => $comment->post_id])
    {{--end reply section--}}
    </div>
@endforeach

<script>
    document.querySelectorAll('.profile-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const userIdentifier = this.dataset.userIdentifier;
            const currentUrl = window.location.href;
            const profileUrl = `/user/${userIdentifier}/profile?return_to=${encodeURIComponent(currentUrl)}`;
            window.location.href = profileUrl;
        });
    });

    
</script>

<script>
    document.querySelectorAll('[data-start-time]').forEach(el => {
        const commentId = el.dataset.commentId;
        const startTime = parseInt(el.dataset.startTime) * 1000;
        const timerSpan = el;

        function updateTimer() {
            const now = Date.now();
            const elapsed = Math.floor((now - startTime) / 1000);
            const remaining = 60 - elapsed;

            if (remaining <= 0) {
                // Remove delete button
                const deleteBtn = document.querySelector(`[data-id="${commentId}"].delete-comment-btn`);
                if (deleteBtn) deleteBtn.remove();

                // Remove edit button
                const editBtn = document.querySelector(`[data-id="${commentId}"].edit-comment-btn`);
                if (editBtn) editBtn.remove();

                // Remove timer display
                const timer = document.getElementById(`edit-delete-timer-${commentId}`);
                if (timer) timer.remove();
            } else {
                timerSpan.textContent = remaining;
                setTimeout(updateTimer, 1000);
            }
        }

        updateTimer();
    });
</script>

<script>
    function toggleEdit(commentId) {
        document.querySelectorAll('[id^="edit_box_"]').forEach(box => box.classList.add('d-none'));
        const editBox = document.getElementById('edit_box_' + commentId);
        editBox.classList.toggle('d-none');
        
        // Initialize emoji picker for edit textarea when box is opened
        if (!editBox.classList.contains('d-none')) {
            setTimeout(function() {
                var editTextarea = $('#edit_comment_text_' + commentId);
                if (editTextarea.length && !editTextarea.data('emojiPickerInit')) {
                    initEmojiPicker('#edit_comment_text_' + commentId);
                    editTextarea.data('emojiPickerInit', true);
                }
            }, 100);
        }
    }

    function closeEditBox(commentId) {
        const editBox = document.getElementById('edit_box_' + commentId);
        if (editBox) {
            editBox.classList.add('d-none');
        }
    }

    function submitEdit(event, commentId) {
        event.preventDefault();
        const commentText = document.getElementById('edit_comment_text_' + commentId).value;

        fetch(`/comment/${commentId}/edit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ comment: commentText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update comment text
                document.querySelector(`#comment-${commentId} .comment-msg`).innerHTML = commentText;
                
                // Add grey pen icon next to timestamp if not already present
                const replyTime = document.querySelector(`#comment-${commentId} .reply-time`);
                if (replyTime) {
                    const existingGreyPen = replyTime.nextElementSibling;
                    if (!existingGreyPen || !existingGreyPen.querySelector('.fa-pen[style*="grey"]')) {
                        const greyPen = document.createElement('span');
                        greyPen.className = 'ms-2';
                        greyPen.style.cursor = 'pointer';
                        greyPen.title = '{{ __("messages.other_lang.edited") }}';
                        greyPen.innerHTML = '<i class="fa fa-pen" style="color: grey;"></i>';
                        
                        // Insert after reply-time
                        replyTime.parentNode.insertBefore(greyPen, replyTime.nextSibling);
                    }
                }
                
                // Hide edit box
                document.getElementById('edit_box_' + commentId).classList.add('d-none');
            } else {
                alert('Failed to update comment');
            }
        });
    }
</script>

<!-- Report Comment Modal -->
<div id="reportCommentModal" class="report-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 9999; align-items: center; justify-content: center;">
    <div class="report-modal-content" style="background: #1a1a1a; border-radius: 20px; padding: 30px; max-width: 500px; width: 90%; position: relative; border: 2px solid #B051B0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: white; font-size: 20px; font-weight: bold; text-transform: uppercase; margin: 0;">{{ __('messages.comment.report_comment_title') }}</h2>
            <button onclick="closeReportModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">&times;</button>
        </div>
        <div style="width: 100%; height: 2px; background: #333; margin-bottom: 20px;"></div>
        <p style="color: white; margin-bottom: 20px; line-height: 1.6;">{{ __('messages.comment.report_comment_warning') }}</p>
        <form id="reportCommentForm" onsubmit="submitReport(event)">
            <input type="hidden" id="report_comment_id" name="comment_id">
            <input type="hidden" id="report_post_id" name="post_id">
            <input type="hidden" id="report_user_id" name="reported_user_id">
            <label style="color: white; display: block; margin-bottom: 10px;">{{ __('messages.comment.report_reason_label') }}</label>
            <textarea id="report_reason" name="report_reason" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #444; background: #2a2a2a; color: white; min-height: 100px; margin-bottom: 20px; resize: vertical;" placeholder=""></textarea>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeReportModal()" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">{{ __('messages.comment.report_no') }}</button>
                <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">{{ __('messages.comment.report_yes') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReportModal(commentId, postId, reportedUserId) {
        document.getElementById('report_comment_id').value = commentId;
        document.getElementById('report_post_id').value = postId;
        document.getElementById('report_user_id').value = reportedUserId;
        document.getElementById('report_reason').value = '';
        document.getElementById('reportCommentModal').style.display = 'flex';
    }

    function closeReportModal() {
        document.getElementById('reportCommentModal').style.display = 'none';
        document.getElementById('reportCommentForm').reset();
    }

    function submitReport(event) {
        event.preventDefault();
        
        const formData = {
            comment_id: document.getElementById('report_comment_id').value,
            post_id: document.getElementById('report_post_id').value,
            reported_user_id: document.getElementById('report_user_id').value,
            report_reason: document.getElementById('report_reason').value,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route("comment.report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeReportModal();
                showSuccessToast(data.message || '{{ __("messages.placeholder.comment_reported_successfully") }}');
            } else {
                showErrorToast(data.message || '{{ __("messages.placeholder.something_went_wrong") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('{{ __("messages.placeholder.something_went_wrong") }}');
        });
    }

    // Close modal when clicking outside
    document.getElementById('reportCommentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReportModal();
        }
    });
</script>

