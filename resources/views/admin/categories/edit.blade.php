@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <section class="card">
        <h1>Edit Category</h1>
        <form class="form" method="POST" action="{{ route('admins.categories.update', $category) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.categories._form', [
                'buttonText' => 'Save category',
            ])
        </form>
    </section>
@endsection
