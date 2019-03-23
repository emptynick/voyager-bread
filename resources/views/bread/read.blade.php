@extends('voyager::master')
@section('page_title', __('bread::bread.read_name_singular', ['name' => $bread->getTranslation('name_singular')]))

@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li><a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a></li>
    <li><a href="{{ route('voyager.'.$bread->getTranslation('slug').'.index')}}">{{ $bread->getTranslation('name_plural') }}</a></li>
    <li class="active">{{ $bread->getTranslation($data->getKey()) }}</li>
</ol>
@endsection

@section('content')
<div class="page-content container-fluid" id="bread-read">
    <language-picker></language-picker>
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary panel-bordered">                
                <div class="panel-heading">
                    <h3 class="panel-title panel-icon"><i class="voyager-info-circled"></i> {{ __('bread::bread.read_name_singular', ['name' => $bread->getTranslation('name_singular')]) }}</h3>
                    <div class="panel-actions">
                        <a class="panel-action voyager-angle-up" data-toggle="panel-collapse" aria-hidden="true"></a>
                    </div>
                </div>

                <div class="panel-body" style="overflow: visible">
                    <formfield-base v-for="(formfield, key) in layout.formfields"
                                    :key="'field_'+key"
                                    :view="'read'"
                                    :type="slugify(formfield.type)"
                                    :layout-type="'view'"
                                    :layout="layout"
                                    :options="formfield.options"
                                    :value="data[formfield.options.field]">
                    </formfield-base>
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
    el: "#bread-read",
    data: {
        bread: {!! json_encode($bread) !!},
        layout: {!! json_encode($layout) !!},
        data: {!! json_encode($data) !!},
    },
    methods: {
        
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