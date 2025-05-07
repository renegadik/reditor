@extends('layouts.app')

@section('content')
    <h2 class="mb-4">{{ __('settings_title') }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('settings.store') }}">
        @csrf

        {{-- Язык интерфейса --}}
        <div class="mb-3">
            <label for="language" class="form-label">{{ __('settings_language_title') }}</label>
            <select name="language" id="language" class="form-select">
                <option value="ru" {{ ($settings['language'] ?? '') === 'ru' ? 'selected' : '' }}>Русский</option>
                <option value="en" {{ ($settings['language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
            </select>
        </div>

        {{-- Можно добавить ещё поля настроек сюда --}}

        <button type="submit" class="btn btn-primary">{{ __('save_button') }}</button>
        <a href="{{ route('home') }}" class="btn btn-secondary">← {{ __('back_button') }}</a>
    </form>
@endsection
