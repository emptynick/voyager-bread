<div class="form-group">
	<label>{{ __('bread::manager.on_value') }}</label>
	<input type="text" value="{{ $options['on'] or '' }}" class="form-control" placeholder="{{ __('bread::manager.on_value') }}" name="row[][options][on]">
</div>
<div class="form-group">
	<label>{{ __('bread::manager.off_value') }}</label>
	<input type="text" value="{{ $options['off'] or '' }}" class="form-control" placeholder="{{ __('bread::manager.off_value') }}" name="row[][options][off]">
</div>
<div class="form-group">
	<label>{{ __('bread::manager.checked') }}</label>
	<input type="checkbox" value="true" name="row[][options][checked]" {{ ((isset($options['checked']) && $options['checked']) ? 'checked' : '') }}>
</div>
