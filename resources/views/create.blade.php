@extends('layouts.app')

@section('content')
    <h2>create key</h2>

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
            <label class="form-label">key</label>
            <input type="text" name="key" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">type</label>
            <select name="type" class="form-select" required>
                <option value="string">string</option>
                <option value="list">list | ["test", "asd", "asd"]</option>
                <option value="set">set | ["test", "asd", "asd"]</option>
                <option value="hash">hash | { "username": "test", "role": "asd", "active": true } </option>
                <option value="zset">zset | { "asd1": 100, "asd2": 50, "asd3": 75 }</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">value</label>
            <textarea name="value" class="form-control" rows="4" required placeholder="json if no str"></textarea>
        </div>

        <button type="submit" class="btn btn-success">create</button>
        <a href="{{ route('home') }}" class="btn btn-secondary">undo</a>
    </form>
@endsection
