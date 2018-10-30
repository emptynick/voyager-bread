@extends('voyager::master')

@section('page_title', __('bread::manager.edit_views_for_table', ['table' => $table]))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-bread"></i>
    {{ __('bread::manager.edit_views_for_table', ['table' => $table]) }}
</h1>
@stop

@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li>
        <a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a>
    </li>
    <li><a href="{{ route('voyager.bread.index') }}">{{ __('bread::manager.bread_manager') }}</a></li>
    <li><a href="{{ route('voyager.bread.edit', ['table' => $table]) }}">{{ ucfirst($table) }}</a></li>
    <li>{{ __('bread::generic.views') }}</li>
</ol>
@endsection

@section('content')
<div class="page-content container-fluid">
    <div id="view-builder">
        <language-switcher :languages="{{ json_encode(config('voyager.multilingual.locales')) }}"></language-switcher>
        <vue-snotify></vue-snotify>
        <div class="row">
            <div class="col-md-12">
                <div class="dropdown" style="display:inline" v-if="this.views.length > 0">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        {{ __("bread::manager.add_element") }}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">{{ __("bread::generic.formfields") }}</li>
                        @foreach(\Bread\BreadFacade::formfields()->where('group', 'formfield')->sortBy('name') as $formfield)
    					<li>
                            <a href="#" v-on:click="addElement('{{ $formfield->getCodeName() }}', 'formfield')">
                                {{ $formfield->getName() }}
                            </a>
                        </li>
    					@endforeach
                        <li class="dropdown-header">{{ __("bread::generic.layout_elements") }}</li>
                        @foreach(\Bread\BreadFacade::formfields()->where('group', 'layout') as $formfield)
    					<li>
                            <a href="#" v-on:click="addElement('{{ $formfield->getCodeName() }}', 'layout')">
                                {{ $formfield->getName() }}
                            </a>
                        </li>
    					@endforeach
                        @if (count($relationships) > 0)
                            <li class="dropdown-header">{{ __("bread::generic.relationships") }}</li>
                            @foreach($relationships as $relationship)
                            @if ($relationship['has_bread'])
                            <li>
                                <a href="#" v-on:click="addElement('{{ $relationship['type_slug'] }}', '{{ $relationship['name'] }}')">
                                    {{ $relationship['name'] }}
                                </a>
                            </li>
                            @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div class="dropdown" style="display:inline" v-if="this.views.length > 0">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        {{ __("bread::generic.view") }} (@{{ this.currentView.name }})
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li v-for="(view, key) in this.views">
                            <a href="#" v-on:click="currentViewId = key">
                                @{{ view.name }}
                            </a>
                        </li>
                    </ul>
                </div>
                <button @click="newViewPrompt()" class="btn btn-primary">{{ __("bread::manager.view_new") }}</button>
                <button @click="saveViews()" class="btn btn-primary">{{ __("bread::manager.view_save") }}</button>
                <button v-if="this.views.length > 0" @click="deleteView()" class="btn btn-primary">{{ __("bread::manager.view_delete") }}</button>
            </div>
        </div>
        <div class="clearfix"></div>
        <div style="width:100%; height:100%;" ref="editWrapper" id="editWrapper">
            <div v-if="this.views.length == 0">
                <br>
                <div class="panel panel-bordered">
                    <div class="panel-body" style="text-align:center">
                        <h3>{{ __('bread::manager.no_views_message') }}</h3>
                    </div>
                </div>
            </div>
            <div v-if="this.views.length > 0 && this.currentView.elements.length == 0">
                <br>
                <div class="panel panel-bordered">
                    <div class="panel-body" style="text-align:center">
                        <h3>{{ __('bread::manager.no_elements_message') }}</h3>
                    </div>
                </div>
            </div>

            <view-builder
                v-bind:elements.sync="currentElements"
                :fields="this.fields"
                :relationships="this.relationships"
                ref="view_builder"
            />
        </div>
    </div>
</div>
<div class="row clearfix"></div>
@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
@include('bread::components.formfields.formfield')
@foreach(\Bread\BreadFacade::formfields() as $formfield)
@include($formfield->getComponent('view'))
<input type="hidden" id="{{ $formfield->getCodeName() }}_default_options" value="{{ $formfield->getOptions('view') }}">
@endforeach

@include('bread::components.validation-form')
@include('bread::components.language-switcher')
@include('bread::components.language-input')
@include('bread::components.view-builder')
@include('bread::components.relationship-create')
<script>
var builder = new Vue({
    el: "#view-builder",
    data: {
        views: {!! $views->toJson() !!},
        fields: {!! $fields->toJson() !!},
        currentViewId: 0,
        relationships: {!! $relationships->toJson() !!},
    },
    computed: {
        currentView() {
            return this.views[this.currentViewId];
        },
        currentElements: {
            get: function () {
                if (!this.currentView) {
                    return [];
                }
                return this.currentView.elements;
            },
            set: function (value) {
                this.views[this.currentViewId].elements = value;
            }
        },
    },
    methods: {
        addElement: function(type, group) {
            this.$refs.view_builder.addElement(type, group);
        },
        deleteView() {
            this.$snotify.confirm('{{ __("voyager::manager.view_delete_confirm") }}', '{{ __("voyager::manager.view_delete") }}', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.views.splice(this.currentViewId, 1);
                        this.currentViewId = this.views.length - 1;
                        this.$snotify.remove(toast.id);
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        saveViews: function() {
            this.$http.post('{{ route("voyager.bread.storelayouts", ["table" => $table]) }}', {
                views: JSON.stringify(this.views),
                _token: "{{ csrf_token() }}"
            }).then(response => {
                this.$snotify.success('{{ __("voyager::manager.view_saved") }}');
            }, response => {
                this.$snotify.error('{{ __("voyager::manager.view_saving_failed") }}: ' + response.body);
            });
        },
        newViewPrompt: function() {
            this.$snotify.prompt('', '{{ __("bread::manager.add_view") }}', {
                buttons: [
                    { text: '{{ __("voyager::generic.save") }}', action: (toast) => {
                        let viewExists = false;
                        this.views.forEach(function(view) {
                            if (view.name == toast.value)
                                viewExists = true;
                        });
                        if (toast.value != '' && !viewExists) {
                            let view = {
                                name: toast.value,
                                type: "view",
                                elements: []
                            };
                            this.views.push(view);
                            this.currentViewId = this.views.length - 1;
                            toast.valid = true;
                            this.$snotify.remove(toast.id);
                        } else {
                            toast.valid = false;
                        }
                    }},
                    { text: '{{ __("voyager::generic.cancel") }}', action: (toast) => {
                        toast.valid = true;
                        this.$snotify.remove(toast.id);
                    }},
                ],
                placeholder: '{{ __("bread::generic.name") }}'
            });
        },
    },
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
<style>
.formfield-panel {
    overflow-y: visible !important;
    overflow-x: visible !important;
}
</style>
@endsection
