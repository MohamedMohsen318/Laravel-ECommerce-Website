@extends('layouts.app')

@section('title', 'Comments')

@section('content')
    <section class="stack">
        <h1>Comments</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>User</th>
                    <th>Reply To</th>
                    <th>Body</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comments as $comment)
                    <tr>
                        <td>{{ $comment->item->name }}</td>
                        <td>{{ $comment->user->name }}</td>
                        <td>{{ $comment->parent ? '#' . $comment->parent->id : '-' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($comment->body, 60) }}</td>
                        <td>{{ $comment->created_at->format('Y-m-d') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admins.comments.destroy', $comment) }}"
                                onsubmit="return confirm('Delete this comment and its replies?')">
                                @csrf
                                @method('DELETE')
                                <button class="button secondary" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No comments found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $comments->links() }}
    </section>
@endsection
