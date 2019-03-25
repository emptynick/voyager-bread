@extends('voyager::master')
@section('page_title', __('bread::manager.manager'))

@section('content')
<div class="page-content container-fluid" id="manager-edit">
    <language-picker></language-picker>
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary panel-bordered">                
                <div class="panel-heading">
                    <h3 class="panel-title panel-icon"><i class="voyager-info-circled"></i> {{ ucfirst($table) }} {{ __('voyager::bread.bread_info') }}</h3>
                    <div class="panel-actions">
                        <a class="panel-action voyager-angle-up" data-toggle="panel-collapse" aria-hidden="true"></a>
                    </div>
                </div>

                <div class="panel-body" style="overflow: visible">
                    <div class="row clearfix">
                        <div class="col-md-6 form-group">
                            <label for="name">{{ __('voyager::database.table_name') }}</label>
                            <input type="text" class="form-control" readonly :value="table">
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-md-6 form-group">
                            <label for="display_name_singular">{{ __('voyager::bread.display_name_singular') }}</label>
                            <language-input classes="form-control" placeholder="{{ __('voyager::bread.display_name_singular') }}" v-model="bread.name_singular"></language-input>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="display_name_plural">{{ __('voyager::bread.display_name_plural') }}</label>
                            <language-input classes="form-control" placeholder="{{ __('voyager::bread.display_name_plural') }}" v-model="bread.name_plural"></language-input>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-md-6 form-group">
                            <label for="slug">{{ __('voyager::bread.url_slug') }}</label>
                            <language-input classes="form-control" placeholder="{{ __('voyager::bread.url_slug_ph') }}" v-model="bread.slug" :slug="bread.name_plural"></language-input>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="icon">{{ __('voyager::bread.icon_hint') }} <a
                                        href="{{ route('voyager.compass.index', [], false) }}#fonts"
                                        target="_blank">{{ __('voyager::bread.icon_hint2') }}</a></label>
                            <input type="text" class="form-control" placeholder="{{ __('voyager::bread.icon_class') }}" v-model="bread.icon">
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-md-6 form-group">
                            <label for="model_name">{{ __('voyager::bread.model_name') }}</label>
                            <span class="voyager-question"
                                aria-hidden="true"
                                data-toggle="tooltip"
                                data-placement="right"
                                title="{{ __('voyager::bread.model_name_ph') }}"></span>
                            <input type="text" class="form-control" placeholder="{{ __('voyager::bread.model_class') }}" v-model="bread.model_name">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="controller">{{ __('voyager::bread.controller_name') }}</label>
                            <span class="voyager-question"
                                aria-hidden="true"
                                data-toggle="tooltip"
                                data-placement="right"
                                title="{{ __('voyager::bread.controller_name_hint') }}"></span>
                            <input type="text" class="form-control" placeholder="{{ __('voyager::bread.controller_name') }}" v-model="bread.controller">
                        </div>
                    </div>
                </div><!-- .panel-body -->
            </div><!-- .panel -->
            <div class="panel panel-primary panel-bordered">                
                <div class="panel-heading">
                    <h3 class="panel-title panel-icon"><i class="voyager-bread"></i> Layout Builder</h3>
                    <div class="panel-actions">
                        <a class="panel-action voyager-angle-up" data-toggle="panel-collapse" aria-hidden="true"></a>
                    </div>
                </div>

                <div class="panel-body" style="overflow: visible">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="panel panel-body">
                                <label>Add Formfield</label>
                                <v-select :options="filteredFormfields" :on-change="addFormfield" label="type" v-model="selectedFormfield"></v-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-body">
                                <div class="col-md-6">
                                    <label>Layout</label>
                                    <v-select :options="bread.layouts" v-model="currentLayout" label="name"></v-select>
                                </div>
                                <div class="col-md-6" v-if="currentLayout">
                                    <label>Name</label>
                                    <input type="text" v-model="currentLayout.name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-body">
                                <button class="btn btn-success" @click="saveBread()">Save BREAD</button>
                                <button class="btn btn-danger" @click="deleteLayout()">Delete Layout</button>
                                <button class="btn btn-primary" @click="addLayout('view')">Add View Layout</button>
                                <button class="btn btn-primary" @click="addLayout('list')" style="margin-bottom: 5px">Add List Layout</button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div v-if="currentLayout">
                        <div v-if="currentLayout.formfields.length == 0" class="alert alert-info">
                            Create a new element by choosing a formfield above.
                        </div>
                        <div v-if="currentLayout.type == 'list' && listWidth > 100" class="alert alert-warning">
                            <strong>Warning</strong> Your list width is greater than 100%
                        </div>
                        <div class="row fake-table-hd" v-if="currentLayout.type == 'list'">
                            <div class="col-xs-2">Type</div>
                            <div class="col-xs-2">Field</div>
                            <div class="col-xs-2">Title</div>
                            <div class="col-xs-1">Size (in %)</div>
                            <div class="col-xs-1">Searchable</div>
                            <div class="col-xs-1">Orderable</div>
                            <div class="col-xs-1">Default order</div>
                            <div class="col-xs-2">Actions</div>
                        </div>
                        <draggable v-model="currentLayout.formfields" handle=".drag_handle">
                            <formfield-base v-for="(formfield, key) in currentLayout.formfields"
                                            :view="'mockup'"
                                            :type="slugify(formfield.type)"
                                            :layout-type="currentLayout.type"
                                            :layout="currentLayout"
                                            :options="formfield.options"
                                            :validation="formfield.validation"
                                            :key="key"
                                            :columns="columns"
                                            :accessors="accessors"
                                            :relationships="relationships">
                            </formfield-base>
                        </draggable>
                    </div>
                    <div v-else class="alert alert-info">
                        Create a new Layout by clicking "Add View Layout" or "Add List Layout" above.
                    </div>
                </div>
            </div>
            <div class="panel panel-primary panel-bordered">                
                <div class="panel-heading">
                    <h3 class="panel-title panel-icon"><i class="voyager-lock"></i> Roles</h3>
                    <div class="panel-actions">
                        <a class="panel-action voyager-angle-up" data-toggle="panel-collapse" aria-hidden="true"></a>
                    </div>
                </div>

                <div class="panel-body" style="overflow: visible">
                    <div class="row" v-if="currentLayout">
                        <div v-if="currentLayout.type == 'list'" class="col-md-12">
                            <div class="panel panel-body">
                                Browse
                                <select v-model="currentLayout.browse_roles" class="form-control" multiple>
                                    <option v-for="role in roles" :value="role.id">
                                        @{{ role.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div v-else>
                            <div class="col-md-4">
                                <div class="panel panel-body">
                                    Read
                                    <select v-model="currentLayout.read_roles" class="form-control" multiple>
                                        <option v-for="role in roles" :value="role.id">
                                            @{{ role.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-body">
                                    Edit
                                    <select v-model="currentLayout.edit_roles" class="form-control" multiple>
                                        <option v-for="role in roles" :value="role.id">
                                            @{{ role.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-body">
                                    Add
                                    <select v-model="currentLayout.add_roles" class="form-control" multiple>
                                        <option v-for="role in roles" :value="role.id">
                                            @{{ role.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary panel-bordered">                
                <div class="panel-heading">
                    <h3 class="panel-title panel-icon"><i class="voyager-code"></i> Code</h3>
                    <div class="panel-actions">
                        <a class="panel-action panel-collapsed voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                    </div>
                </div>

                <div class="panel-body" style="overflow: visible; display: none">
                    <div class="row">
                        <textarea class="form-control" rows="25" v-bind:value="JSON.stringify(bread, null, 4)"></textarea>
                    </div>
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
    el: "#manager-edit",
    data: {
        table: '{{ $table }}',
        bread: {!! json_encode(Bread::getBread($table) ?? (object)['table' => $table, 'layouts' => []]) !!},
        columns: {!! json_encode(Bread::getTableColumns($table)) !!},
        accessors: {!! json_encode(Bread::getBreadAccessors($table)) !!},
        relationships: {!! json_encode(Bread::getBreadRelationships($table)) !!},
        formfields: {!! json_encode(Bread::formfields()->flatten(1)->toArray()) !!},
        roles: {!! json_encode(Bread::getAllRoles()) !!},
        currentLayout: null,
        selectedFormfield: null,
    },
    methods: {
        deleteFormfield: function (key) {
            // Todo: confirm
            this.currentLayout.formfields.splice(key, 1);
        },
        saveBread: function () {
            this.$http.post("{{ route('voyager.bread.store') }}", {
                '_token': '{{ csrf_token() }}',
                'bread': JSON.stringify(this.bread)
            }).then(function (response) {
                if (response.statusText == 'OK') {
                    toastr.success('BREAD saved as "'+response.body.path+'"');
                } else {
                    toastr.error('Saving BREAD failed: '+response.statusText);
                }
            });
        },
        deleteLayout: function () {
            // Todo: confirm
            var vm = this;
            this.bread.layouts.forEach(function (layout, key) {
                if (vm.currentLayout == layout) {
                    vm.bread.layouts.splice(key, 1);
                }
            });

            if (this.bread.layouts.length == 0) {
                this.currentLayout = null;
            } else {
                this.currentLayout = this.bread.layouts[0];
            }
        },
        addLayout: function (type) {
            var name = type.charAt(0).toUpperCase() + type.slice(1);
            var layout = {
                'name': 'New '+type,
                'type': type,
                'formfields': []
            };
            if (type == 'list') {
                layout.order_by = '';
            }
            this.bread.layouts.push(layout);
            var vm = this;
            Vue.nextTick(function () {
                vm.currentLayout = layout;
            });
        },
        addFormfield: function (formfield) {
            if (!formfield || !this.currentLayout) {
                return;
            }
            formfield = Object.assign({}, formfield);
            if (this.currentLayout.type == 'list') {
                formfield.options = {
                    'width': 50,
                    'searchable': true,
                    'orderable': true
                };
            } else {
                formfield.options = {};
            }
            formfield.validation = [];
            this.currentLayout.formfields.push(formfield);
            this.selectedFormfield = null;
        },
    },
    computed: {
        listWidth: function () {
            var width = 0;
            this.currentLayout.formfields.forEach(function (formfield) {
                width += parseInt(formfield.options.width);
            });

            return width;
        },
        filteredFormfields: function () {
            var vm = this;
            return this.formfields.filter(function (formfield) {
                if (vm.currentLayout) {
                    if ((vm.currentLayout.type == 'list' && !formfield.lists) || (vm.currentLayout.type == 'view' && !formfield.views)) {
                        return false;
                    }
                }

                return true;
            });
        }
    },
    watch: {
        currentLayout: function () {
            this.$eventHub.$emit('close-options', -1);
        }
    },
    mounted: function () {
        if (this.bread.layouts && this.bread.layouts.length > 0) {
            this.currentLayout = this.bread.layouts[0];
        }

        @localization
    },
});
</script>

@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection