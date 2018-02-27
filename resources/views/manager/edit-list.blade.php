@extends('voyager::master')

@section('page_title', __('bread::manager.edit_list_for', ['bread' => $view->bread->display_name_plural]))

@section('page_header')
<h1 class="page-title">
	<i class="voyager-bread"></i>
	{{ __('bread::manager.edit_list_for', ['bread' => $view->bread->display_name_plural]) }}
</h1>
@stop
@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
	@if(count(Request::segments()) == 1)
		<li class="active"><i class="voyager-boat"></i> {{ __('voyager.generic.dashboard') }}</li>
	@else
		<li class="active">
			<a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager.generic.dashboard') }}</a>
		</li>
	@endif
	<li class="active"><a href="{{ route('voyager.bread.index') }}">{{ __('bread::manager.bread_manager') }}</a></li>
	<li class="active"><a href="{{ route('voyager.bread.edit', $view->bread->table_name) }}">{{ $view->bread->display_name_plural }}</a></li>
	<li class="active"><a href="{{ route('voyager.bread.edit', $view->bread->table_name) }}#views">{{ __('bread::manager.lists') }}</a></li>
	<li class="active">{{ $view->name }}</li>
	<li>{{ __('voyager.generic.edit') }}</li>
</ol>
@endsection
@section('content')

