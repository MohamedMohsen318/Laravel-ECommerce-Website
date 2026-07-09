@extends('layouts.app')

@section('title', 'Edit Product Option')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Edit Product Option</h1>
            <a class="button secondary" href="{{ route('admins.item-options.index') }}">Back to Options</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.item-options.update', $itemOption) }}">
                @csrf
                @method('PUT')
                <label class="field">
                    <span>Option name</span>
                    <input type="text" name="name" value="{{ old('name', $itemOption->name) }}">
                </label>
                @foreach (old('values', $itemOption->values->pluck('value')->all()) as $value)
                    <label class="field">
                        <span>Value</span>
                        <input type="text" name="values[]" value="{{ $value }}">
                    </label>
                @endforeach
                <label class="field">
                    <span>New value</span>
                    <input type="text" name="values[]" placeholder="Optional">
                </label>
                <button class="button" type="submit">Update Option</button>
            </form>
        </div>
    </section>
@endsection
