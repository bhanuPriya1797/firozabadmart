@php
    $halfTypes = ['text', 'email', 'phone', 'number', 'file', 'select', 'checkbox', 'radio'];
    $fullTypes = ['textarea', 'editor'];
    $colClass = in_array($setting->type, $halfTypes) ? 'col-md-6' : 'col-md-12';

    $inputClass = ($setting->class ?? 'form-control') . ($errors->has($setting->key) ? ' is-invalid' : '');

    $validation = json_decode($setting->validation, true) ?? [];
    $fileRules = json_decode($setting->file_constraints, true) ?? [];
    $rawOptions = $setting->options ?? [];
    if (is_string($rawOptions)) {
        $decoded = json_decode($rawOptions, true);
        if (is_array($decoded)) {
            $rawOptions = $decoded;
        } else {
            $rawOptions = array_filter(array_map('trim', explode(',', $rawOptions)));
        }
    }
    $optionsArr = is_array($rawOptions) ? $rawOptions : [];

    $attrs = '';
    foreach ($validation as $k => $v) {
        if (is_bool($v)) {
            $attrs .= $v ? " {$k}" : '';
        } else {
            $attrs .= " {$k}=\"{$v}\"";
        }
    }
@endphp

<div class="{{ $colClass }}">
    <div class="mb-4">
        <label class="form-label" for="{{ $setting->key }}">{{ $setting->label }}</label>

        @if(in_array($setting->type, ['text', 'email', 'phone']))
            <input type="{{ $setting->type === 'phone' ? 'text' : $setting->type }}"
                   name="{{ $setting->key }}"
                   id="{{ $setting->key }}"
                   class="{{ $inputClass }}"
                   value="{{ old($setting->key, $setting->value) }}"
                   {!! $attrs !!}>

        @elseif($setting->type === 'textarea' || $setting->type === 'editor')
            <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" class="{{ $inputClass }}" rows="4" {!! $attrs !!}>{{ old($setting->key, $setting->value) }}</textarea>

        @elseif($setting->type === 'number')
            <input type="number"
                   name="{{ $setting->key }}"
                   id="{{ $setting->key }}"
                   class="{{ $inputClass }}"
                   value="{{ old($setting->key, $setting->value) }}"
                   min="1"
                   step="1"
                   {!! $attrs !!}>

        @elseif($setting->type === 'file')
        <input type="file" name="{{ $setting->key }}" class="{{ $inputClass }}"
            @if(isset($fileRules['types'])) accept="{{ implode(',', array_map(fn($t) => '.' . $t, $fileRules['types'])) }}" @endif
            @if(isset($fileRules['max_size'])) data-max-size="{{ $fileRules['max_size'] }}" @endif
        >

        <input type="hidden" name="delete_file[{{ $setting->key }}]" id="delete-file-{{ $setting->key }}" value="0">

        @if($setting->value)
            <div class="mt-2 d-flex align-items-center gap-3 preview-wrapper" id="preview-{{ $setting->key }}">
                <a href="{{ asset('storage/' . $setting->value) }}" target="_blank">
                    <img src="{{ asset('storage/' . $setting->value) }}" width="100" class="img-thumbnail" alt="">
                </a>

                <button type="button"
                        class="btn btn-sm btn-outline-danger delete-setting-file"
                        data-key="{{ $setting->key }}">
                    Delete
                </button>
            </div>
        @endif

        @elseif($setting->type === 'select')
            <select name="{{ $setting->key }}" class="{{ $inputClass }}">
                @foreach($optionsArr as $option)
                    @php
                        $optVal = is_array($option) ? ($option['value'] ?? '') : $option;
                        $optLabel = is_array($option) ? ($option['label'] ?? $optVal) : $option;
                        if ($setting->key === 'FRONT_TOUR_PACKAGES_PAGINATION_TYPE') {
                            $optLabel = strtolower($optVal) === 'ajax' ? 'Load More Button' : 'Normal Pagination';
                        } else {
                            $optLabel = is_string($optLabel) ? ucfirst($optLabel) : $optLabel;
                        }
                    @endphp
                    <option value="{{ $optVal }}" @selected(old($setting->key, $setting->value) == $optVal)>{{ $optLabel }}</option>
                @endforeach
            </select>

        @elseif($setting->type === 'checkbox')
            @foreach($optionsArr as $option)
                <div class="form-check">
                    <input type="checkbox"
                           name="{{ $setting->key }}[]"
                           class="form-check-input"
                           value="{{ $option }}"
                           id="{{ $setting->key . '_' . $loop->index }}"
                           @checked(is_array(json_decode($setting->value, true)) && in_array($option, json_decode($setting->value, true)))>
                    <label class="form-check-label" for="{{ $setting->key . '_' . $loop->index }}">{{ ucfirst($option) }}</label>
                </div>
            @endforeach
        @else
            <input type="text"
                   name="{{ $setting->key }}"
                   id="{{ $setting->key }}"
                   class="{{ $inputClass }}"
                   value="{{ old($setting->key, $setting->value) }}"
                   {!! $attrs !!}>
        @endif

        {{-- Error display --}}
        @error($setting->key)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
