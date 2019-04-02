@extends('voyager::master')
@section('page_title', __('bread::bread.browse_name_plural', ['name' => $bread->getTranslation('name_plural')]))

@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li><a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a></li>
    <li class="active">{{ $bread->getTranslation('name_plural') }}</li>
</ol>
@endsection

@section('content')
<div class="page-content container-fluid" id="bread-browse">
    <language-picker></language-picker>
    <vue-snotify></vue-snotify>
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary panel-bordered">                
                <div class="panel-heading">
                    <h3 class="panel-title panel-icon"><i class="voyager-info-circled"></i> {{ __('bread::bread.browse_name_plural', ['name' => $bread->getTranslation('name_plural')]) }}</h3>
                    <div class="panel-actions">
                        <a class="panel-action voyager-angle-up" data-toggle="panel-collapse" aria-hidden="true"></a>
                    </div>
                </div>

                <div class="panel-body" style="overflow: visible">
                    
                    <browse-table
                                ref="browse-table"
                                url-data="{{ route('voyager.'.$bread->getTranslation('slug').'.data') }}"
                                url-delete="{{ route('voyager.'.$bread->getTranslation('slug').'.destroy', '#') }}"
                                name-singular="{{ $bread->getTranslation('name_singular') }}"
                                name-plural="{{ $bread->getTranslation('name_plural') }}"
                                token="{{ csrf_token() }}"
                                :bread="{{ json_encode($bread) }}"
                                :layout="{{ json_encode($layout) }}"
                                :columns="{{ json_encode($layout->getColumnDefinitions()) }}"
                                :loading-image="'{{ voyager_asset('images/logo-icon.png') }}'" />
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
    el: "#bread-browse",
    mounted: function () {
        @localization
        this.$refs['browse-table'].getFromUrl();
        this.$refs['browse-table'].loadItems();
        
    },
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection