@props([
    'name' => '',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'placeholder' => '',
    'helpText' => null,
    'rows' => 3,
    'options' => [],
    'id' => null
])

<div class="form-control">
    @if($label)
        <label for="{{ $id ?? $name }}" class="label">
            <span class="label-text">{{ $label }}</span>
            @if($required)
                <span class="label-text-alt text-error">*</span>
            @endif
        </label>
    @endif

    @if($type === 'select')
        <select 
            name="{{ $name }}" 
            id="{{ $id ?? $name }}"
            class="select select-bordered w-full"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}
        >
            <option value="">Selecione...</option>
            @foreach($options as $key => $option)
                <option value="{{ $key }}" @selected($key == old($name, $value))>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea 
            name="{{ $name }}" 
            id="{{ $id ?? $name }}"
            rows="{{ $rows }}"
            class="textarea textarea-bordered w-full"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'file')
        <input 
            type="file" 
            name="{{ $name }}" 
            id="{{ $id ?? $name }}"
            class="file-input file-input-bordered w-full"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}
        >
    @else
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $id ?? $name }}"
            value="{{ old($name, $value) }}"
            class="input input-bordered w-full"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}
        >
    @endif

    @if($helpText)
        <div class="label">
            <span class="label-text-alt">{{ $helpText }}</span>
        </div>
    @endif

    @error($name)
        <div class="label">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </div>
    @enderror
</div>