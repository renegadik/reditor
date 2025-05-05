@extends('layouts.app')

@section('content')
    <h2>key: <code>{{ $key }}</code></h2>
    <p><strong>type:</strong> {{ $type }}</p>
    <p><strong>ttl:</strong> {{ $ttl }}</p>

    <pre class="bg-white p-3 border rounded">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <a href="{{ route('home') }}" class="btn btn-secondary mt-3">← back</a>
@endsection
