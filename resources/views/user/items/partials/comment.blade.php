@php
    $isArabic = app()->getLocale() === 'ar';
    $t = fn (string $en, string $ar) => $isArabic ? $ar : $en;
@endphp

<div class="comment-item" style="margin-inline-start: {{ $comment->parent_id ? '24px' : '0' }}">
    <div class="comment-meta">
        <strong>{{ $comment->user->name }}</strong>
        <span class="muted">{{ $comment->created_at->diffForHumans() }}</span>
    </div>

    <p id="comment-body-{{ $comment->id }}">{{ $comment->body }}</p>

    @auth
        <div class="actions">
            @if ($comment->user_id === auth()->id())
                <button type="button" class="link-button"
                    onclick="document.getElementById('edit-form-{{ $comment->id }}').classList.toggle('hidden'); document.getElementById('comment-body-{{ $comment->id }}').classList.toggle('hidden')">
                    {{ $t('Edit', 'تعديل') }}
                </button>

                <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                    onsubmit="return confirm('{{ $t('Delete this comment?', 'تحذف التعليق ده؟') }}')">
                    @csrf
                    @method('DELETE')
                    <button class="link-button" type="submit">{{ $t('Delete', 'حذف') }}</button>
                </form>
            @endif

            <button type="button" class="link-button"
                onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('hidden')">
                {{ $t('Reply', 'رد') }}
            </button>
        </div>

        @if ($comment->user_id === auth()->id())
            <form id="edit-form-{{ $comment->id }}" class="hidden stack" method="POST"
                action="{{ route('comments.update', $comment) }}">
                @csrf
                @method('PUT')
                <textarea name="body" maxlength="1000" required>{{ $comment->body }}</textarea>
                <button class="button secondary" type="submit">{{ $t('Save', 'حفظ') }}</button>
            </form>
        @endif

        <form id="reply-form-{{ $comment->id }}" class="hidden stack" method="POST"
            action="{{ route('products.comments.store', $item) }}">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <textarea name="body" maxlength="1000" required
                placeholder="{{ $t('Write a reply', 'اكتب رد') }}"></textarea>
            <button class="button secondary" type="submit">{{ $t('Send', 'إرسال') }}</button>
        </form>
    @endauth

    @if ($comment->replies->isNotEmpty())
        <div class="stack">
            @foreach ($comment->replies as $reply)
                @include('user.items.partials.comment', ['comment' => $reply, 'item' => $item])
            @endforeach
        </div>
    @endif
</div>
