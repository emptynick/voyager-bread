@extends('voyager::master')

@section('page_title', __('bread::manager.edit_bread_for_table', ['table' => (isset($bread) ? $bread->table_name : $table)]))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-bread"></i>
    {{ __('bread::manager.edit_bread_for_table', ['table' => (isset($bread) ? $bread->table_name : $table)]) }}
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
                        <h3 class="panel-title panel-icon"><i class="voyager-bread"></i> {{ ucfirst((isset($bread) ? $bread->table_name : $table)) }} {{ __('bread::manager.bread_info') }}</h3>
                    </div>

                    <div class="panel-body">
                        <div class="row clearfix">
                            <div class="col-md-6 form-group">
                                <label for="name">{{ __('bread::manager.table_name') }}</label>
                                <input type="text" class="form-control" name="table_name" placeholder="{{ __('generic_name') }}"
                                       value="{{ (isset($bread) ? $bread->table_name : $table) }}"><!-- @todo: placeholder?! -->
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-4 form-group">
                                <label>{{ __('bread::manager.display_name_singular') }}</label>
                                <input type="text" class="form-control" name="display_name_singular" value="{{ (isset($bread) ? $bread->display_name_singular : ucfirst(str_singular($table))) }}" placeholder="{{ __('bread::manager.display_name_singular') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>{{ __('bread::manager.display_name_plural') }}</label>
                                <input type="text" class="form-control" name="display_name_plural" value="{{ (isset($bread) ? $bread->display_name_plural : ucfirst($table)) }}" placeholder="{{ __('bread::manager.display_name_plural') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>{{ __('bread::manager.url_slug') }}</label>
                                <input type="text" class="form-control slug" data-slug-origin="display_name_plural" name="slug" value="{{ (isset($bread) ? $bread->slug : str_slug($table)) }}" placeholder="{{ __('bread::manager.url_slug_ph') }}" data-slug-origin="display_name_plural" data-slug-forceupdate="true">
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.model_name') }}</label>
                                <span class="voyager-question" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="{{ __('bread::manager.model_name_ph') }}"></span>
                                <input type="text" class="form-control" name="model_name" value="{{ (isset($bread) ? $bread->model_name : '') }}" placeholder="{{ __('bread::manager.model_class') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.controller_name') }}</label>
                                <span class="voyager-question" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="{{ __('bread::manager.controller_name_hint') }}"></span>
                                <input type="text" class="form-control" name="controller_name" value="{{ (isset($bread) ? $bread->controller_name : '') }}" placeholder="{{ __('bread::manager.controller_name') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.policy_name') }}</label>
                                <span class="voyager-question" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="{{ __('bread::manager.policy_name_ph') }}"></span>
                                <input type="text" class="form-control" name="policy_name" value="{{ (isset($bread) ? $bread->policy_name : '') }}" placeholder="{{ __('bread::manager.policy_name') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.icon_hint') }} <a
                                                                                 href="{{ route('voyager.compass.index', [], false) }}#fonts"
                                                                                 target="_blank">{{ __('bread::manager.icon_hint2') }}</a></label>
                                <input type="text" class="form-control" name="icon" value="{{ (isset($bread) ? $bread->icon : '') }}" placeholder="{{ __('bread::manager.icon_class') }}">
                            </div>
                        </div>
                        @if (isset($bread))
                        <div class="row clearfix">
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.browse_list') }}</label>
                                <select class="form-control select2" data-placeholder="{{ __('bread::manager.select_list') }}" name="browse_list">
                                    <option></option>
                                    @foreach($bread->lists as $list)
                                    <option value="{{ $list->id }}" {{ ($bread->browse_list == $list->id) ? 'selected' : '' }}>{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.read_view') }}</label>
                                <select class="form-control select2" data-placeholder="{{ __('bread::manager.select_view') }}" name="read_view">
                                    <option></option>
                                    @foreach($bread->views as $view)
                                    <option value="{{ $view->id }}" {{ ($bread->read_view == $view->id) ? 'selected' : '' }}>{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.edit_view') }}</label>
                                <select class="form-control select2" data-placeholder="{{ __('bread::manager.select_view') }}" name="edit_view">
                                    <option></option>
                                    @foreach($bread->views as $view)
                                    <option value="{{ $view->id }}" {{ ($bread->edit_view == $view->id) ? 'selected' : '' }}>{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('bread::manager.add_view') }}</label>
                                <select class="form-control select2" data-placeholder="{{ __('bread::manager.select_view') }}" name="add_view">
                                    <option></option>
                                    @foreach($bread->views as $view)
                                    <option value="{{ $view->id }}" {{ ($bread->add_view == $view->id) ? 'selected' : '' }}>{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (isset($bread))
        <div class="row">
            <a name="views"></a>
            <div class="col-md-6">
                <div class="panel panel-primary panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title panel-icon"><i class="voyager-window-list"></i> {{ __('bread::manager.views') }}</h3>
                    </div>

                    <div class="panel-body">
                        <div class="row clearfix">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('voyager.generic.name') }}</th>
                                        <th>{{ __('bread::manager.rows') }}</th>
                                        <th style="text-align:right">{{ __('voyager.generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bread->views as $view)
                                    <tr>
                                        <td>{{ $view->name }}</td>
                                        <td>{{ $view->rows->count() }}</td>
                                        <td class="actions">
                                            <div class="pull-right">
                                                <a href="{{ route('voyager.bread.edit.view', $view) }}" class="btn btn-sm btn-primary">
                                                    <i class="voyager-edit"></i> {{ __('voyager.generic.edit') }}
                                                </a>
                                                <a href="{{ route('voyager.bread.delete.view', $view) }}" class="btn btn-sm btn-danger delete-view">
                                                    <i class="voyager-edit"></i> {{ __('voyager.generic.delete') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success pull-right add-view-modal" data-type="view">
                                {{ __('bread::manager.add_view') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-primary panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title panel-icon"><i class="voyager-list"></i> {{ __('bread::manager.lists') }}</h3>
                    </div>

                    <div class="panel-body">
                        <div class="row clearfix">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('voyager.generic.name') }}</th>
                                        <th>{{ __('bread::manager.rows') }}</th>
                                        <th style="text-align:right">{{ __('voyager.generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bread->lists as $list)
                                    <tr>
                                        <td>{{ $list->name }}</td>
                                        <td>{{ $list->rows->count() }}</td>
                                        <td class="actions">
                                            <div class="pull-right">
                                                <a href="{{ route('voyager.bread.edit.view', $list) }}" class="btn btn-sm btn-primary">
                                                    <i class="voyager-edit"></i> {{ __('voyager.generic.edit') }}
                                                </a>
                                                <a href="{{ route('voyager.bread.delete.view', $list) }}" class="btn btn-sm btn-danger delete-view">
                                                    <i class="voyager-edit"></i> {{ __('voyager.generic.delete') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success pull-right add-view-modal" data-type="list">{{ __('bread::manager.add_list') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <button type="submit" class="btn pull-right btn-primary">{{ __('voyager.generic.save') }}</button>
    </form>
</div>

@if (isset($bread))
<div class="modal fade" id="addviewmodal" tabindex="-1" role="dialog" aria-labelledby="addviewmodallabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('voyager.bread.store.view') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="addviewmodallabel">{{ __('bread::manager.add_view') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager.generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="bread_id" value="{{ $bread->id }}">
                    <input type="hidden" name="view_type" value="view">
                    <div class="form-group">
                        <label class="col-form-label">{{ __('voyager.generic.name') }}:</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('voyager.generic.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('voyager.generic.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@stop

@section('javascript')
<script>
$('input[data-slug-origin]').each(function(i, el) {
    $(el).slugify({
        forceupdate: true,
    });
});
$('.add-view-modal').on('click', function(e) {
    e.preventDefault();
    var type = $(this).data('type');
    $modal = $('#addviewmodal');
    if(type == 'view') {
        $modal.find('#addviewmodallabel').text('{{ __('bread::manager.add_view') }}');
        $modal.find('input[name="view_type"]').val('view');
    } else {
        $modal.find('#addviewmodallabel').text('{{ __('bread::manager.add_list') }}');
        $modal.find('input[name="view_type"]').val('list');
    }

    $modal.modal('show');
});

$('.delete-view').on('click', function(e) {
    e.preventDefault();
    var url = $(this).prop('href');
    toastr.info('{{ __('bread::manager.delete_view_question') }}<br /><br /><a href="'+url+'" class="btn btn-danger">{{ __('voyager.generic.yes') }}</button>', '{{ __('bread::manager.delete_view') }}');
    toastr.options = {
        'escapeHtml': false,
    };
});

</script>
@endsection
