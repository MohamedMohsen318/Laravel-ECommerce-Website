@extends('layouts.app')

@section('title','Create Category')

@section('content')

    <section class="card">

        <h1>Create Category</h1>

        <form method="POST"
              action="{{ route('admins.categories.store') }}"
              enctype="multipart/form-data">

            @csrf

            <label>Parent</label>

            <select name="parent_id">
                <option value="">No parent</option>

                @foreach($selectCategories as $cat)
                    <option value="{{ $cat['id'] }}">
                        {{ str_repeat('-- ', $cat['level']) }}
                        {{ $cat['name'] }}
                    </option>
                @endforeach
            </select>

            <label>Name</label>
            <input type="text" name="translations[en][name]" required>

            <label>Description</label>
            <textarea name="translations[en][description]"></textarea>

            <input type="hidden" name="translations[ar][name]" value="">
            <input type="hidden" name="translations[ar][description]" value="">

            <label>Image</label>
            <input type="file" name="image">

            <label>
                <input type="checkbox" name="is_active" value="1" checked>
                Active
            </label>

            <button type="submit">Create</button>

        </form>

    </section>

@endsection
