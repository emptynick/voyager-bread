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
        <li><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</li>
    @else
        <li>
            <a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a>
        </li>
    @endif
    <li><a href="{{ route('voyager.bread.index') }}">{{ __('bread::manager.bread_manager') }}</a></li>
    @if(isset($bread))
        <li>{{ $bread->display_name_plural }}</li>
        <li>Edit</li>
    @else
        <li>{{ ucfirst($table) }}</li>
        <li>Add</li>
    @endif
</ol>
@endsection

@section('content')
<div class="page-content container-fluid">
    <form method="post" action="{{ route('voyager.bread.store') }}">
        <input type="hidden" name="table_name" value="{{ $table }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title panel-icon"><i class="voyager-bread"></i> {{ ucfirst($table) }} {{ __('voyager::bread.bread_info') }}</h3>
                    </div>

                    <div class="panel-body">
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
                                <input type="text" class="form-control" name="model" value="{{ (isset($bread) ? $bread->model : 'App\\'.studly_case(str_singular($table))) }}" placeholder="{{ __('voyager::bread.model_name') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>{{ __('voyager::bread.controller_name') }}</label>
                                <span class="voyager-question" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="{{ __('voyager::bread.controller_name_hint') }}"></span>
                                <input type="text" class="form-control" name="controller" value="{{ (isset($bread) ? $bread->controller : '') }}" placeholder="{{ __('voyager::bread.controller_name') }}">
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
            <button type="submit" class="btn pull-right btn-primary">{{ __('voyager::generic.save') }}</button>
        </form>
    </div>

    @stop

@section('javascript')
<!-- views, lists, roles -->
<script>
$('input[data-slug-origin]').each(function(i, el) {
    $(el).slugify({
        forceupdate: true,
    });
});
</script>
@endsection
