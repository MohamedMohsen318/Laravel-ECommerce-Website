@extends('layouts.app')

@section('title', 'Product Options')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Product Options</h1>
            <a class="button" href="{{ route('admins.item-options.create') }}">Add Option</a>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr><th>Option</th><th>Values</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($options as $option)
                        <tr>
                            <td>{{ $option->name }}</td>
                            <td>{{ $option->values->pluck('value')->join(', ') }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button secondary" href="{{ route('admins.item-options.edit', $option) }}">Edit</a>
                                    <form method="POST" action="{{ route('admins.item-options.destroy', $option) }}" onsubmit="return confirm('Delete this option?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="muted">No options yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
