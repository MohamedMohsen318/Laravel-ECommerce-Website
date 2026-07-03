@extends('layouts.app')

@section('title', 'Reviews')

@section('content')
    <section class="stack">
        <h1>Reviews</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Body</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>{{ $review->item->name }}</td>
                        <td>{{ $review->user->name }}</td>
                        <td class="rating-stars">{{ str_repeat('*', $review->rating) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($review->body, 60) }}</td>
                        <td>{{ $review->created_at->format('Y-m-d') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admins.reviews.destroy', $review) }}"
                                onsubmit="return confirm('Delete this review?')">
                                @csrf
                                @method('DELETE')
                                <button class="button secondary" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No reviews found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $reviews->links() }}
    </section>
@endsection
