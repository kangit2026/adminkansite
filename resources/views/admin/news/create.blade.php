@extends('admin.layout')

@section('title', 'Нова новина')

@section('content')
<h1 class="h3 mb-4">Нова новина</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
    @include('admin.news._form')
</form>
@endsection
