@extends('voyager::master')
@section('page_title', __('voyager::generic.read').' '.get_translated_value($bread->display_name_singular))

@section('content')
<div id="bread-read">
    <vue-snotify></vue-snotify>
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $bread->icon }}"></i>
            {{ __('voyager::generic.read').' '.get_translated_value($bread->display_name_singular) }}
        </h1>

        <language-switcher :languages="{{ json_encode(config('voyager.multilingual.locales')) }}"></language-switcher>

    </div>
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @include('voyager::alerts')

                        <div v-for="(item, key) in elements" :class="'col-md-'+item.width">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <component
                                            :is="'formfield-'+item.codename"
                                            :options="item.options"
                                            :computed="item.computed"
                                            :name="item.field"
                                            :show="'read'"
                                            :input="getContent(item)"
                                            :is-translatable="item.computed.isTranslatable"
                                        ></component>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <button type="submit" name="submit_action" value="" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>
</script>
<script src="{{ route('voyager.bread.scripts') }}"></script>
@foreach(\Bread\BreadFacade::formfields() as $formfield)
@include($formfield->getComponent())
@endforeach
@include('bread::components.language-switcher')
@include('bread::components.language-input')
<script>
new Vue({
    el: "#bread-read",
    data: {
        elements: {!! $layout->elements->toJson() !!},
        content: {!! collect($content)->merge(old())->toJson() ?? 'null' !!},
    },
    methods: {
        getContent: function(item) {
            if (this.content) {
                return this.content[item.field] || (item.computed.isTranslatable ? [] : '');
            } else {
                return '';
            }
        },
    },
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection
