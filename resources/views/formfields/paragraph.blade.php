@if(isset($input) || isset($output) || isset($mockup))
<p>{{ $options['text'] or 'Paragraph' }}</p>
@else
<div class="form-group">
	<label>{{ __('bread::generic.text') }}</label>
	<input type="text" value="{{ $options['text'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.text') }}" name="row[][options][text]" data-name="html">
</div>
@endif
