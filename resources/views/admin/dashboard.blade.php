@extends('admin.layout')

@section('title', 'Дашборд')

@section('content')
<h1 class="h3 mb-4">Вітаємо, {{ auth()->user()->name }}!</h1>
<div class="card">
    <div class="card-body">
        <p class="mb-0">Базова адмінка встановлена і працює. Наступні розділи (Проекти, Новини, Вакансії, Сторінки тощо) з'являться в меню зліва по мірі реалізації.</p>
    </div>
</div>
@endsection
