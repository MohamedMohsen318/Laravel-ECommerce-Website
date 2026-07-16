@extends('layouts.app')

@section('title', 'Add Attribute')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Add Attribute</h1>
            <a class="button secondary" href="{{ route('admins.item-attributes.index') }}">Back</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.item-attributes.store') }}">
                @csrf
                @include('admin.item-attributes.form', ['attribute' => null])
                <button class="button" type="submit">Save Attribute</button>
            </form>
        </div>
    </section>
@endsection