<div class="page-content container-fluid">
	<div class="row clearfix">
		<div class="col-md-12">
			<div class="panel panel-primary panel-bordered">
				<div class="panel-heading">
					<h3 class="panel-title panel-icon"><i class="voyager-bread"></i> {{ __('bread::manager.edit_list_for', ['bread' => $view->bread->display_name_plural]) }}</h3>
				</div>

				<div class="panel-body">
					<form method="post" action="{{ route('voyager.bread.update.view') }}">
						{{ csrf_field() }}
						<input type="hidden" name="bread_name" value="{{ $view->bread->table_name }}">
						<input type="hidden" name="view_id" value="{{ $view->id }}">
						<div class="table-responsive">
							<table class="table table-bordered list-builder">
								<thead>
									<tr>
										<th></th>
										<th>{{ __('bread::generic.field') }}</th>
										<th>{{ __('bread::generic.label') }}</th>
										<th>{{ __('bread::generic.type') }}</th>
										<th>{{ __('bread::manager.searchable') }}</th>
										<th>{{ __('bread::manager.orderable') }}</th>
										<th>{{ __('bread::manager.invisible') }}</th>
										<th>{{ __('bread::generic.actions') }}</th>
									</tr>
								</thead>
								<tbody>
									@forelse($view->rows as $row)
									<tr>
										<input type="hidden" name="row[0][id]" value="{{ $row->id }}">
										<td style="font-size:16px;vertical-align:middle;-webkit-transform: rotate(-45deg);
  -moz-transform: rotate(-45deg);
  -o-transform: rotate(-45deg);
  -ms-transform: rotate(-45deg);
  transform: rotate(-45deg);" class="text-center">
											<i class="voyager-resize-full" data-move></i>
										</td>
										<td>
											<select class="form-control" name="row[0][column]">
												@foreach($columns as $column)
												<option value="{{ $column }}"
												{{ (($row['field'] == $column) ? 'selected' : '') }}>{{ $column }}</option>
												@endforeach
												@if (isset($view->bread->model->relationships))
												@foreach ($view->bread->model->relationships as $name => $relationship)
												@php
												$type = get_unqualified_class($relationship);
												@endphp
												@if($type != 'MorphTo')
												<optgroup label="{{ ucfirst($name) }}" style="font-weight:800;">
													@foreach(get_model_fields($relationship->getRelated()) as $rel_col)
													<option value="{{ get_field_name($name, $rel_col) }}"
													{{ (($row['field'] == get_field_name($name, $rel_col)) ? 'selected' : '') }}>{{ $name }}.{{ $rel_col }}</option>
													@endforeach
													@if($type == 'BelongsToMany')
													<optgroup label="{{ ucfirst($name) }} Pivot" style="font-weight:600;">
														@foreach(get_table_fields($relationship->getTable(), $relationship) as $piv_col)
														<option value="{{ get_field_name('pivot', $name, $piv_col) }}"
														{{ (($row['field'] == get_field_name('pivot', $name, $piv_col)) ? 'selected' : '') }}>{{ $piv_col }}</option>
														@endforeach
													</optgroup>
													@endif
												</optgroup>
												@endif
												@endforeach
												@endif
											</select>
										</td>
										<td>
											<input type="text" name="row[0][options][label]" value="{{ $row['options']['label'] or '' }}" class="form-control">
										</td>
										<td>
											<select class="form-control" name="row[0][formfield]">
												@foreach(Bread::formfields() as $formfield)
												<option value="{{ $formfield->getCodename() }}" {{ (($row['type'] == $formfield->getCodename()) ? 'selected' : '') }}>
												{{ $formfield->getName() }}</option>
												@endforeach
											</select>
										</td>
										<td>
											<input type="checkbox"
											   	   {{ ($row->isSearchable ? 'checked' : '') }}
												   class="form-check" name="row[0][options][searchable]" value="true">
										</td>
										<td>
											<input type="checkbox"
													{{ ($row->isOrderable ? 'checked' : '') }}
												   class="form-check" name="row[0][options][orderable]" value="true">
										</td>
										<td>
											<input type="checkbox"
													{{ (!$row->isVisible ? 'checked' : '') }}
												   class="form-check" name="row[0][options][invisible]" value="true">
										</td>
										<td>
											<button class="btn btn-danger" data-remove><i class="voyager-x"></i> {{ __('voyager.generic.delete') }}</button>
											<button class="btn btn-success" data-add><i class="voyager-plus"></i> {{ __('voyager.generic.add') }}</button>
										</td>
									</tr>
									@empty
									<tr>
										<input type="hidden" name="row[0][id]" value="null">
										<td style="vertical-align:middle" class="text-center">
											<i class="voyager-sort" data-move></i>
										</td>
										<td>
											<select class="form-control" name="row[0][column]">
												@foreach($columns as $column)
												<option value="{{ $column }}">{{ $column }}</option>
												@endforeach

											</select>
										</td>
										<td>
											<input type="text" name="row[0][options][label]" class="form-control">
										</td>
										<td>
											<select class="form-control" name="row[0][formfield]">
												@foreach(Bread::formfields() as $formfield)
												<option value="{{ $formfield->getCodename() }}">{{ $formfield->getName() }}</option>
												@endforeach
											</select>
										</td>
										<td>
											<input type="checkbox" class="form-check" checked name="row[0][options][searchable]" value="true" checked>
										</td>
										<td>
											<input type="checkbox" class="form-check" checked name="row[0][options][orderable]" value="true" checked>
										</td>
										<td>
											<input type="checkbox" class="form-check" name="row[0][options][invisible]" value="true">
										</td>
										<td>
											<button class="btn btn-danger" data-remove><i class="voyager-x"></i> {{ __('voyager.generic.delete') }}</button>
											<button class="btn btn-success" data-add><i class="voyager-plus"></i> {{ __('voyager.generic.add') }}</button>
										</td>
									</tr>
									@endforelse
								</tbody>
								<tfoot>
									<tr>
										<th></th>
										<th>{{ __('bread::generic.field') }}</th>
										<th>{{ __('bread::generic.label') }}</th>
										<th>{{ __('bread::generic.type') }}</th>
										<th>{{ __('bread::manager.searchable') }}</th>
										<th>{{ __('bread::manager.orderable') }}</th>
										<th>{{ __('bread::manager.invisible') }}</th>
										<th>{{ __('bread::generic.actions') }}</th>
									</tr>
								</tfoot>
							</table>
						</div>
						<button type="submit" class="btn btn-success">{{ __('voyager.generic.save') }}</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
@section('css')
<style type="text/css">
.sortable-ghost {
	background-color: #f0fced;
}
</style>
@stop
@section('javascript')
<script src="{{ asset('vendor/bread/js/jquery.sortable.js') }}"></script>
<script src="{{ asset('vendor/bread/js/list-builder.js') }}"></script>
<script>
$(document).ready(function(){
	$('.list-builder').listBuilder({});
});
</script>
@endsection
