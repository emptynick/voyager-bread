<div class="form-group">
	<label>{{ __('bread::generic.placeholder') }}</label>
	<input type="text" value="{{ $options['placeholder'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.placeholder') }}" name="row[][options][placeholder]" data-name="placeholder">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.default_value') }}</label>
	<input type="color" value="{{ $options['value'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.default_value') }}" name="row[][options][value]" data-name="value">
</div>
