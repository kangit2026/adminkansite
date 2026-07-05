@extends('admin.layout')

@section('title', 'Редагування новини')

@section('content')
<h1 class="h3 mb-4">Редагування новини</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.news._form')
</form>
@endsection
