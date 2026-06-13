@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
    <section class="card">
        <h1>Create Category</h1>
        <form class="form" method="POST" action="{{ route('admins.categories.store') }}" enctype="multipart/form-data">
            @include('admin.categories._form', [
                'category' => null,
                'buttonText' => 'Create category',
            ])
        </form>
    </section>
@endsection
