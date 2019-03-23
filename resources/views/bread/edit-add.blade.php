@extends('voyager::master')
@section('page_title', __('bread::bread.read_name_singular', ['name' => $bread->getTranslation('name_singular')]))

@section('content')
<div class="page-content container-fluid" id="bread-edit-add">
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
                                        :value="data[formfield.options.field]">
                        </formfield-base>

                        <button class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@php debug(old()); @endphp
@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
<script>
var builder = new Vue({
    el: "#bread-edit-add",
    data: {
        bread: {!! json_encode($bread) !!},
        layout: {!! json_encode($layout) !!},
        data: {!! json_encode($data) !!},
        errors: {!! json_encode($errors->getMessages()) !!},
        url: '{{ $url }}'
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