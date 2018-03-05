@if(isset($input) || isset($output) || isset($mockup))
<p>{!! isset($options['text']) ? nl2br($options['text']) : 'Paragraph' !!}</p>
@else
<div class="form-group">
	<label>{{ __('bread::generic.text') }}</label>
	<textarea class="form-control" placeholder="{{ __('bread::generic.text') }}" name="row[][options][text]" data-name="html">{{ $options['text'] or '' }}</textarea>
</div>
@endif
