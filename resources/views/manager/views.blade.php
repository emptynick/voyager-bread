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
                            <a href="#" v-on:click="addElement('{{ $formfield->getCodeName() }}')">
                                {{ $formfield->getName() }}
                            </a>
                        </li>
    					@endforeach
                        <li class="dropdown-header">{{ __("bread::generic.layout_elements") }}</li>
                        @foreach(\Bread\BreadFacade::formfields()->where('group', 'layout') as $formfield)
    					<li>
                            <a href="#" v-on:click="addElement('{{ $formfield->getCodeName() }}')">
                                {{ $formfield->getName() }}
                            </a>
                        </li>
    					@endforeach
                        @if (count($relationships) > 0)
                            <li class="dropdown-header">{{ __("bread::generic.relationships") }}</li>
                            @foreach($relationships as $relationship)
                            <li>
                                <a href="#" v-on:click="addElement('{{ $relationship['type_slug'] }}', '{{ $relationship['name'] }}')">
                                    {{ $relationship['name'] }}
                                </a>
                            </li>
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
            <draggable v-model="currentView.elements" v-if="this.views.length > 0 && this.currentView.elements.length > 0" :options="{ handle: '.voyager-handle' }">
                <div v-for="(item, id) in this.currentView.elements" :key="id" :class="'col-md-'+item.width">
                    <div :class="'panel panel-bordered '+item.class"
                    style="height:100%; margin-bottom:0 !important;"
                    v-tooltip.notrigger="{ html: id+'_options', visible: isOptionsOpen(id), class: 'options-tooltip', placement: 'bottom' }">
                        <div class="panel-heading">
                            <h3 class="panel-title"></h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-trash" @click="deleteElement(id)"></a>
                                <a class="panel-action voyager-settings open-settings" @click="openOptions(id)"></a>
                                <a @mousedown="startDrag(id)" @mouseup="endDrag()" class="panel-action voyager-code drag_handle"></a>
                                <a class="panel-action voyager-handle"></a>
                            </div>
                        </div>
                        <div class="panel-body formfield-panel">
                            <component :is="componentType(item)" v-bind="item" :show="'mockup'" :type="'view'" :translatable="'{{ $model->isTranslatable ?: false }}'">

                            </component>
                            <div :id="id+'_options'">
                                <div class="pull-left">
                                    <h4>{{ __("bread::generic.options") }}</h4>
                                </div>
                                <div class="pull-right" @click="openOptions(null)">
                                    <span class="voyager-x" style="cursor:pointer;"></span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group" v-if="componentType(item) != 'formfield-relationship'">
                                    <label>{{ __("bread::manager.field") }}</label>
                                    <select class="form-control" v-model="item.field">
                                        <option v-for="field in fields">
                                            @{{ field }}
                                        </option>
                                    </select>
                                </div>
                                <component :is="componentType(item)" v-bind="item" :show="'options'" :type="'view'" :fields="fields" :lists="getLists(item)" :views="getViews(item)" :translatable="'{{ $model->isTranslatable ?: false }}'"></component>
                                <validation-form v-bind="item" />
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </draggable>
        </div>
    </div>
</div>
<div class="row clearfix"></div>
@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
@foreach(\Bread\BreadFacade::formfields() as $formfield)
@include($formfield->getComponent('view'))
<input type="hidden" id="{{ $formfield->getCodeName() }}_default_options" value="{{ $formfield->getOptions('view') }}">
@endforeach

@include('bread::components.validation-form')
<script>
var builder = new Vue({
    el: "#view-builder",
    data: {
        views: {!! $views->toJson() !!},
        fields: {!! $fields->toJson() !!},
        currentViewId: 0,
        currentOptionsId: -1,
        currentDragId: -1,
        cols: 12,
        relationships: {!! $relationships->toJson() !!},
    },
    computed: {
        currentView() {
            return this.views[this.currentViewId];
        },
        currentElements() {
            return this.currentView.elements;
        },
    },
    methods: {
        deleteElement(id) {
            this.$snotify.confirm('{{ __("voyager::manager.delete_element_confirm") }}', '{{ __("voyager::manager.delete_element") }}', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.currentView.elements.splice(id, 1);
                        this.$snotify.remove(toast.id);
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        addElement: function(type, rl_name = '') {
            let options = [];
            let def_opt = document.getElementById(type+'_default_options');
            if (!def_opt) {
                this.$snotify.error('The formfield "'+type+'" is not supported.', 'Error');
                return;
            }
            options = JSON.parse(def_opt.value);
            let newitem = {
                width: 12,
                options: options,
                type: type,
                field: "",
                validation_rules: []
            };
            if (rl_name != '') {
                newitem.options.relationship = rl_name;
            }
            this.currentView.elements.push(newitem);
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
        isOptionsOpen: function(id) {
            return this.currentOptionsId == id;
        },
        openOptions: function(id) {
            if (this.isOptionsOpen(id)) {
                this.currentOptionsId = -1;
            } else {
                this.currentOptionsId = id;
            }
        },
        startDrag: function(id) {
            this.currentDragId = id;
        },
        endDrag: function() {
            this.currentDragId = -1;
        },
        drag: function(e) {
            if (this.currentDragId > -1) {
                e.preventDefault();
                var maxWidth = this.$refs.editWrapper.clientWidth;
                var relative = e.clientX - this.findPos(this.$refs.editWrapper).left;
                var threshold = maxWidth / this.cols;
                var size = Math.min(Math.max(Math.round(relative / threshold), 2), this.cols);

                this.currentView.elements[this.currentDragId].width = size;
            }
        },
        findPos: function(obj) {
            var curleft = curtop = 0;
            if (obj.offsetParent) {
                do {
                    curleft += obj.offsetLeft;
                    curtop += obj.offsetTop;
                } while (obj = obj.offsetParent);
            }
            return {
                left : curleft,
                top : curtop
            };
        },
        componentType: function(item) {
            return 'formfield-'+item.type;
        },
        getLists: function(item) {
            if (item.type == 'relationship') {
                for (var r in this.relationships) {
                    if (this.relationships[r].name == item.options.relationship) {
                        return this.relationships[r].lists;
                    }
                }
            }
            return [];
        },
        getViews: function(item) {
            if (item.type == 'relationship') {
                for (var r in this.relationships) {
                    if (this.relationships[r].name == item.options.relationship) {
                        return this.relationships[r].views;
                    }
                }
            }
            return [];
        },
    },
    mounted: function() {
        window.addEventListener('mouseup', this.endDrag);
        window.addEventListener('mousemove', this.drag);
    }
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
