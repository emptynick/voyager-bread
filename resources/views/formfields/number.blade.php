@if(isset($input))
<input {{ (isset($options['required']) && $options['required'] == 1 ? 'required' : '') }}
       type="number"
       data-name="{{ $options['display_name'] or '' }}"
       class="form-control"
       name="{{ $name }}"
       placeholder="{{ $options['placeholder'] or '' }}"
       min="{{ $options['min'] or '' }}"
       max="{{ $options['max'] or '' }}"
       step="{{ $options['step'] or '' }}"
       value="{{ $content or (isset($options['value']) ? $options['value'] : '') }}">
@elseif (isset($mockup))
<input type="text" class="form-control" placeholder="{{ $options['placeholder'] or '' }}" value="{{ $options['value'] or '' }}" disabled>
@elseif (isset($output))
<div class="readmore">{{ mb_strlen($data) > 200 ? mb_substr($data, 0, 200) . ' ...' : $data }}</div>
@else
<div class="form-group">
	<label>{{ __('bread::generic.placeholder') }}</label>
	<input type="text" value="{{ $options['placeholder'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.placeholder') }}" name="row[][options][placeholder]" data-name="placeholder">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.default_value') }}</label>
	<input type="text" value="{{ $options['value'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.default_value') }}" name="row[][options][value]" data-name="value">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.min') }}</label>
	<input type="number" value="{{ $options['min'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.min') }}" name="row[][options][min]">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.max') }}</label>
	<input type="number" value="{{ $options['max'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.max') }}" name="row[][options][max]">
</div>
<div class="form-group">
	<label>{{ __('bread::generic.step') }}</label>
	<input type="number" value="{{ $options['step'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.step') }}" name="row[][options][step]">
</div>
@endif
