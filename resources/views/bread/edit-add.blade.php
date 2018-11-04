@extends('voyager::master')
@section('page_title', __('voyager::generic.'.($content ? 'edit' : 'add')).' '.get_translated_value($bread->display_name_singular))

@section('content')
<div id="bread-edit">
    <vue-snotify></vue-snotify>
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $bread->icon }}"></i>
            {{ __('voyager::generic.'.($content ? 'edit' : 'add')).' '.get_translated_value($bread->display_name_singular) }}
        </h1>

        <language-switcher :languages="{{ json_encode(config('voyager.multilingual.locales')) }}"></language-switcher>

    </div>
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form action="@if($content){{ route('voyager.'.get_translated_value($bread->slug).'.update', $content['primary_key']) }}@else{{ route('voyager.'.get_translated_value($bread->slug).'.store') }}@endif" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @if($content)
                        {{ method_field("PUT") }}
                    @endif
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            @include('voyager::alerts')

                            <div v-for="(item, key) in elements" :class="'col-md-'+item.width">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div :class="'form-group '+(hasError(item.field) ? 'has-error' : '')">
                                            <component
                                                :is="'formfield-'+item.codename"
                                                :options="item.options"
                                                :computed="item.computed"
                                                :name="item.field"
                                                :show="'{{ $content ? 'edit' : 'add' }}'"
                                                :input="getContent(item)"
                                                :errors="getErrors(item.field)"
                                                :is-translatable="item.computed.isTranslatable"
                                            ></component>

                                            <span class="help-block" style="color:#f96868" v-if="hasError(item.field)">
                                                <ul>
                                                    <li v-for="msg in getErrors(item.field)">
                                                        @{{ msg }}
                                                    </li>
                                                </ul>
                                            </span>
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
    el: "#bread-edit",
    data: {
        elements: {!! $layout->elements->toJson() !!},
        content: {!! collect($content)->merge(old())->toJson() ?? 'null' !!},
        errors: {!! $errors->toJson() !!},
    },
    methods: {
        getContent: function(item) {
            if (this.content) {
                return this.content[item.field] || (item.computed.isTranslatable ? [] : '');
            } else {
                return '';
            }
        },
        hasError: function(field) {
            return (this.getErrors(field).length > 0);
        },
        getErrors: function(field) {
            let errors = this.errors[field];
            if (!errors) {
                errors = [];
                for (var key in this.errors) {
                    if (key.startsWith(field+'.')) {
                        errors.push(this.errors[key][0]);
                    }
                }
                if (errors.length == 0) {
                    return [];
                }
            }
            return errors;
        },
    },
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
<style>
.panel-footer {
    z-index: 1 !important;
}
</style>
@endsection
