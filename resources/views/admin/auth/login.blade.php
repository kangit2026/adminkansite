@extends('admin.layout')

@section('title', 'Вхід')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: radial-gradient(900px 500px at 20% 0%, rgba(18,15,130,.08) 0%, #e8eaef 55%);">
    <div class="card" style="width: 400px; border-color: var(--kan-border-strong);">
        <div class="card-body p-4 pt-5 pb-5">
            <div class="d-flex justify-content-center mb-4">
                <div class="admin-brand-mark" style="width:48px;height:48px;border-radius:13px;font-size:.85rem;">KAN</div>
            </div>
            <h1 class="h5 mb-1 text-center fw-semibold">Вхід в адмінку</h1>
            <p class="text-center text-muted small mb-4">{{ config('app.name') }}</p>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
                    <label class="form-check-label small" for="remember">Запам'ятати мене</label>
                </div>
                <button type="submit" class="btn btn-dark w-100">Увійти</button>
            </form>
        </div>
    </div>
</div>
@endsection
