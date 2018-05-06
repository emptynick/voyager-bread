@extends('voyager::master')

@section('page_title', __('voyager::bread.edit_bread_for_table', ['table' => $table]))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-bread"></i>
    {{ __('voyager::bread.edit_bread_for_table', ['table' => $table]) }}
</h1>
@stop

@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
	@if(count(Request::segments()) == 1)
		<li class="active"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</li>
	@else
		<li class="active">
			<a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a>
		</li>
	@endif
	<li class="active"><a href="{{ route('voyager.bread.index') }}">{{ __('bread::manager.bread_manager') }}</a></li>
	@if(isset($bread))
    <li class="active">{{ $bread->display_name_plural }}</li>
	<li>Edit</li>
    @else
    <li class="active">{{ ucfirst($table) }}</li>
	<li>Add</li>
    @endif
</ol>
@endsection

@section('content')
<div class="page-content container-fluid">
    <form method="post" action="{{ route('voyager.bread.store') }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title panel-icon"><i class="voyager-bread"></i> {{ ucfirst($table) }} {{ __('voyager::bread.bread_info') }}</h3>
                    </div>

                    <div class="panel-body">
                        <div class="row clearfix">
                            <div class="col-md-6 form-group">
                                <label for="name">{{ __('bread::manager.table_name') }}</label>
                                <input type="text" class="form-control" name="table_name" placeholder="{{ __('voyager::generic.table_name') }}"
                                       value="{{ $table }}"><!-- @todo: placeholder?! -->
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-4 form-group">
                                <label>{{ __('voyager::bread.display_name_singular') }}</label>
                                <input type="text" class="form-control" name="display_name_singular" value="{{ (isset($bread) ? $bread->display_name_singular : ucfirst(str_singular($table))) }}" placeholder="{{ __('voyager::bread.display_name_singular') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>{{ __('voyager::bread.display_name_plural') }}</label>
                                <input type="text" class="form-control" name="display_name_plural" value="{{ (isset($bread) ? $bread->display_name_plural : ucfirst($table)) }}" placeholder="{{ __('voyager::bread.display_name_plural') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>{{ __('voyager::bread.url_slug') }}</label>
                                <input type="text" class="form-control slug" data-slug-origin="display_name_plural" name="slug" value="{{ (isset($bread) ? $bread->slug : str_slug($table)) }}" placeholder="{{ __('voyager::bread.url_slug_ph') }}" data-slug-origin="display_name_plural" data-slug-forceupdate="true">
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-3 form-group">
                                <label>{{ __('voyager::bread.model_name') }}</label>
                                <span class="voyager-question" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="{{ __('voyager::bread.model_name_ph') }}"></span>
                                <input type="text" class="form-control" name="model_name" value="{{ (isset($bread) ? get_class($bread->model) : '') }}" placeholder="{{ __('voyager::bread.model_name') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('voyager::bread.controller_name') }}</label>
                                <span class="voyager-question" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="{{ __('voyager::bread.controller_name_hint') }}"></span>
                                <input type="text" class="form-control" name="controller_name" value="{{ (isset($bread) ? $bread->controller : '') }}" placeholder="{{ __('voyager::bread.controller_name') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('voyager::bread.policy_name') }}</label>
                                <span class="voyager-question" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="{{ __('voyager::bread.policy_name_ph') }}"></span>
                                <input type="text" class="form-control" name="policy_name" value="{{ (isset($bread) ? $bread->policy : '') }}" placeholder="{{ __('voyager::bread.policy_name') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('voyager::bread.icon_hint') }} <a
                                                                                 href="{{ route('voyager.compass.index', [], false) }}#fonts"
                                                                                 target="_blank">{{ __('voyager::bread.icon_hint') }}</a></label>
                                <input type="text" class="form-control" name="icon" value="{{ (isset($bread) ? $bread->icon : '') }}" placeholder="{{ __('voyager::bread.icon_hint2') }}">
                            </div>
                        </div>
                        @if (isset($bread))
                        <div class="row clearfix">
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.browse_list') }}</label>
                                <select class="form-control select2" data-placeholder="{{ __('bread::manager.select_list') }}" name="browse_list">
                                    <option></option>
                                    @foreach($bread->getLists() as $list)
                                    <option value="{{ $list->name }}" {{ ($bread->browse_list == $list->name) ? 'selected' : '' }}>{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.read_view') }}</label>
                                <select class="form-control select2" data-placeholder="{{ __('bread::manager.select_view') }}" name="read_view">
                                    <option></option>
                                    @foreach($bread->getViews() as $view)
                                    <option value="{{ $view->name }}" {{ ($bread->read_view == $view->name) ? 'selected' : '' }}>{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.edit_view') }}</label>
                                <select class="form-control select2" data-placeholder="{{ __('bread::manager.select_view') }}" name="edit_view">
                                    <option></option>
                                    @foreach($bread->getViews() as $view)
                                    <option value="{{ $view->name }}" {{ ($bread->edit_view == $view->name) ? 'selected' : '' }}>{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.add_view') }}</label>
                                <select class="form-control select2" data-placeholder="{{ __('bread::manager.select_view') }}" name="add_view">
                                    <option></option>
                                    @foreach($bread->getViews() as $view)
                                    <option value="{{ $view->name }}" {{ ($bread->add_view == $view->name) ? 'selected' : '' }}>{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if(isset($bread))
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ __('bread::manager.lists') }}</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="20%">{{ __('voyager::generic.name') }}</th>
                                    <th width="40%" style="text-align:right">{{ __('voyager::generic.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bread->getLists() as $list)
                                <tr>
                                    <td>{{ $list->name }}</td>
                                    <td class="actions">
                                        <div class="pull-right">
                                            <a href="{{ route('voyager.bread.edit.layout', [$table, $list->name]) }}" class="btn btn-sm btn-primary">
                                                <i class="voyager-edit"></i> {{ __('voyager::generic.edit') }}
                                            </a>
                                            <a href="{{ route('voyager.bread.delete.layout', $list->name) }}" class="btn btn-sm btn-danger delete-view">
                                                <i class="voyager-edit"></i> {{ __('voyager::generic.delete') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ __('bread::manager.views') }}</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="20%">{{ __('voyager::generic.name') }}</th>
                                    <th width="40%" style="text-align:right">{{ __('voyager::generic.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bread->getViews() as $view)
                                <tr>
                                    <td>{{ $view->name }}</td>
                                    <td class="actions">
                                        <div class="pull-right">
                                            <a href="{{ route('voyager.bread.edit.layout', [$table, $view->name]) }}" class="btn btn-sm btn-primary">
                                                <i class="voyager-edit"></i> {{ __('voyager::generic.edit') }}
                                            </a>
                                            <a href="{{ route('voyager.bread.delete.layout', $view->name) }}" class="btn btn-sm btn-danger delete-view">
                                                <i class="voyager-edit"></i> {{ __('voyager::generic.delete') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <button type="submit" class="btn pull-right btn-primary">{{ __('voyager::generic.save') }}</button>
    </form>
</div>

@stop

@section('javascript')
<script>
$('input[data-slug-origin]').each(function(i, el) {
    $(el).slugify({
        forceupdate: true,
    });
});
</script>
@endsection
