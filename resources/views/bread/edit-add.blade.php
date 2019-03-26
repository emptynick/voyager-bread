@extends('voyager::master')
@section('page_title', __('bread::bread.'.($data ? 'edit' : 'add').'_name_singular', ['name' => $bread->getTranslation('name_singular')]))

@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li><a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a></li>
    <li><a href="{{ route('voyager.'.$bread->getTranslation('slug').'.index')}}">{{ $bread->getTranslation('name_plural') }}</a></li>
    <li class="active">{{ __('bread::bread.'.($data ? 'edit' : 'add').'_name_singular', ['name' => $bread->getTranslation('name_singular')]) }}</li>
</ol>
@endsection

@section('content')
<div class="page-content container-fluid" id="bread-edit-add">
    <language-picker></language-picker>
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary panel-bordered">                
                <div class="panel-heading">
                    <h3 class="panel-title panel-icon"><i class="voyager-info-circled"></i> {{ __('bread::bread.'.($data ? 'edit' : 'add').'_name_singular', ['name' => $bread->getTranslation('name_singular')]) }}</h3>
                    <div class="panel-actions">
                        <a class="panel-action voyager-angle-up" data-toggle="panel-collapse" aria-hidden="true"></a>
                    </div>
                </div>

                <div class="panel-body" style="overflow: visible">
                    <form method="POST" :action="url" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="PUT" v-if="data">
                        {{ csrf_field() }}
                        <formfield-base v-for="(formfield, key) in layout.formfields"
                                        :key="'field_'+key"
                                        :view="data ? 'edit' : 'add'"
                                        :type="slugify(formfield.type)"
                                        :layout-type="'view'"
                                        :layout="layout"
                                        :options="formfield.options"
                                        :value="getValue(formfield.options.field)"
                                        :errors="getErrors(formfield.options.field)"
                                        :relationships="relationships"
                                        token="{{ csrf_token() }}">
                        </formfield-base>
                        <div class="clearfix"></div>
                        <button class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
<script>
var builder = new Vue({
    el: "#bread-edit-add",
    data: {
        bread: {!! json_encode($bread) !!},
        layout: {!! json_encode($layout) !!},
        data: {!! json_encode($data) !!},
        old: {!! json_encode(old()) !!},
        errors: {!! json_encode($errors->getMessages(), JSON_FORCE_OBJECT) !!},
        url: '{{ $url }}',
        relationships: {!! json_encode(Bread::getBreadRelationships($bread->table)) !!},
    },
    methods: {
        getValue: function (field) {
            return this.old[field] || (this.data ? this.data[field] : '');
        },
        getErrors: function (field) {
            var errors = [];
            for (var error in this.errors) {
                if (error == field || error.startsWith(field+'.')) {
                    errors.push(this.errors[error][0]);
                }
            }

            return errors;
        }
    },
    computed: {
        
    },
    mounted: function () {
        @localization
    },
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection