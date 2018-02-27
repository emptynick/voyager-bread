<div class="col-md-{{ $width or 12 }} cell">
	<div class="panel panel-bordered panel-primary panel-relationship">
		<div class="panel-heading">
			<h3 class="panel-title"></h3>
			<div class="panel-actions">
				<a class="panel-action voyager-trash cell-delete"></a>
				<a class="panel-action voyager-resize-full cell-resize"></a>
			</div>
		</div>
		<div class="panel-body">
			<div class="content">
				<div class="form-group">
					<label>{{ $data['label'] or '' }}</label>
					<br>
					<small>{{ $data['helptext'] or '' }}</small>
				</div>
			</div>
			<div class="options hidden">
				<input type="hidden" name="row[][formfield]" value="{{ (isset($relationship) ? 'relationship' : '') }}">
				<input type="hidden" name="row[][id]" value="{{ $id or 'null' }}">
				<input type="hidden" name="row[][width]" value="{{ $width or 12 }}" class="row-width">

				<div class="form-group">
					<label>{{ __('bread::generic.label') }}</label>
					<input type="text" value="{{ $data['label'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.label') }}" name="row[][options][label]" data-name="label">
				</div>
				<div class="form-group">
					<label>{{ __('bread::generic.help_text') }}</label>
					<input type="text" value="{{ $data['helptext'] or '' }}" class="form-control" placeholder="{{ __('bread::generic.help_text') }}" name="row[][options][helptext]" data-name="helptext">
				</div>

				@php $related_bread = get_related_bread($relationship); @endphp
				@if($related_bread !== null)
					<input type="hidden" name="row[][column]" value="none">
					<input type="hidden" name="row[][options][relationship]" value="{{ $name }}">
					<!-- Browse list and add view for relationships -->
					<div class="form-group">
					    <label>{{ __('bread::manager.browse_list') }}</label>
					    <select class="form-control" name="row[][options][list_id]">
	                        <option value="null" selected>{{ __('bread::generic.none') }}</option>
					        @foreach($related_bread->lists as $l)
					        <option value="{{ $l->id }}"
					        {{ ((isset($data['list_id']) && $data['list_id'] == $l->id) ? 'selected' : '') }}>{{ $l->name }}</option>
					        @endforeach
					    </select>
					</div>
					<div class="form-group">
					    <label>{{ __('bread::manager.add_view') }}</label>
					    <select class="form-control" name="row[][options][view_id]">
	                        <option value="null" selected>{{ __('bread::generic.none') }}</option>
					        @foreach($related_bread->views as $v)
					        <option value="{{ $v->id }}"
					        {{ ((isset($data['view_id']) && $data['view_id'] == $v->id) ? 'selected' : '') }}>{{ $v->name }}</option>
					        @endforeach
					    </select>
					</div>
				@endif
				<div class="form-group">
					<label>{{ __('bread::manager.validation_rules') }}</label>
					<table class="table table-responsive repeater">
						<thead>
							<tr>
								<th>{{ __('bread::manager.rule') }}</th>
								<th>{{ __('bread::manager.message') }}</th>
								<th>{{ __('bread::generic.actions') }}</th>
							</tr>
						</thead>
						<tbody>
							@if(isset($validation))
							@foreach($validation as $rule => $message)
							<tr class="repeater-item">
								<td>
									<input type="text" value="{{ $rule }}" class="form-control" name="row[][validation][rules][{{ $loop->index }}][rule]">
								</td>
								<td>
									<input type="text" value="{{ $message }}" class="form-control" name="row[][validation][rules][{{ $loop->index }}][message]">
								</td>
								<td class="btn-group inline">
									<button class="btn btn-sm btn-success repeater-add"><i class="voyager-plus"></i></button>
				                    <button class="btn btn-sm btn-danger repeater-delete"><i class="voyager-x"></i></button>
								</td>
							</tr>
							@endforeach
							@else
							<tr class="repeater-item">
								<td>
									<input type="text" value="" class="form-control" name="row[][validation][rules][][rule]">
								</td>
								<td>
									<input type="text" value="" class="form-control" name="row[][validation][rules][][message]">
								</td>
								<td class="btn-group inline">
									<button class="btn btn-sm btn-success repeater-add"><i class="voyager-plus"></i></button>
				                    <button class="btn btn-sm btn-danger repeater-delete"><i class="voyager-x"></i></button>
								</td>
							</tr>
							@endif
						</tbody>
					</table>
				</div>
				<button role="button" class="btn btn-success save-options">{{ __('bread::generic.save') }}</button>
			</div>
		</div>
	</div>
</div>
