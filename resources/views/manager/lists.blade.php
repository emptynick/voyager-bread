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
        <language-switcher :languages="{{ json_encode(config('voyager.multilingual.locales')) }}"></language-switcher>
        <vue-snotify></vue-snotify>
        <div class="col-md-12">
            <div class="dropdown" style="display:inline" v-if="this.lists.length > 0">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    {{ __("bread::manager.add_element") }}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-header">{{ __("bread::generic.formfields") }}</li>
                    @foreach(\Bread\BreadFacade::formfields()->where('group', 'formfield') as $formfield)
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
                    {{ __("bread::generic.list") }} (@{{ this.currentList.name }})
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
            <button @click="newListPrompt()" class="btn btn-primary">{{ __('bread::manager.list_new') }}</button>
            <button @click="saveLists()" class="btn btn-primary">{{ __('bread::manager.list_save') }}</button>
            <button @click="deleteList()" class="btn btn-primary" v-if="this.lists.length > 0">{{ __('bread::manager.list_delete') }}</button>
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
                    <div class="col-xs-2">{{ __('bread::manager.field') }}</div>
                    <div class="col-xs-3">{{ __('bread::manager.label') }}</div>
                    <div class="col-xs-1">{{ __('bread::manager.type') }}</div>
                    <div class="col-xs-1">{{ __('bread::manager.searchable') }}</div>
                    <div class="col-xs-1">{{ __('bread::manager.orderable') }}</div>
                    <div class="col-xs-1">{{ __('bread::manager.initial_order') }}</div>
                    <div class="col-xs-1">{{ __('bread::manager.show_in_relationship') }}</div>
                    <div class="col-xs-2">{{ __('bread::generic.actions') }}</div>
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
                                <optgroup label="{{ $relationship['name'] }}">
                                    <!-- Todo: display relationship attributes here -->
                                    @foreach ($relationship['attributes'] as $attr)
                                    <option value="{{ $relationship['name'] }}|{{ $attr }}">{{ $relationship['name'].'.'.$attr }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-3">
                            <language-input type="text" v-model="element.label" :input="element.label">
                        </div>
                        <div class="col-xs-1">@{{ ucfirst(element.type) }}</div>
                        <div class="col-xs-1"><input type="checkbox" v-model="element.searchable"></div>
                        <div class="col-xs-1"><input type="checkbox" v-model="element.orderable"></div>
                        <div class="col-xs-1">
                            <input type="radio" :value="element.id" v-model="currentList.initial_ordered">
                        </div>
                        <div class="col-xs-1">
                            <input type="radio" :value="element.id" v-model="currentList.relationship">
                        </div>
                        <div class="col-xs-2">
                            <button class="btn btn-primary" v-on:click="openOptions(element.id)">{{ __('bread::generic.options') }}</button>
                            <button class="btn btn-danger" v-on:click="deleteElement(element.id)">{{ __('voyager::generic.delete') }}</button>
                        </div>
                        <div :id="element.id+'_options'">
                            <div class="pull-left">
                                <h4>{{ __('bread::generic.options') }}</h4>
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
            <div class="col-md-3">
                <div class="panel panel-bordered" v-if="this.lists.length > 0">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-data"></i> {{ __('bread::generic.data') }}
                            <span class="panel-desc"> {{ __('bread::manager.data_to_be_show') }}</span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="radio">
                            <label>
                                <input type="radio" value="all" v-model="currentList.data">{{ __('bread::manager.all_data') }}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" value="scope" v-model="currentList.data">{{ __('bread::manager.method_scope') }}
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                :disabled="currentList.data != 'scope'"
                                v-model="currentList.scope"
                                placeholder="{{ __('bread::manager.method_scope_name') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-bordered" v-if="this.lists.length > 0">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-trash"></i> {{ __('bread::manager.trashed') }}
                            <span class="panel-desc"> {{ __('bread::manager.trashed_info') }}</span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="radio">
                            <label>
                                <input type="radio" value="show" v-model="currentList.trashed">{{ __('bread::generic.show') }}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" value="hide" v-model="currentList.trashed">{{ __('bread::generic.hide') }}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" value="only" v-model="currentList.trashed">Only
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" value="select" v-model="currentList.trashed">{{ __('bread::generic.select') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-bordered" v-if="this.lists.length > 0">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-lock"></i> {{ __('bread::manager.roles') }}
                            <span class="panel-desc"> {{ __('bread::manager.roles_info') }}</span>
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
@include('bread::components.language-switcher')
@include('bread::components.language-input')
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
            this.$snotify.confirm('{{ __('bread::manager.delete_element_confirm') }}', '{{ __('bread::manager.delete_element') }}', {
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
            this.$snotify.confirm('{{ __('bread::manager.list_delete_confirm') }}', '{{ __('bread::manager.list_delete') }}?', {
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
                this.$snotify.success('{{ __("bread::manager.lists_saved") }}');
            }, response => {
                this.$snotify.error('{{ __("bread::manager.lists_save_failed") }} ' + response.body);
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
                                type: "list",
                                trashed: 'hide',
                                data: 'all',
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
