@php
    $isArabic = app()->getLocale() === 'ar';
    $t = fn (string $en, string $ar) => $isArabic ? $ar : $en;
    $avg = (int) round($item->averageRating());
@endphp

<section class="card stack" id="reviews">
    <div class="reviews-header">
        <h2>{{ $t('Reviews', 'التقييمات') }}</h2>
        <div class="rating-summary">
            <span class="rating-stars">{{ str_repeat('*', $avg) }}{{ str_repeat('-', 5 - $avg) }}</span>
            <span class="muted">{{ $item->averageRating() }} ({{ $item->reviewsCount() }} {{ $t('reviews', 'تقييم') }})</span>
        </div>
    </div>

    @auth
        <form method="POST" action="{{ route('products.reviews.store', $item) }}" class="stack">
            @csrf
            <div class="rating-input">
                @for ($i = 5; $i >= 1; $i--)
                    <label>
                        <input type="radio" name="rating" value="{{ $i }}"
                            {{ old('rating', $myReview?->rating) == $i ? 'checked' : '' }} required>
                        {{ $i }}
                    </label>
                @endfor
            </div>
            <textarea name="body" maxlength="1000"
                placeholder="{{ $t('Write a review (optional)', 'اكتب تقييمك (اختياري)') }}">{{ old('body', $myReview?->body) }}</textarea>
            <button class="button secondary" type="submit">
                {{ $myReview ? $t('Update Review', 'تحديث التقييم') : $t('Submit Review', 'إرسال التقييم') }}
            </button>
        </form>

        @if ($myReview)
            <form method="POST" action="{{ route('products.reviews.destroy', $item) }}"
                onsubmit="return confirm('{{ $t('Delete your review?', 'تحذف تقييمك؟') }}')">
                @csrf
                @method('DELETE')
                <button class="link-button" type="submit">{{ $t('Delete my review', 'احذف تقييمي') }}</button>
            </form>
        @endif
    @else
        <p class="muted">
            <a href="{{ route('login') }}">{{ $t('Log in to leave a review', 'سجل الدخول عشان تضيف تقييم') }}</a>
        </p>
    @endauth

    <div class="stack">
        @forelse ($reviews as $review)
            <div class="review-item">
                <strong>{{ $review->user->name }}</strong>
                <span class="rating-stars">{{ str_repeat('*', $review->rating) }}{{ str_repeat('-', 5 - $review->rating) }}</span>
                @if ($review->body)
                    <p>{{ $review->body }}</p>
                @endif
            </div>
        @empty
            <p class="muted">{{ $t('No reviews yet', 'لا توجد تقييمات حتى الآن') }}</p>
        @endforelse
    </div>

    {{ $reviews->links() }}
</section>
