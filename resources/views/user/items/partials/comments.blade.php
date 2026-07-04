@php
    $isArabic = app()->getLocale() === 'ar';
    $t = fn (string $en, string $ar) => $isArabic ? $ar : $en;
@endphp

<section class="card stack" id="comments">
    <h2>{{ $t('Comments', 'التعليقات') }}</h2>

    @auth
        <form method="POST" action="{{ route('products.comments.store', $item) }}" class="stack">
            @csrf
            <textarea name="body" maxlength="1000" required
                placeholder="{{ $t('Write a comment', 'اكتب تعليق') }}"></textarea>
            <button class="button secondary" type="submit">{{ $t('Comment', 'تعليق') }}</button>
        </form>
    @else
        <p class="muted">
            <a href="{{ route('login') }}">{{ $t('Log in to comment', 'سجل الدخول عشان تعلق') }}</a>
        </p>
    @endauth

    <div class="stack">
        @forelse ($comments as $comment)
            @include('user.items.partials.comment', ['comment' => $comment, 'item' => $item])
        @empty
            <p class="muted">{{ $t('No comments yet', 'لا يوجد تعليقات حتى الآن') }}</p>
        @endforelse
    </div>

    {{ $comments->links() }}
</section>
