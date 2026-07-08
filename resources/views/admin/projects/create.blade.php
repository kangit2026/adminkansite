@extends('admin.layout')

@section('title', 'Новий проєкт')

@section('content')
<h1 class="h3 mb-4">Новий проєкт</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
    @include('admin.projects._form')
</form>
@endsection
