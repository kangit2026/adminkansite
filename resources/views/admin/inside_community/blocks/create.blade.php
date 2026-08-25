@extends('admin.layout')

@section('title', 'Додати блок Inside Community')

@section('content')
<h1 class="h3 mb-4">Додати блок Inside Community</h1>

@include('admin._inside_community_tabs')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.inside-community.blocks.store') }}" enctype="multipart/form-data">
    @include('admin.inside_community.blocks._form')
</form>
@endsection
