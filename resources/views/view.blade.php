@extends('voyager::master')

@section('page_title', __('bread::manager.edit_views_for', ['bread' => $bread->display_name_plural]))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-lightbulb"></i>
    {{ __('bread::manager.edit_views_for', ['bread' => $bread->display_name_plural]) }}
</h1>
@stop
@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    @if(count(Request::segments()) == 1)
    <li class="active"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</li>
    @else
    <li class="active">
        <a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a>
    </li>
    @endif
    <li class="active"><a href="{{ route('voyager.bread.index') }}">{{ __('bread::manager.bread_manager') }}</a></li>
    <li class="active"><a href="{{ route('voyager.bread.edit', $bread->name) }}">{{ $bread->display_name_plural }}</a></li>
    <li class="active"><a href="{{ route('voyager.bread.views.edit', $bread->name) }}">{{ __('bread::manager.views') }}</a></li>
</ol>
@endsection
@section('content')
<div class="page-content container-fluid">
    <div id="view-builder">
        <div class="alerts" v-if="{{ $editing }} && this.breakpointWarning">
            <div class="alert alert-warning">
                <strong>Warning</strong>
                <p>
                    Your current browser resolution is smaller than the highest breakpoint.<br>
                    This might be a problem when editing bigger Views!
                </p>
            </div>
        </div>
        <vue-snotify></vue-snotify>
        <div class="col-md-12">
            <div class="dropdown" style="display:inline" v-if="this.layouts.length > 0">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    Add Element
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-header">Formfields</li>
                    @foreach(\Bread\BreadFacade::formfields()->where('element_type', 'formfield') as $formfield)
					<li>
                        <a href="#" v-on:click="addElement('formfield', '{{ $formfield->getCodeName() }}')">
                            {{ $formfield->getName() }}
                        </a>
                    </li>
					@endforeach
                    <li class="dropdown-header">Layout Elements</li>
                    @foreach(\Bread\BreadFacade::formfields()->where('element_type', 'layout_element') as $formfield)
					<li>
                        <a href="#" v-on:click="addElement('layout_element', '{{ $formfield->getCodeName() }}')">
                            {{ $formfield->getName() }}
                        </a>
                    </li>
					@endforeach
                    <li class="dropdown-header">Relationships</li>
                    @foreach($relationships as $relationship)
                    <li>
                        <a href="#" v-on:click="addElement('relationship', '{{ $relationship }}')">
                            {{ $relationship }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="dropdown" style="display:inline" v-if="this.layouts.length > 0">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    View (@{{ this.currentLayoutsName }})
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li v-for="(view, key) in this.layouts" v-if="view.type == 'view'">
                        <a href="#" v-on:click="changeLayout(view.name)">
                            <strong v-if="view.name == currentLayoutsName">
                                @{{ view.name }}
                            </strong>
                            <span v-else>
                                @{{ view.name }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="dropdown" style="display:inline" v-if="this.breakpoints_all.length > 1 && this.layouts.length > 0">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    Breakpoint (@{{ this.breakpoint }})
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li v-for="(bp, key) in this.breakpoints_all">
                        <a href="#" v-on:click="setBreakpoint(key)">
                            <strong v-if="key == breakpoint">
                                @{{ bp.name }} (@{{ key }})
                            </strong>
                            <span v-else>
                                @{{ bp.name }} (@{{ key }})
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            <button @click="gridMode()" class="btn btn-primary" v-if="this.layouts.length > 0">Grid</button>
            <button @click="listMode()" class="btn btn-primary" v-if="this.layouts.length > 0">List</button>
            <button @click="createNewViewPrompt()" class="btn btn-primary">New View</button>
            <button @click="saveLayouts()" class="btn btn-primary">Save Views</button>
        </div>
        <div class="clearfix"></div>
        <div id="wrapper" style="align-items: stretch;width: 100%; height: 100%;">
            <div style="position:relative;width:100%;height:100%;">
                <div v-if="this.layouts.length == 0">
                    <div class="panel panel-bordered">
                        <div class="panel-body" style="text-align:center">
                            <h3>Add a new View by clicking "New View"</h3>
                        </div>
                    </div>
                </div>
                <vue-responsive-grid-layout
                v-if="this.layouts.length > 0"
                @layout-update="updateLayout"
                @layout-init="initLayout"
                @width-init="initWidth"
                @width-change="changeWidth"
                @breakpoint-change="changeBreakpoint"
                :layouts="currentLayouts"
                :cols="cols"
                :compact-type="'vertical'"
                :vertical-compact="true"
                :init-on-start="true"
                :breakpoint="breakpoint"
                :breakpoints="breakpoints"
                :cols-all="colsAll"
                ref="layout"
                >
                <template slot-scope="props">
                    <vue-grid-item
                    v-for="element in props.layout"
                    v-if="element.i && props"
                    :key="element.i"
                    :x="element.x"
                    :y="element.y"
                    :w="element.w"
                    :h="element.h"
                    :i="element.i"
                    :cols="props.cols"
                    :container-width="props.containerWidth"
                    :component="'element-wrapper'"
                    :component-props="{ element: getLayoutElement(element.i) }"
                    :default-size="2"
                    :is-draggable="isDraggable"
                    :is-resizable="isResizable"
                    :height-from-children="true" {{-- Only false for relationships --}}
                    :can-be-resized-with-all="true"
                    >
                </vue-grid-item>
                    <div v-if="props.layout.length == 0">
                        <div class="panel panel-bordered">
                            <div class="panel-body" style="text-align:center">
                                <h3>Add new Formfields by choosing one from the "Add Element" dropdown</h3>
                            </div>
                        </div>
                    </div>
                </template>
                </vue-responsive-grid-layout>
            </div>
        </div>
    </div>
</div>
<div class="row clearfix"></div>
@endsection
@section('javascript')
<script src="{{ asset('vendor/bread/js/translatable.js') }}"></script>
<script src="{{ asset('vendor/bread/js/app.js') }}"></script>

@include('bread::vue.element-wrapper')

@foreach(\Bread\BreadFacade::formfields() as $formfield)
@include($formfield->getComponent())
<input type="hidden" id="{{ $formfield->getCodeName() }}_default_options" value="{{ $formfield->getOptions() }}">
@endforeach

<script>
Vue.prototype.$type = 'view';

var builder = new Vue({
    el: "#view-builder",
    data: {
        editing: {{ $editing }},
        layouts: {!! json_encode($views, JSON_PRETTY_PRINT) !!},
        currentLayoutsName: "{{ @$views->first()->name }}",
        breakpoint: "{{ $highest_bp }}",/*Always the highest...*/
        breakpointWarning: false,
        cols: 10,
        breakpoints: {!! json_encode($bp_widths, JSON_PRETTY_PRINT) !!},
        breakpoints_all: {!! json_encode($breakpoints, JSON_PRETTY_PRINT) !!},
        colsAll: {!! json_encode($bp_cols, JSON_PRETTY_PRINT) !!},
        isDraggable: true,
        isResizable: true,
        currentOptions: null,
		currentOptionsEl: null,
        fields: {!! json_encode($fields) !!},
    },
    computed: {
        currentLayouts() {
            return this.layouts[this.currentLayoutsId];
        },
        currentLayout() {
            return this.layouts[this.currentLayoutsId][this.breakpoint];
        },
        currentLayoutsId() {
            var name = this.currentLayoutsName;
            var id;
            this.layouts.forEach(function(element, i) {
                if(element.name === name)
                    id = i;
            });
            return id;
        }
    },
    methods: {
        getLayoutElement(i) {
            return this.currentLayout.find(obj => {
                return (obj.i === i)
            });
        },
        initLayout({layout, cols}) {
            this.cols = cols;
        },
        initWidth({width}) {
            this.containerWidth = width;
            this.$refs.layout.initLayout();
        },
        changeWidth({width, newCols}) {
            this.containerWidth = width;
            this.cols = newCols;
            this.$nextTick( ()=> {
                this.$refs.layout.updateItemsHeight();
            });
        },
        updateLayout({layout, breakpoint}) {
            var vm = this;
            layout.forEach(function(element, i) {
                vm.currentLayout[i].x = element.x;
                vm.currentLayout[i].y = element.y;
                vm.currentLayout[i].w = element.w;
                vm.currentLayout[i].h = element.h;
            });
        },
        changeBreakpoint({breakpoint, cols}) {
            if (!this.editing) {
                this.cols = cols;
                this.breakpoint = breakpoint;
            } else {
                if (breakpoint != "{{ $highest_bp }}")
                    this.breakpointWarning = true;
                else
                    this.breakpointWarning = false;
            }
        },
        setBreakpoint(breakpoint)
        {
            this.cols = this.colsAll[breakpoint];
            this.breakpoint = breakpoint;
        },
        changeLayout(name) {
            this.currentLayoutsName = name;
            this.$refs.layout.switchLayout(this.currentLayouts);
        },
        gridMode() {
            this.$snotify.warning('This will rearrange all elements. Are you sure you want to continue?', 'Warning', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: 'Yes', action: (toast) => {
                        this.$refs.layout.resizeAllItems(false, false);
                        this.$snotify.remove(toast.id);
                    }},
					{text: 'No', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        listMode() {
            this.$snotify.warning('This will rearrange all elements. Are you sure you want to continue?', 'Warning', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: 'Yes', action: (toast) => {
                        this.$refs.layout.resizeAllItems(true, false);
                        this.$snotify.remove(toast.id);
                    }},
					{text: 'No', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        deleteElement(id) {
            this.$snotify.confirm('Are you sure you want to delete this element?', 'Delete Element?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: 'Yes', action: (toast) => {
                        this.$refs.layout.currentLayout = this.$refs.layout.currentLayout.filter(obj => {
                            return (obj.i !== id);
                        });
                        this.layouts[this.currentLayoutsId][this.breakpoint] = this.currentLayout.filter(obj => {
                            return (obj.i !== id);
                        });
                        this.$snotify.remove(toast.id);
                        this.updateItemsHeight();
                        //Todo: recalc all ids
                    }, bold: false},
					{text: 'No', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        addElement: function(type, codename) {
            let options = JSON.parse($('#'+codename+'_default_options').val());
            let newitem = {
                x: 0,
                y: 999999,
                w: 6,
                h: 10,
                i: ""+this.$refs.layout.currentLayout.length,
                options: options,
                type: codename,
                element: type,
                field: null,
                class: 'panel-primary',
            };
            this.$refs.layout.currentLayout.push(newitem);
            this.layouts[this.currentLayoutsId][this.breakpoint].push(newitem);
            this.updateItemsHeight();
        },
        isOptionsOpen: function(i) {
			return (this.currentOptions === i);
		},
		openOptions: function(i) {
			this.currentOptions = (this.isOptionsOpen(i) ? null : i);
			this.currentOptionsEl = document.getElementById(this.currentOptions+'_options');
		},
        saveLayouts: function() {
            this.$http.post('{{ route('voyager.bread.views.store', ['table' => $table]) }}', {
                views: JSON.stringify(this.layouts),
                _token: "{{ csrf_token() }}"
            }).then(response => {
                this.$snotify.success('Views were successfully saved.');
            }, response => {
                this.$snotify.error('Saving views failed: ' + response.body);
            });
        },
        updateItemsHeight: function() {
            this.$nextTick( ()=> {
                this.$refs.layout.updateItemsHeight();
            });
        },
        createNewViewPrompt: function() {
            this.$snotify.html(`<div class="snotifyToast__title">New View</div>
  <div class="snotifyToast__body">Name:<input id="view-name" type="text" class="form-control"></div> `, {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                type: 'confirm',
                buttons: [
                    {text: 'Save', action: (toast) => {
                        var value = document.getElementById('view-name').value;
                        if (value != "") {
                            this.createNewView(value);
                            this.$snotify.remove(toast.id);
                        }
                    }},
                    {text: 'Cancel', action: (toast) => this.$snotify.remove(toast.id)},
                ]
            });
        },
        createNewView: function(name) {
            let layout = {
                name: name,
                type: "view",
            };
            for (var key in this.breakpoints) {
                layout[key] = [];
            }
            let layoutExists = false;
            this.layouts.forEach(function(layout) {
                if (layout.name == name)
                    layoutExists = true;
            });

            if (layoutExists) {
                this.$snotify.error('This name already exists. Please choose another.', 'Error', {
                    timeout: 5000,
                    showProgressBar: false,
                    closeOnClick: false,
                    pauseOnHover: true,
                    buttons: [
                        {text: 'Ok', action: (toast) => {
                            this.createNewViewPrompt();
                            this.$snotify.remove(toast.id);
                        }
                        },
                        {text: 'Cancel', action: (toast) => this.$snotify.remove(toast.id)},
                    ]
                });
                return;
            }
            this.layouts.push(layout);
            this.changeLayout(name);
        },
        getTranslation: function(key, item) {
            if (key && item) {
                this.$http.post('{{ route('voyager.bread.translation') }}', {
                    key: key,
                    _token: "{{ csrf_token() }}"
                }).then(response => {
                    if (key != response.body)
                        this.$bus.$emit('translationReceived', key, response.body, item);
                }, response => {
                    // Do nothing
                });
            }
        },
    },
    mounted: function() {
        this.$bus.$on('requestTranslation', (key, item) => this.getTranslation(key, item));
        this.$bus.$on('updateItemsHeight', () => this.updateItemsHeight());

        var vm = this;
        window.addEventListener('keyup', function(event) {
            if (event.keyCode == 27) {
                vm.openOptions(null);
            }
            if (vm.layouts.length > 0)
                vm.updateItemsHeight();
        });
        window.addEventListener('click', function(event) {
            if (!event.target.className.includes('open-settings') && vm.currentOptionsEl !== null && event.path.indexOf(vm.currentOptionsEl) == -1) {
                vm.openOptions(null);
            }
        });
        window.addEventListener('change', vm.updateItemsHeight);
        $('.hamburger').on('click', function() {
            //Todo: Force resize when menu is toggled
        });
    }
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('vendor/bread/css/app.css') }}">
<style>
.options-tooltip {
    z-index: 10000 !important;
    width: 150rem;
    background-color: #353d47 !important;
}
.resizable-handle {
    position: absolute;
    width: 20px;
    height: 20px;
    bottom: 0;
    right: 0px;
    text-align: right;
}

.resizable-handle::after {
    content: "";
    position: absolute;
    right: 3px;
    bottom: 3px;
    width: 5px;
    height: 5px;
    border-right: 2px solid #FFFFFF;
    border-bottom: 2px solid #FFFFFF;
}

.floating-actions {
    position: absolute;
    top: 25px;
    right: 5px;
    text-align: right;
}

.floating-actions .panel-action {
    color: #a3afb7;
}

.panel-100-height {
    height: 100%;
}

.vue-responsive-grid-layout {
    display:block;
    position:relative;
}

.dropdown .dropdown-header {
    font-weight: bold;
    padding-left: 10px;
}

.dropdown strong {
    font-weight: bold;
}

.vue-swatches--inline {
    background-color: transparent !important;
}

.panel-body .vue-swatches--inline {
    text-align: center;
}
</style>
@endsection
