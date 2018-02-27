@if(isset($input))
<textarea {{ (isset($options['required']) && $options['required'] == 1) ? 'required' : '' }}
	class="form-control"
	data-name="{{ $options['label'] or '' }}"
	rows="{{ $options['rows'] or 5 }}"
	placeholder="{{ $options['placeholder'] or '' }}"
	name="{{ $name }}">{{ $content or (isset($options['value']) ? $options['value'] : '') }}</textarea>
@elseif (isset($mockup))
<textarea class="form-control" rows="{{ $options['rows'] or 5 }}" placeholder="{{ $options['placeholder'] or '' }}" disabled>{{ $options['value'] or '' }}</textarea>
@elseif (isset($output))
<div class="readmore">{{ mb_strlen($data) > 200 ? mb_substr($data, 0, 200) . ' ...' : $data }}</div>
@else
<div class="form-group">
	<label>{{ __('bread::generic.rows') }}</label>
	<input type="number" step="1" min="1" max="100" value="{{ $options['rows'] or 5 }}" class="form-control" placeholder="{{ __('bread::generic.rows') }}" name="element[][options][rows]" data-name="rows">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.placeholder') }}</label>
	<input type="text" value="{{ $options['placeholder'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.placeholder') }}" name="element[][options][placeholder]" data-name="placeholder">
</div>
<div class="form-group">
	<label>{{ __('voyager.bread.default_value') }}</label>
	<textarea class="form-control" placeholder="{{ __('bread::generic.default_value') }}" name="element[][options][value]" data-name="value">{{ $options['value'] or '' }}</textarea>
</div>
@endif
