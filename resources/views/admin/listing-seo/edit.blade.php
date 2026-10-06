@extends('admin.layout')

@section('title', $config['title'].' — SEO')

@section('content')
@include($config['tabs'])

<h1 class="h3 mb-4">{{ $config['title'] }} ({{ $config['path'] }}) — SEO</h1>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.'.$section.'.seo.update') }}">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header fw-semibold">Meta-теги для сторінки списку</div>
        @include('admin._site_setting_listing_seo_fields', ['prefix' => $config['prefix'], 'settings' => $settings])
    </div>

    <button type="submit" class="btn btn-dark">Зберегти</button>
</form>
@endsection
