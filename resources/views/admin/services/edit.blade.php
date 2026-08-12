@extends('admin.layout')

@section('title', 'Редагування послуги')

@section('content')
<h1 class="h3 mb-4">Редагування послуги</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.services._form')
</form>
@endsection
