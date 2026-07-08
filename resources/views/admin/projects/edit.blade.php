@extends('admin.layout')

@section('title', 'Редагування проєкту')

@section('content')
<h1 class="h3 mb-4">Редагування проєкту</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.projects._form')
</form>
@endsection
