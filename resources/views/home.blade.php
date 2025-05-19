@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp

@section('content')
    <h2>{{ __('keys_list_title') }}</h2>

    @if(count($keys) === 0)
        <div class="alert alert-warning">{{ __('no_keyses_alert') }}</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('key') }}</th>
                    <th>{{ __('type') }}</th>
                    <th>{{ __('data') }}</th>
                    <th>{{ __('ttl') }}</th>
                    <th>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keys as $key)
                    <tr>
                        <td>{{ $key['key'] }}</td>
                        <td>{{ $key['type'] }}</td>
                        <td>{{ Str::limit($key['value'], 30) }}</td>
                        <td>{{ $key['ttl'] }}</td>
                        <td>
                            @if(!empty($key['key']))
                                <a href="{{ route('show', ['key' => urlencode($key['key'])]) }}" class="btn btn-sm btn-primary">{{ __('open_button') }}</a>
                            @endif
                            <form method="POST" action="{{ route('delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key['key'] }}">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('ask_delete') }}')">{{ __('delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
