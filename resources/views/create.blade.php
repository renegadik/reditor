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

    <form action="{{ route('create') }}" method="POST" id="create-form">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('key') }}</label>
            <input type="text" name="key" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('type') }}</label>
            <select name="type" id="type-select" class="form-select" required onchange="handleTypeChange()">
                <option value="1">{{ __('string') }}</option>
                <option value="2">{{ __('set') }}</option>
                <option value="3">{{ __('list') }}</option>
                <option value="5">{{ __('hash') }}</option>
                {{-- <option value="4">{{ __('zset') }}</option> --}}
            </select>
        </div>

        <div class="mb-3" id="string-wrapper">
            <label class="form-label">{{ __('value') }}</label>
            <textarea name="value" class="form-control" rows="4" required placeholder="json if no str"></textarea>
        </div>

        <div class="mb-3" id="dynamic-fields" style="display: none;">
            <label class="form-label">{{ __('value') }}</label>
            <div id="field-container"></div>
            <button type="button" class="btn btn-outline-primary mt-2" onclick="addDynamicField()">+ {{ __('add_field') }}</button>
        </div>

        <button type="submit" class="btn btn-success mt-3">{{ __('create_button') }}</button>
        <a href="{{ route('home') }}" class="btn btn-secondary mt-3">{{ __('back_button') }}</a>
    </form>

    <script>
        function handleTypeChange() {
            const type = document.getElementById('type-select').value;
            const stringWrapper = document.getElementById('string-wrapper');
            const dynamicFields = document.getElementById('dynamic-fields');
            const fieldContainer = document.getElementById('field-container');

            fieldContainer.innerHTML = ''; // чистим всё

            const stringInput = stringWrapper.querySelector('textarea');

            if (type === '1') {
                stringWrapper.style.display = 'block';
                stringInput.setAttribute('required', 'required');
                dynamicFields.style.display = 'none';
            } else {
                stringWrapper.style.display = 'none';
                stringInput.removeAttribute('required');
                dynamicFields.style.display = 'block';
                addDynamicField();
            }

        }


        let fieldIndex = 0;

        function addDynamicField() {
            const type = document.getElementById('type-select').value;
            const container = document.getElementById('field-container');

            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-2 align-items-center';

            if (type === '4' || type === '5') {
                const index = fieldIndex++;
                div.innerHTML = `
                    <input type="text" name="fields[${index}][key]" class="form-control" placeholder="Key" required>
                    <input type="text" name="fields[${index}][value]" class="form-control" placeholder="Value" required>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.remove()">{{ __('delete') }}</button>
                `;
            } else {
                div.innerHTML = `
                    <input type="text" name="fields[]" class="form-control" placeholder="Value" required>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.remove()">{{ __('delete') }}</button>
                `;
            }

            container.appendChild(div);
        }

        document.addEventListener('DOMContentLoaded', () => {
            handleTypeChange();
        });
    </script>
@endsection
