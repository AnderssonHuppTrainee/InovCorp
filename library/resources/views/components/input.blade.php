@props([
    'name',          // Nome 
    'label' => null, 
    'type' => 'text',// Tipo do input (text, email, password, select, etc.)
    'value' => null, 
    'required' => false, // Se o campo e obrigatório
    'placeholder' => '', // Texto do placeholder
    'options' => [], 
    'helpText' => null, // Texto de ajuda
    'disabled' => false, // campo desabilitado
    'readonly' => false, // Se o campo é somente leitura
])

<div class="form-control">
    @if($label)
        <label for="{{ $name }}" class="label">
            <span class="label-text">{{ $label }}</span>
            @if($required)
                <span class="label-text-alt text-error">*</span>
            @endif
        </label>
    @endif

    @if($type === 'select')
        <select 
            name="{{ $name }}" 
            id="{{ $name }}"
            class="select select-bordered w-full"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {{ $attributes }}
        >
            <option value="">Selecione...</option>
            @foreach($options as $key => $option)
                <option 
                    value="{{ $key }}" 
                    @selected($key == old($name, $value))
                >
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea 
            name="{{ $name }}" 
            id="{{ $name }}"
            class="textarea textarea-bordered w-full"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {{ $attributes }}
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'file')
        <input 
            type="file" 
            name="{{ $name }}" 
            id="{{ $name }}"
            class="file-input file-input-bordered w-full"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}
        >
    @else
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="input input-bordered w-full"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
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