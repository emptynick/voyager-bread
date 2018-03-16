@extends('voyager::master')

@section('page_title', __('bread::manager.edit_view_for', ['bread' => $view->bread->display_name_plural]))

@section('page_header')
<h1 class="page-title">
	<i class="voyager-bread"></i>
	{{ __('bread::manager.edit_view_for', ['bread' => $view->bread->display_name_plural]) }}
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
	<li class="active"><a href="{{ route('voyager.bread-hook.index') }}">{{ __('bread::manager.bread_manager') }}</a></li>
	<li class="active"><a href="{{ route('voyager.bread-hook.edit', $view->bread->table_name) }}">{{ $view->bread->display_name_plural }}</a></li>
	<li class="active"><a href="{{ route('voyager.bread-hook.edit', $view->bread->table_name) }}#views">{{ __('bread::manager.views') }}</a></li>
	<li class="active">{{ $view->name }}</li>
	<li>{{ __('voyager.generic.edit') }}</li>
</ol>
@endsection

@section('content')
<div class="page-content container-fluid">
	<div class="row clearfix">
		<div class="col-md-8">
			<form method="post" action="{{ route('voyager.bread-hook.update.view') }}">
				{{ csrf_field() }}
				<input type="hidden" name="bread_name" value="{{ $view->bread->table_name }}">
				<input type="hidden" name="view_id" value="{{ $view->id }}">
				<div class="panel panel-primary panel-bordered">
					<div class="panel-heading">
						<h3 class="panel-title panel-icon"><i class="voyager-bread"></i> {{ __('bread::manager.edit_view_for', ['bread' => $view->bread->display_name_plural]) }}</h3>

					</div>

					<div class="panel-body bread-builder">
						<div class="ignore {{ (count($rows) > 0 ? 'hidden' : '') }}">
							<h3 class="text-center">{{ __('bread::manager.drag_formfields_here') }}</h3>
						</div>
						@foreach ($rows as $row)
							<div data-type="{{ $row->type }}" class="formfield">
							@if ($row->type == 'relationship')
							@include('bread::manager.partials.relationship-wrapper', [
                                'columns'	   => $columns,
                                'data'		   => $row->options,
								'id'		   => $row->id,
								'field'		   => $row->field,
								'width'		   => $row->width,
                                'relationship' => $view->bread->model->relationships[$row->options['relationship']],
                                'name'         => $row->options['relationship'],
                            ])
							@else
							@php
							$formf = Bread::formField($row->type);
							@endphp
							@include('bread::manager.partials.'.strtolower($formf->type).'-wrapper', [
								'columns'	=> $columns,
								'formfield' => $formf,
								'data'		=> $row->options,
								'id'		=> $row->id,
								'field'		=> $row->field,
								'width'		=> $row->width,
								'validation'=> $row->validation_rules,
							])
							@endif
							</div>
						@endforeach
					</div>
					<div class="panel-footer">
						<button type="submit" class="btn btn-success">{{ __('bread::manager.update_view') }}</button>
					</div>
				</div>
			</form>
		</div>
		<div class="col-md-4">
			<div class="panel panel-primary panel-bordered">
				<div class="panel-heading">
					<h3 class="panel-title panel-icon"><i class="voyager-settings"></i> {{ __('bread::generic.options') }}</h3>
				</div>

				<div class="panel-body">
					<div class="panel-group" id="optionsAccordion">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#optionsAccordion" href="#formfieldCollapse">{{ __('bread::manager.formfields') }}</a>
								</h4>
							</div>
							<div id="formfieldCollapse" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="formfield-container">
										@foreach (Bread::formfields() as $formfield)
										<button class="btn btn-default btn-xl" data-type="{{ $formfield->getCodename() }}" data-toggle="tooltip" title="{{ $formfield->getName() }}">
											{{ $formfield->getName() }}
										</button>
										@endforeach
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#optionsAccordion" href="#relationshipCollapse">{{ __('bread::manager.relationships') }}</a>
								</h4>
							</div>
							<div id="relationshipCollapse" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="formfield-container">
										@if (isset($view->bread->model->relationships))
										@foreach ($view->bread->model->relationships as $name => $relationship)
										@if(get_related_bread($relationship) === null) @continue @endif
										<button class="btn btn-default btn-xl" data-type="{{ $name }}" data-toggle="tooltip" title="{{ title_case($name) }} {{ __('bread::generic.relationship') }}">
											{{ title_case($name) }} {{ __('bread::generic.relationship') }}
										</button>
										@endforeach
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#optionsAccordion" href="#designCollapse">{{ __('bread::manager.design_elements') }}</a>
								</h4>
							</div>
							<div id="designCollapse" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="formfield-container">
										@foreach (Bread::formfields('designElement') as $formfield)
										<button class="btn btn-default btn-xl" data-type="{{ $formfield->getCodename() }}" data-toggle="tooltip" title="{{ $formfield->getName() }}">
											{{ $formfield->getName() }}
										</button>
										@endforeach
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#optionsAccordion" href="#optionsCollapse">{{ __('bread::generic.options') }}</a>
								</h4>
								<div class="panel-actions">
									<label>{{ __('bread::manager.live_update') }} <input type="checkbox" class="live-update"></label>
								</div>
							</div>
							<div id="optionsCollapse" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="options-container">
										<strong style="width:100%; text-align:center;">
											{{ __('bread::manager.please_select_formfield') }}
										</strong>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="mockups hidden">
	@foreach (Bread::formfields() as $formfield)
    <div data-type="{{ $formfield->getCodename() }}" class="formfield">
        @include('bread::manager.partials.formfield-wrapper', [
            'columns'	=> $columns,
            'formfield' => $formfield,
        ])
    </div>
    @endforeach

	@foreach (Bread::formfields('designElement') as $formfield)
    <div data-type="{{ $formfield->getCodename() }}" class="formfield">
        @include('bread::manager.partials.designelement-wrapper', [
            'formfield' => $formfield,
        ])
    </div>
    @endforeach

    @if (isset($view->bread->model->relationships))
    @foreach ($view->bread->model->relationships as $name => $relationship)
	@if(get_related_bread($relationship) === null) @continue @endif
    <div data-type="{{ $name }}" class="formfield">
    @include('bread::manager.partials.relationship-wrapper', [
        'columns'	   => $columns,
        'formfield'    => null,
        'relationship' => $relationship,
        'name'         => $name,
    ])
    </div>
    @endforeach
    @endif

	<!-- Design Elements -->

</div>
@stop
@section('css')
<style type="text/css">
.options-container .form-group label {
	font-weight: 500 !important;
	font-size: 16px;
}
</style>
@endsection
@section('javascript')
<script src="{{ asset('vendor/bread/js/jquery.sortable.js') }}"></script>
<script src="{{ asset('vendor/bread/js/view-builder.js') }}"></script>
<script>
$('.bread-builder').breadBuilder({});
$('.toggler').toggles();
$(document).ready(function() {
	$(".app-container").removeClass("expanded");
});
</script>
@endsection
