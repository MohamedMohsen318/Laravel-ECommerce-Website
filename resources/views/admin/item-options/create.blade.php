@extends('layouts.app')

@section('title', 'Add Product Option')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Add Product Option</h1>
            <a class="button secondary" href="{{ route('admins.item-options.index') }}">Back to Options</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.item-options.store') }}">
                @csrf
                <label class="field">
                    <span>Option name</span>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Size, Color, Material">
                </label>
                <label class="field">
                    <span>Values</span>
                    <textarea name="values[]" placeholder="Small"></textarea>
                </label>
                <label class="field">
                    <span>Value</span>
                    <input type="text" name="values[]" placeholder="Medium">
                </label>
                <label class="field">
                    <span>Value</span>
                    <input type="text" name="values[]" placeholder="Large">
                </label>
                <button class="button" type="submit">Save Option</button>
            </form>
        </div>
    </section>
@endsection
