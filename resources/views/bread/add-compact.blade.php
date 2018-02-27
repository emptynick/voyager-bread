<div class="row">
	@foreach($breadView->rows as $row)

	@if($row->field == 'relationship') @continue @endif
	<div class="col-md-{{ $row->width or 12 }}">
		<div class="panel panel-bordered panel-primary">
			<div class="panel-body">
				<div class="form-group has-feedback">
					<label><strong>{{ $row->options['label'] or '&nbsp;' }}</strong></label>
					{!! $row->formfield->createInput('', $row->options, $row->field) !!}
					<small>{{ $row->options['helptext'] or '&nbsp;' }}</small>
					<small class="text-danger error-message"></small>
				</div>
			</div>
		</div>
	</div>
	@endforeach
</div>
