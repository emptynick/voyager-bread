@if(isset($input) || isset($output) || isset($mockup))
<{{ $options['type'] or 'h2' }}>{{ $options['text'] or 'Heading' }}</{{ $options['type'] or 'h2' }}>
@else
<div class="form-group">
	<label>{{ __('bread::generic.text') }}</label>
	<input type="text" value="{{ $options['text'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.text') }}" name="row[][options][text]" data-name="html">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.type') }}</label>
    <select class="form-control" name="row[][options][type]" data-name="heading">
        <option value="h1"
        {{ ((isset($options['type']) && $options['type'] == 'h1') ? 'selected' : '') }}
        >H1</option>
        <option value="h2"
        {{ ((isset($options['type']) && $options['type'] == 'h2') ? 'selected' : '') }}
        >H2</option>
        <option value="h3"
        {{ ((isset($options['type']) && $options['type'] == 'h3') ? 'selected' : '') }}
        >H3</option>
        <option value="h4"
        {{ ((isset($options['type']) && $options['type'] == 'h4') ? 'selected' : '') }}
        >H4</option>
        <option value="h5"
        {{ ((isset($options['type']) && $options['type'] == 'h5') ? 'selected' : '') }}
        >H5</option>
        <option value="h6"
        {{ ((isset($options['type']) && $options['type'] == 'h6') ? 'selected' : '') }}
        >H6</option>
    </select>
</div>
@endif
