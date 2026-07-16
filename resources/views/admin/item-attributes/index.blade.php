@extends('layouts.app')

@section('title', 'Item Attributes')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Item Attributes</h1>
            <a class="button" href="{{ route('admins.item-attributes.create') }}">Add Attribute</a>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Values</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attributes as $attribute)
                        <tr>
                            <td>{{ $attribute->name }}</td>
                            <td>{{ $attribute->values->pluck('value')->join(', ') }}</td>
                            <td>
                                <a class="button secondary" href="{{ route('admins.item-attributes.edit', $attribute) }}">Edit</a>
                                <form method="POST" action="{{ route('admins.item-attributes.destroy', $attribute) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No attributes yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
