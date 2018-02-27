<select class="form-control" {{ ((isset($options['multiple']) && $options['multiple']) ? 'multiple' : '') }}>
    @if (isset($options['options']))
    @foreach($options['options'] as $option)
    <option value="{{ $option['key'] or '' }}" {{ ((isset($option['default']) && boolval($option['default'])) ? 'selected' : '') }}>{{ $option['value'] or '' }}</option>
    @endforeach
    @endif
</select>
