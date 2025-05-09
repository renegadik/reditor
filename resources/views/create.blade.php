@extends('layouts.app')

@section('content')
    <h2>{{ __('create_key_title') }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('create') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('key') }}</label>
            <input type="text" name="key" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('type') }}</label>
            <select name="type" class="form-select" required>
                <option value="1">{{ __('string') }}</option>
                <option value="3"> {{ __('list') }} | ["test", "asd", "asd"]</option>
                <option value="2"> {{ __('set') }} | ["test", "asd", "asd"]</option>
                <option value="4"> {{ __('hash') }} | { "username": "test", "role": "asd", "active": true } </option>
                <option value="5"> {{ __('zset') }} | { "asd1": 100, "asd2": 50, "asd3": 75 }</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('value') }}</label>
            <textarea name="value" class="form-control" rows="4" required placeholder="json if no str"></textarea>
        </div>

        <button type="submit" class="btn btn-success">{{ __('create_button') }}</button>
        <a href="{{ route('home') }}" class="btn btn-secondary">{{ __('back_button') }}</a>
    </form>
@endsection
