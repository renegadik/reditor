@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">{{ __('key') }}: <strong>{{ $key }}</strong></h2>
        <h4 class="mb-0">{{ __('type') }}: <strong>{{ $type }}</strong></h4>
        <form method="POST" action="{{ route('delete') }}" class="d-inline mb-0">
            @csrf
            <input type="hidden" name="key" value="{{ $key }}">
            <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('ask_delete') }}')">
                {{ __('delete') }}
            </button>
        </form>
    </div>

    <p><strong>{{ __('ttl') }}:</strong> {{ $ttl }}</p>

    @if (is_string($value))
    <form method="POST" action="{{ route('update_key') }}">
        @csrf
        <input type="hidden" name="key" value="{{ $key }}">

        <div id="string-view" ondblclick="editString()" class="border p-3 rounded bg-white cursor-pointer" style="white-space: pre-wrap;">
            <span id="string-value" style="display: block; white-space: pre-wrap;">{{ is_array(json_decode($value, true)) ? json_encode(json_decode($value, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value }}</span>
        </div>

        <div id="string-edit" style="display: none; position: relative;" class="mt-3">
            <textarea name="value" id="string-input"
                    class="form-control pe-5"
                    style="min-height: 150px; overflow:hidden;"
                    onkeydown="submitOnEnter(event)"
                    oninput="autoResize(this)"
                    onfocus="autoResize(this)">
                {{ is_array(json_decode($value, true)) ? json_encode(json_decode($value, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value }}
            </textarea>

            <button type="submit" class="btn btn-primary" style="position: absolute; right: 0px; top: 0;">
                {{ __('save_button') }}
            </button>
        </div>
    </form>

    @elseif (is_array($value))
        <div style="position: relative;">
            <form method="POST" action="{{ route('update_key') }}">
                @csrf
                <input type="hidden" name="key" value="{{ $key }}">

                <table class="table table-bordered bg-white">
                    <thead>
                        <tr>
                            <th>{{ __('key') }}</th>
                            <th>{{ __('value') }}</th>
                            <th>{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="data-table-body">
                        @foreach ($value as $k => $v)
                            <tr>
                                <td>{{ $k }}</td> 
                                <td ondblclick="editCell('{{ $k }}')" id="cell-{{ $k }}">
                                    <div id="view-{{ $k }}"><span id="value-{{ $k }}">{{ $v }}</span></div>
                            
                                    <div id="edit-{{ $k }}" style="display: none; position: relative;">
                                        <form method="POST" action="{{ route('update_key') }}">
                                            @csrf
                                            <input type="hidden" name="key" value="{{ $key }}">
                                            <input type="text" name="value[{{ $k }}]" id="input-{{ $k }}" class="form-control pe-5" value="{{ $v }}" onkeydown="submitOnEnter(event)">
                                            <button type="submit" class="btn btn-primary" style="position: absolute; right: 0px; top: 50%; transform: translateY(-50%);">
                                                {{ __('update') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            
                                <td class="text-center">
                                    <form method="POST" action="{{ route('delete_subkey') }}">
                                        @csrf
                                        <input type="hidden" name="key" value="{{ $key }}">
                                        <input type="hidden" name="sub_key" value="{{ $k }}">
                                        <input type="hidden" name="sub_value" value="{{ $v }}">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('{{ __('ask_delete') }}')">{{ __('delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>

            <button type="button" class="btn btn-sm btn-outline-primary" style="position: absolute; right: 0; bottom: -45px;" onclick="addField()">
                + {{ __('add_field') }}
            </button>
        </div>
    @else
        <pre class="bg-white p-3 border rounded">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    @endif

    <a href="{{ route('home') }}" class="btn btn-secondary mt-5">← {{ __('back_button') }}</a>

    <script>
        function editString() {
            document.getElementById('string-view').style.display = 'none';
            document.getElementById('string-edit').style.display = 'block';
            const input = document.getElementById('string-input');
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
            autoResize(input);
        }


        function editCell(key) {
            document.getElementById('view-' + key).style.display = 'none';
            const editBlock = document.getElementById('edit-' + key);
            editBlock.style.display = 'block';
            const input = document.getElementById('input-' + key);
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }

        function submitOnEnter(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                event.target.form.submit();
            }
        }

        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }


        function addField() {
            const tbody = document.getElementById('data-table-body');
            if (document.getElementById('new-field-row')) return;

            const isHash = '{{ $type }}' === 'hash'; 

            const row = document.createElement('tr');
            row.id = 'new-field-row';

            row.innerHTML = `
                <td colspan="3">
                    <form method="POST" action="{{ route('add_subkey') }}" class="d-flex gap-2 align-items-center">
                        @csrf
                        <input type="hidden" name="key" value="{{ $key }}">
                        <input type="text" name="new_key" class="form-control" placeholder="{{ __('new_key_title') }}" ${isHash ? 'required' : 'disabled'}>
                        <input type="text" name="new_value" class="form-control" placeholder="{{ __('new_value') }}" required>
                        <button type="submit" class="btn btn-sm btn-success">{{ __('save_button') }}</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeField()">{{ __('delete') }}</button>
                    </form>
                </td>
            `;

            tbody.appendChild(row);
        }

        function removeField() {
            const row = document.getElementById('new-field-row');
            if (row) row.remove();
        }

        document.addEventListener('click', function (e) {
            const isInsideEditor = e.target.closest('form');

            const stringEdit = document.getElementById('string-edit');
            const stringView = document.getElementById('string-view');

            if (stringEdit && stringEdit.style.display === 'block' && !stringEdit.contains(e.target)) {
                stringEdit.style.display = 'none';
                stringView.style.display = 'block';
            }

            @if (is_array($value))
                @foreach ($value as $k => $v)
                    const editBlock{{ $loop->index }} = document.getElementById('edit-{{ $k }}');
                    const viewBlock{{ $loop->index }} = document.getElementById('view-{{ $k }}');

                    if (editBlock{{ $loop->index }} && editBlock{{ $loop->index }}.style.display === 'block' && !editBlock{{ $loop->index }}.contains(e.target)) {
                        editBlock{{ $loop->index }}.style.display = 'none';
                        viewBlock{{ $loop->index }}.style.display = 'block';
                    }
                @endforeach
            @endif
        });
    </script>
@endsection
