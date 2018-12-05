@extends('voyager::master')
@section('page_title', $table ? 'Add BREAD' : 'Edit BREAD')

@section('content')
<div id="bread-manager-edit-add">
    <h1 class="page-title">
        <i class="voyager-bread"></i>
        <span v-if="table">Add BREAD for table @{{ table }}</span>
        <span v-else>Edit BREAD for table @{{ bread.table }}</span>
    </h1>
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <vue-snotify></vue-snotify>
        <language-switcher></language-switcher>
        <!-- Settings -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel-group">
                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#bread-settings">BREAD settings</a>
                            </h4>
                        </div>
                        <div id="bread-settings" :class="'panel-collapse collapse'+(table ? 'in' : '')">
                            <div class="panel-body">
                                <div class="row clearfix">
                                    <div class="col-md-4 form-group">
                                        <label>Display Name singular</label>
                                        <language-input v-model="bread.display_name_singular" />
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Display Name Plural</label>
                                        <language-input v-model="bread.display_name_plural" :name="'display_name_plural'" />
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Slug</label>
                                        <language-input v-model="bread.slug" :slug="'display_name_plural'" />
                                    </div>
                                </div>
                                <div class="row clearfix">
                                    <div class="col-md-3 form-group">
                                        <label>Model Name</label>
                                        <input type="text" class="form-control" v-model="bread.model_name">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Controller Name</label>
                                        <input type="text" class="form-control" v-model="bread.controller_name">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Policy Name</label>
                                        <input type="text" class="form-control" v-model="bread.policy_name">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Icon</label>
                                        <input type="text" class="form-control" v-model="bread.icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Settings -->

        <!-- View and List builder -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="overflow: visible">
                        <!-- Add Layout -->
                        <div class="dropdown" style="display:inline">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                Layouts <span v-if="currentLayout">(@{{ currentLayout.name }})</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" v-on:click.prevent="addLayout('view')">Add View</a>
                                    <a href="#" v-on:click.prevent="addLayout('list')">Add List</a>
                                </li>
                                <li class="divider" v-if="bread.layouts.length > 0"></li>
                                <li v-for="(layout, key) in bread.layouts">
                                    <a href="#" v-on:click.prevent="current_layout = key">
                                        @{{ layout.name }} (@{{ layout.type.charAt(0).toUpperCase()+layout.type.slice(1) }})
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Add Element -->
                        <div class="dropdown" style="display:inline" v-if="currentLayout">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                Add Element
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <!-- Formfields -->
                                <li class="dropdown-header">Formfield</li>
                                <li v-for="formfield in formfields" v-if="true"> <!-- Type == view && group == layout -->
                                    <a href="#" v-on:click.prevent="addElement('formfield', formfield.codename, formfield.name)">
                                        @{{ formfield.name }}
                                    </a>
                                </li>
                                <!-- Relationship -->
                                <li class="dropdown-header" v-if="currentLayout && currentLayout.type == 'view'">
                                    Relationships
                                </li>
                                <li v-for="relationship in relationships" v-if="currentLayout && currentLayout.type == 'view' && relationship.lists.length > 0">
                                    <a href="#" v-on:click.prevent="addElement('relationship', relationship)">
                                        @{{ relationship.name }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        {{--
                        <!-- Breakpoint -->
                        <div class="dropdown" style="display:inline">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                Breakpoint (@{{ current_breakpoint.toUpperCase() }})
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li v-for="(width, breakpoint) in breakpoints">
                                    <a href="#" v-on:click.prevent="current_breakpoint = breakpoint">@{{ breakpoint.toUpperCase() }} (@{{ width }}px)</a>
                                </li>
                            </ul>
                        </div>
                        --}}
                        <button v-if="currentLayout" v-on:click.prevent="deleteLayout()" class="btn btn-primary">Delete Layout</button>
                        <button class="btn btn-primary" v-on:click.prevent="saveBread()">Save BREAD</button>
                        <component
                            v-if="currentLayout"
                            :is="currentLayout.type+'-builder'"
                            :layout="currentLayout"
                            :parent="getThis()"
                            :fields="fields"
                            :accessors="accessors"
                            :relationships="relationships">
                        </component>
                        <div class="clearfix"></div>
                        <!-- View Options -->
                        <div v-if="currentLayout && currentLayout.type == 'view'">
                            <!-- Read roles -->
                            <div class="col-md-4">
                                <div class="panel panel-bordered" v-if="this.views.length > 0">
                                   <div class="panel-heading">
                                       <h3 class="panel-title">
                                           <i class="voyager-people"></i> Read roles
                                           <span class="panel-desc"> The roles which use this view for reading</span>
                                       </h3>
                                   </div>
                                   <div class="panel-body" style="overflow: visible">
                                       <select v-model="currentLayout.read_roles" multiple="true" class="form-control">
                                           <option v-for="(id, role) in roles" :value="id">
                                               @{{ role }}
                                           </option>
                                       </select>
                                   </div>
                                </div>
                            </div>
                            <!-- Edit roles -->
                            <div class="col-md-4">
                                <div class="panel panel-bordered" v-if="this.views.length > 0">
                                   <div class="panel-heading">
                                       <h3 class="panel-title">
                                           <i class="voyager-people"></i> Edit roles
                                           <span class="panel-desc"> The roles which use this view for editing</span>
                                       </h3>
                                   </div>
                                   <div class="panel-body" style="overflow: visible">
                                       <select v-model="currentLayout.edit_roles" multiple="true" class="form-control">
                                           <option v-for="(id, role) in roles" :value="id">
                                               @{{ role }}
                                           </option>
                                       </select>
                                   </div>
                                </div>
                            </div>
                            <!-- Add roles -->
                            <div class="col-md-4">
                                <div class="panel panel-bordered" v-if="this.views.length > 0">
                                   <div class="panel-heading">
                                       <h3 class="panel-title">
                                           <i class="voyager-people"></i> Add roles
                                           <span class="panel-desc"> The roles which use this view for adding</span>
                                       </h3>
                                   </div>
                                   <div class="panel-body" style="overflow: visible">
                                       <select v-model="currentLayout.add_roles" multiple="true" class="form-control">
                                           <option v-for="(id, role) in roles" :value="id">
                                               @{{ role }}
                                           </option>
                                       </select>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <!-- List Options -->
                        <div v-if="currentLayout && currentLayout.type == 'list'">
                            <div class="col-md-4">
                                <div class="panel panel-bordered" v-if="this.lists.length > 0">
                                   <div class="panel-heading">
                                       <h3 class="panel-title">
                                           <i class="voyager-people"></i> Roles
                                           <span class="panel-desc"> The roles which use this list for browsing</span>
                                       </h3>
                                   </div>
                                   <div class="panel-body" style="overflow: visible">
                                       <select v-model="currentLayout.browse_roles" multiple="true" class="form-control">
                                           <option v-for="(id, role) in roles" :value="id">
                                               @{{ role }}
                                           </option>
                                       </select>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END View and List builder -->
    </div>
</div>
@endsection
@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
@include('bread::components.language-switcher')
@include('bread::components.language-input')
@include('bread::components.language-output')
@include('bread::components.validation-form')
@include('bread::components.view-builder')
@include('bread::components.list-builder')
@include('bread::components.base-formfield')
@include('bread::components.base-layout-element')
@include('bread::components.base-relationship')

@foreach(\Bread\BreadFacade::formfields() as $formfield)
    @include($formfield->getComponent())
    <input type="hidden" id="{{ $formfield->codename }}_default_options" value="{{ json_encode($formfield->options) }}">
@endforeach

<script>
var builder = new Vue({
    el: "#bread-manager-edit-add",
    data: {
        bread: {!! $bread !!},
        table: {!! $table ? "'".$table."'" : 'null' !!},
        fields: {!! $fields ?? '[]' !!},
        accessors: {!! $accessors ?? '[]' !!},
        relationships: {!! $relationships ?? '[]' !!},
        breakpoints: {!! $breakpoints !!},
        formfields: {!! \Bread\BreadFacade::formfields() !!},
        roles: {!! \TCG\Voyager\Models\Role::pluck('id', 'display_name') !!},
        store_url: '{{ route("voyager.bread.store", "#") }}',
        update_url: '{{ route("voyager.bread.update", "#") }}',

        current_layout: null,
        current_options: null,
        current_breakpoint: '{!! $breakpoints->keys()->first() !!}',
    },
    computed: {
        lists: function() {
            var lists = [];
            if (this.bread.layouts) {
                for (i in this.bread.layouts) {
                    if (this.bread.layouts[i].type == 'list') {
                        lists.push(this.bread.layouts[i]);
                    }
                }
            }
            return lists;
        },
        views: function() {
            var views = [];
            if (this.bread.layouts) {
                for (i in this.bread.layouts) {
                    if (this.bread.layouts[i].type == 'view') {
                        views.push(this.bread.layouts[i]);
                    }
                }
            }
            return views;
        },
        currentLayout: function() {
            if (!this.bread.layouts) {
                return null;
            }

            return this.bread.layouts[this.current_layout];
        },
    },
    methods: {
        saveBread: function() {
            var error = '';
            if (!this.bread.display_name_singular || this.bread.display_name_singular == '') {
                error = 'Please enter a singular display name!';
            } else if (!this.bread.display_name_plural || this.bread.display_name_plural == '') {
                error = 'Please enter a plural display name!';
            } else if (!this.bread.slug || this.bread.slug == '') {
                error = 'Please enter a slug!';
            } else if (!this.bread.model_name || this.bread.model_name == '') {
                error = 'Please enter a model name!';
            } else if (!this.bread.model_name.includes('\\')) {
                error = 'Your model name does not contain the namespace!';
            }

            if (error != '') {
                this.$snotify.error(error);
                return;
            }
            var url = this.getUrl(this.store_url, this.bread.table);
            this.$http.post(url, {
                _token: '{{ csrf_token() }}',
                bread: JSON.stringify(this.bread),
            }).then(response => {
                this.$snotify.success('The BREAD was saved.');
            }, response => {
                this.$snotify.error('There was a problem saving this BREAD: ' + response.body.message);
            });
        },
        addLayout: function(type) {
            this.$snotify.prompt('', 'Add Layout', {
                buttons: [
                    { text: '{{ __("voyager::generic.save") }}', action: (toast) => {
                        var layoutExists = false;
                        this.bread.layouts.forEach(function(layout) {
                            if (layout.name == toast.value)
                                layoutExists = true;
                        });
                        if (toast.value != '' && !layoutExists) {
                            this.bread.layouts.push({
                                'name': toast.value,
                                'type': type,
                                'elements': [],
                                'browse_roles': [],
                                'read_roles': [],
                                'edit_roles': [],
                                'add_roles': [],
                            });
                            this.current_layout = this.bread.layouts.length - 1;
                            toast.valid = true;
                            this.$snotify.remove(toast.id);
                        } else {
                            toast.valid = false;
                        }
                    }},
                    { text: 'Cancel', action: (toast) => {
                        toast.valid = true;
                        this.$snotify.remove(toast.id);
                    }},
                ],
                placeholder: 'Name'
            });
        },
        deleteLayout: function() {
            this.$snotify.confirm('Do you really want to delete this Layout?', 'Delete Layout', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.bread.layouts.splice(this.current_layout, 1);
                        this.current_layout = this.bread.layouts.length - 1;
                        this.$snotify.remove(toast.id);
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        addElement: function(group, codename, name) {

            var options = document.getElementById(codename+'_default_options').value;
            this.currentLayout.elements.push({
                group: group,
                codename: codename,
                name: name,
                width: 12,
                field: '',
                is_translatable: false,
                validation: [],
                options: JSON.parse(options),
            });
        },
        getThis: function() {
            return this;
        }
    },
    mounted: function() {
        if (this.bread && this.bread.layouts.length > 0) {
            this.current_layout = 0;
        }
        this.$bus.$emit('setLocale', '{{ $locale }}');
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
