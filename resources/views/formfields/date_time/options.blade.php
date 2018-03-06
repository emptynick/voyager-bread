<div class="form-group">
	<label>{{ __('bread::generic.type') }}</label>
	<select class="form-control" name="row[][options][type]" data-name="type">
		@foreach(__('bread::manager.date_time_types') as $key => $type)
		<option value="{{ $key }}" {{ ((isset($options['type']) && $options['type'] == $key) ? 'checked' : '') }}>{{ $type }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label>{{ __('bread::generic.format') }}</label>
	<input type="text" value="{{ $options['format'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.format') }}" name="row[][options][format]" data-name="format">
</div>
