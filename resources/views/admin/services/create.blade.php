@extends('admin.layout')

@section('title', 'Нова послуга')

@section('content')
<h1 class="h3 mb-4">Нова послуга</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
    @include('admin.services._form')
</form>
@endsection
