@extends('voyager::master')

@section('page_title', __('bread::manager.edit_lists_for_table', ['table' => $table]))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-bread"></i>
    {{ __('bread::manager.edit_lists_for_table', ['table' => $table]) }}
</h1>
@stop

@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li>
        <a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a>
    </li>
    <li><a href="{{ route('voyager.bread.index') }}">{{ __('bread::manager.bread_manager') }}</a></li>
    <li><a href="{{ route('voyager.bread.edit', ['table' => $table]) }}">{{ ucfirst($table) }}</a></li>
    <li>{{ __('bread::generic.lists') }}</li>
</ol>
@endsection

@section('content')
<div class="page-content container-fluid">
    <div id="list-builder">
        <vue-snotify></vue-snotify>
        <div class="col-md-12">
            <div class="dropdown" style="display:inline" v-if="this.lists.length > 0">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    Add Element
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-header">Formfields</li>
                    @foreach(\Bread\BreadFacade::formfields()->where('element_type', 'formfield') as $formfield)
					<li>
                        <a href="#" v-on:click="addElement('{{ $formfield->getCodeName() }}')">
                            {{ $formfield->getName() }}
                        </a>
                    </li>
					@endforeach
                </ul>
            </div>
            <div class="dropdown" style="display:inline" v-if="this.lists.length > 0">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    List (@{{ this.currentList.name }})
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li v-for="(list, key) in this.lists">
                        <a href="#" v-on:click="currentListId = key">
                            <strong v-if="currentListId == key">@{{ list.name }}</strong>
                            <span v-else>@{{ list.name }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <button @click="newListPrompt()" class="btn btn-primary">New List</button>
            <button @click="saveLists()" class="btn btn-primary">Save Lists</button>
            <button @click="deleteList()" class="btn btn-primary" v-if="this.lists.length > 0">Delete List</button>
        </div>
        <div class="clearfix"></div>
        <div id="wrapper" style="align-items: stretch;width: 100%; height: 100%;">
            <div style="position:relative;width:100%;height:100%;">
                <div v-if="this.lists.length == 0">
                    <br>
                    <div class="panel panel-bordered">
                        <div class="panel-body" style="text-align:center">
                            <h3>{{ __('bread::manager.no_lists_message') }}</h3>
                        </div>
                    </div>
                </div>
                <div v-if="this.lists.length > 0 && this.currentList.elements.length == 0">
                    <br>
                    <div class="panel panel-bordered">
                        <div class="panel-body" style="text-align:center">
                            <h3>{{ __('bread::manager.no_elements_message') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- -->
            <div class="panel panel-bordered" v-if="this.lists.length > 0 && currentList.elements.length > 0">
                <div class="row fake-table-hd">
                    <div class="col-xs-2">Field</div>
                    <div class="col-xs-3">Label</div>
                    <div class="col-xs-1">Type</div>
                    <div class="col-xs-1">Searchable</div>
                    <div class="col-xs-1">Orderable</div>
                    <div class="col-xs-1">Initial Order</div>
                    <div class="col-xs-1">Show in relationship</div>
                    <div class="col-xs-2">Actions</div>
                </div>
                <draggable v-model="currentList.elements" @end="recalculateIds">
                    <div class="row row-dd"
                         v-for="element in currentList.elements"
                         :key="element.id"
                         v-tooltip.notrigger.bottom="{ html: element.id+'_options', visible: isOptionsOpen(element.id), class: 'options-tooltip' }">
                        <div class="col-xs-2">
                            <select class="form-control" v-model="element.field">
                                <optgroup label="Fields">
                                    <option v-for="field in fields">
                                        @{{ field }}
                                    </option>
                                </optgroup>
                                @foreach ($relationships as $relationship)
                                <optgroup label="{{ $relationship }}">
                                    @foreach (\Bread\BreadFacade::getRelationshipAttributes($bread, $model, $relationship) as $rl_attr)
                                    <option value="{{ $relationship }}|{{ $rl_attr }}">{{ $rl_attr }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-3">
                            <input type="text" class="form-control" v-model="element.label">
                        </div>
                        <div class="col-xs-1">@{{ ucfirst(element.type) }}</div>
                        <div class="col-xs-1"><input type="checkbox" v-model="element.searchable"></div>
                        <div class="col-xs-1"><input type="checkbox" v-model="element.orderable"></div>
                        <div class="col-xs-1">
                            <input type="radio" :value="element.id" v-model="currentList.initial_ordered">
                        </div>
                        <div class="col-xs-1"><input type="checkbox" v-model="element.relationship_show"></div>
                        <div class="col-xs-2">
                            <button class="btn btn-primary" v-on:click="openOptions(element.id)">Options</button>
                            <button class="btn btn-danger" v-on:click="deleteElement(element.id)">Delete</button>
                        </div>
                        <div :id="element.id+'_options'">
                            <div class="pull-left">
                                <h4>Options</h4>
                            </div>
                            <div class="pull-right" @click="openOptions(null)">
                                <span class="voyager-x" style="cursor:pointer;"></span>
                            </div>
                            <div class="clearfix"></div>
                            <component :is="'formfield-'+element.type" v-bind="element" :show="'options'" :type="'list'"></component>
                            <validation-form v-bind="element" />
                        </div>
                    </div>
                </draggable>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="col-md-6">
                <div class="panel panel-bordered" v-if="this.lists.length > 0">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-data"></i> Data
                            <span class="panel-desc"> The Data to be shown in this list</span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="radio">
                            <label>
                                <input type="radio" value="all" v-model="currentList.data">All Data
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" value="scope" v-model="currentList.data">Method/Scope
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                :disabled="currentList.data != 'scope'"
                                v-model="currentList.scope"
                                placeholder="Method/Scope Name">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-bordered" v-if="this.lists.length > 0">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-lock"></i> Roles
                            <span class="panel-desc"> The roles which use this List</span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <select class="form-control" multiple v-model="currentList.browse_roles">
                            <option v-for="(role, key) in roles" v-bind:value="key">@{{ role }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row clearfix"></div>
@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
@foreach(\Bread\BreadFacade::formfields() as $formfield)
@include($formfield->getComponent('list'))
<input type="hidden" id="{{ $formfield->getCodeName() }}_default_options" value="{{ $formfield->getOptions($bread) }}">
@endforeach
@include('bread::components.validation-form')
<script>
var builder = new Vue({
    el: "#list-builder",
    data: {
        lists: {!! $lists->toJson() !!},
        fields: {!! $fields->toJson() !!},
        roles: {!! \TCG\Voyager\Models\Role::all()->pluck('display_name', 'id')->toJson() !!},
        currentListId: 0,
        currentOptionId: null,
    },
    computed: {
        currentList() {
            return this.lists[this.currentListId];
        },
    },
    methods: {
        isOptionsOpen: function(id)
        {
            if (this.currentOptionId == id)
                return true;
            return false;
        },
        openOptions: function(id) {
            this.currentOptionId = id;
        },
        deleteElement(id) {
            this.$snotify.confirm('Are you sure you want to delete this element?', 'Delete Element?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        var del_key = -1;
                        this.currentList.elements.map(function(el, key) {
                            if (el.id == id) {
                                del_key = key;
                            }
                        });
                        if (del_key >= 0) {
                            this.currentList.elements.splice(del_key, 1);
                            this.recalculateIds();
                        }

                        this.$snotify.remove(toast.id);
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        addElement: function(type) {
            let options = JSON.parse(document.getElementById(type+'_default_options').value);
            let newitem = {
                id: this.currentList.elements.length,
                options: options,
                type: type,
                field: null,
                searchable: true,
                orderable: true,
                validation_rules: []
            };
            this.currentList.elements.push(newitem);
        },
        deleteList() {
            this.$snotify.confirm('Are you sure you want to delete this List?', 'Delete List?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.lists.splice(this.currentListId, 1);
                        this.currentListId = this.lists.length - 1;
                        this.$snotify.remove(toast.id);
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        saveLists: function() {
            this.$http.post('{{ route("voyager.bread.storelayouts", ["table" => $table]) }}', {
                lists: JSON.stringify(this.lists),
                _token: "{{ csrf_token() }}"
            }).then(response => {
                this.$snotify.success('Lists were successfully saved.');
            }, response => {
                this.$snotify.error('Saving lists failed: ' + response.body);
            });
        },
        newListPrompt: function() {
            this.$snotify.prompt('', '{{ __("bread::manager.add_list") }}', {
                buttons: [
                    { text: '{{ __("voyager::generic.save") }}', action: (toast) => {
                        let listExists = false;
                        this.lists.forEach(function(list) {
                            if (list.name == toast.value)
                                listExists = true;
                        });
                        if (toast.value != '' && !listExists) {
                            let list = {
                                name: toast.value,
                                type: "view",
                                elements: []
                            };
                            this.lists.push(list);
                            this.currentListId = this.lists.length - 1;
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
        ucfirst: function(input) {
            return input.charAt(0).toUpperCase() + input.slice(1)
        },

        recalculateIds: function() {
            this.currentList.elements = this.currentList.elements.map(function(el, i) {
                el.id = i;
                return el;
            });
        }
    },
    mounted: function() {

    }
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection
