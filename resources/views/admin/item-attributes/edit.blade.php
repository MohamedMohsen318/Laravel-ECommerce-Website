@extends('layouts.app')

@section('title', 'Edit Attribute')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Edit Attribute</h1>
            <a class="button secondary" href="{{ route('admins.item-attributes.index') }}">Back</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.item-attributes.update', $itemAttribute) }}">
                @csrf
                @method('PUT')
                @include('admin.item-attributes.form', ['attribute' => $itemAttribute])
                <button class="button" type="submit">Update Attribute</button>
            </form>
        </div>
    </section>
@endsection
