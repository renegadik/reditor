@extends('layouts.app')

@section('content')
    <h2>keys list</h2>

    @if(count($keys) === 0)
        <div class="alert alert-warning">no keys in redis</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>key</th>
                    <th>type</th>
                    <th>ttl</th>
                    <th>data</th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keys as $key)
                    <tr>
                        <td>{{ $key['key'] }}</td>
                        <td>{{ $key['type'] }}</td>
                        <td>{{ $key['ttl'] }}</td>
                        <td>{{ $key['value'] }}</td>
                        <td>
                            <a href="{{ route('show', ['key' => urlencode($key['key'])]) }}" class="btn btn-sm btn-primary">open</a>
                            <form method="POST" action="{{ route('delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key['key'] }}">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
