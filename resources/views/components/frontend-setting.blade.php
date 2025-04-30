@props(['type', 'name', 'label', 'value'])

<div class="form-group">
    @if($type === 'bool')
        <input type="hidden" name="{{ $name }}" value="0"/>
        <div class="checkbox">
            <label for="{{ $name }}">
                <input type="checkbox" name="{{ $name }}"
                       id="{{ $name }}" value="1"
                       @if($value === '1') checked @endif>
                {{ $label }}
            </label>
        </div>
    @elseif($type === 'int')
        <label for="{{ $name }}">{{ $label }}</label>
        <input type="number"  name="{{ $name }}" id="{{ $name }}" value="{{ $value }}" class="form-control">
    @elseif($type === 'string')
        <label for="{{ $name }}">{{ $label }}</label>
        <input type="text" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}" class="form-control">
    @endif
</div>
