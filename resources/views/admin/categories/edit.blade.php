@extends('layouts.app')

@section('title','Edit Category')

@section('content')

    <section class="card">

        <h1>Edit Category</h1>

        <form method="POST"
              action="{{ route('admins.categories.update',$category) }}"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <label>Parent</label>

            <select name="parent_id">

                <option value="">No parent</option>

                @foreach($selectCategories as $cat)
                    <option value="{{ $cat['id'] }}"
                        @selected($category->parent_id == $cat['id'])>

                        {{ str_repeat('-- ', $cat['level']) }}
                        {{ $cat['name'] }}

                    </option>
                @endforeach

            </select>

            <label>Name</label>
            <input type="text"
                   name="translations[en][name]"
                   value="{{ $category->translate('en')?->name }}">

            <label>Description</label>
            <textarea name="translations[en][description]">
            {{ $category->translate('en')?->description }}
        </textarea>

            <label>Image</label>
            <input type="file" name="image">

            <label>
                <input type="checkbox"
                       name="is_active"
                       value="1"
                    @checked($category->is_active)>
                Active
            </label>

            <button type="submit">Save</button>

        </form>

    </section>

@endsection
