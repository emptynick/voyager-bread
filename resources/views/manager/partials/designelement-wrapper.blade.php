<div class="col-md-{{ $width or 12 }} cell row">
	<div class="pull-right">
		<a class="voyager-trash cell-delete"></a>
		<a class="voyager-resize-full cell-resize"></a>
	</div>
	<input type="hidden" name="row[][column]" value="design_element">
	<div class="content">
		@if (isset($formfield))
		{!! $formfield->createMockup((isset($data) ? $data : null)) !!}
		@endif
	</div>

	<div class="options hidden">
		<input type="hidden" name="row[][formfield]" value="{{ (isset($formfield) ? $formfield->getCodename() : '') }}">
		<input type="hidden" name="row[][id]" value="{{ $id or 'null' }}">
		<input type="hidden" name="row[][width]" value="{{ $width or 12 }}" class="row-width">
		@if (isset($formfield))
		{!! $formfield->createOptions((isset($data) ? $data : null)) !!}
		@endif
		<button role="button" class="btn btn-success save-options">{{ __('bread::generic.save') }}</button>
	</div>
</div>
