<div class="form-group">
	<label>{{ __('bread::generic.placeholder') }}</label>
	<input type="text" value="{{ $options['placeholder'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.placeholder') }}" name="row[][options][placeholder]" data-name="placeholder">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.default_value') }}</label>
	<input type="text" value="{{ $options['value'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.default_value') }}" name="row[][options][value]" data-name="value">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.slug') }}</label>
	<input type="checkbox" {{ ((isset($options['slug']) && $options['slug']) ? 'checked' : '') }} class="form-check-input enables" data-enables=".origin" name="row[][options][slug]" value="true">
    <input type="text" value="{{ $options['slug_origin'] or '' }}" name="row[][options][slug_origin]" placeholder="{{ __('bread::generic.slug_origin') }}" class="form-control origin" {{ ((isset($options['slug']) && $options['slug']) ? '' : 'disabled') }}>
</div>
