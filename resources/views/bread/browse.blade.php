@extends('voyager::master')
@section('page_title', __('voyager::generic.viewing').' '.get_translated_value($bread->display_name_plural))

@section('content')
<div id="bread-browse">
    <vue-snotify></vue-snotify>
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $bread->icon }}"></i> {{ get_translated_value($bread->display_name_plural) }}
        </h1>
        @can('add', $model)
            <a href="{{ route('voyager.'.get_translated_value($bread->slug).'.create') }}" class="btn btn-success">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', $model)
            <a href="#" v-on:click="deleteEntries()" v-if="deleteIds.length > 0" class="btn btn-danger">
                <i class="voyager-trash"></i> <span>{{ __('voyager::generic.bulk_delete') }} (@{{ deleteIds.length }})</span>
            </a>
        @endcan
        @if ($model->isTranslatable)
        <language-switcher :languages="{{ json_encode(config('voyager.multilingual.locales')) }}"></language-switcher>
        @endif
    </div>
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <v-server-table :url="tableUrl" :columns="columns" :options="options" ref="browse_table">
                            <template v-for="col in this.columns" :slot="col" slot-scope="props">
                                <component
                                    :is="'formfield-'+props.row[col].type"
                                    :options="JSON.parse(props.row[col].options)"
                                    :name="''"
                                    :show="'browse'"
                                    :locale="'{{ app()->getLocale() }}'"
                                    :input="props.row[col].data"
                                ></component>
                            </template>
                            <div slot="h__bread_delete">
                                @can('delete', $model)
                                    <input type="checkbox" id="select_all_checkbox" v-model="selectAll" v-on:click="toggleSelectAll()">
                                @endcan
                            </div>
                            <div slot="bread_delete" slot-scope="props">
                                @can('delete', $model)
                                    <input type="checkbox" v-model="deleteIds" :value="props.row.bread_key">
                                @endcan
                            </div>
                            <div slot="bread_actions" slot-scope="props" class="pull-right">
                                @can('read', $model)
                                <a :href="props.row.bread_read" class="btn btn-sm btn-warning">
                                    <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.view') }}</span>
                                </a>
                                @endcan
                                @can('edit', $model)
                                <a :href="props.row.bread_edit" class="btn btn-sm btn-primary">
                                    <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.edit') }}</span>
                                </a>
                                @endcan
                                @can('delete', $model)
                                <a href="#" @if ($soft_delete)v-if="!props.row.deleted_at"@endif class="btn btn-sm btn-danger" v-on:click="deleteEntry(props.row.bread_delete)">
                                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
                                </a>
                                @if ($soft_delete)
                                <a href="#" v-if="props.row.deleted_at" class="btn btn-sm btn-success" v-on:click="restoreEntry(props.row.restore)">
                                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Restore</span>
                                </a>
                                @endif
                                @endcan
                            </div>
                            <div slot="h__bread_actions">
                                <div class="pull-right">Actions</div>
                            </div>

                            <div slot="beforeLimit">
                                @if ($soft_delete)
                                <div v-if="this.layout.trashed == 'select'">
                                    Trashed:
                                    <select class="form-control" v-model="withTrashed">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                        <option value="only">Only</option>
                                    </select>
                                </div>
                                @endif
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
@foreach(\Bread\BreadFacade::formfields() as $formfield)
@include($formfield->getComponent('view'))
@endforeach
@include('bread::components.language-switcher')
<script>
new Vue({
    el: "#bread-browse",
    data: {
        withTrashed: 'no',
        deleteIds: [],
        selectAll: false,
        layout: {!! collect($layout)->toJson() !!},
        columns: {!! $layout->elements->pluck('field')->prepend('bread_delete')->push('bread_actions')->toJson() !!},
        options: {
            debounce: 750,
            filterByColumn: true,
            filterable: {!! $layout->elements->where('searchable', true)->pluck('field')->toJson() !!},
            sortable: {!! $layout->elements->where('orderable', true)->pluck('field')->toJson() !!},
            headings: {
                @foreach ($layout->elements as $el)
                '{{ $el->field }}': '{{ get_translated_value($el->label) }}',
                @endforeach
            },
            listColumns: {

            },
            uniqueKey: '{{ $model->getKeyName() }}',
            orderBy: {
                column: '{!! $layout->elements->slice($layout->initial_ordered ?? 0, 1)->pluck('field')->first() !!}',
            },
            pagination: {
                edge: true
            },
            highlightMatches: true,
            texts: {
                count: "Showing {from} to {to} of {count} {{ get_translated_value($bread->display_name_plural) }}|{count} {{ get_translated_value($bread->display_name_plural) }}|1 {{ get_translated_value($bread->display_name_singular) }}",
                first: 'First',
                last: 'Last',
                filter: "Filter:",
                filterPlaceholder: "Search query",
                limit: "Records:",
                page: "Page:",
                noResults: "No matching {{ get_translated_value($bread->display_name_plural) }}",
                filterBy: "Filter by {column}",
                loading: 'Loading...',
                defaultOption: 'Select {column}',
            },
        },
        tableUrl: "{{ route('voyager.'.get_translated_value($bread->slug).'.data') }}",
    },
    methods: {
        toggleSelectAll: function() {
            this.deleteIds = [];
            this.$refs.browse_table.data.forEach((el) => {
                if (!this.selectAll) {
                    this.deleteIds.push(el.bread_key);
                }
            });
        },
        deleteEntry: function(url) {
            this.$snotify.confirm('Are you sure you want to delete this {{ get_translated_value($bread->display_name_singular) }}?', 'Delete {{ get_translated_value($bread->display_name_singular) }}?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.$http.post(url, { _token: '{{ csrf_token() }}', _method: 'delete' }).then(response => {
                            this.$refs.browse_table.refresh();
                            this.$snotify.remove(toast.id);
                        }, response => {
                            //
                        });
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        deleteEntries: function() {
            this.$snotify.confirm('Are you sure you want to delete this '+this.deleteIds.length+' {{ get_translated_value($bread->display_name_plural) }}?', 'Delete {{ get_translated_value($bread->display_name_plural) }}?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.$http.post("{{ route('voyager.'.get_translated_value($bread->slug).'.index') }}/0", { _token: '{{ csrf_token() }}', _method: 'delete', ids: this.deleteIds }).then(response => {
                            this.$refs.browse_table.refresh();
                            this.$snotify.remove(toast.id);
                            this.deleteIds = [];
                        }, response => {
                            //
                        });
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        restoreEntry: function(url) {
            this.$snotify.confirm('Are you sure you want to restore this {{ get_translated_value($bread->display_name_singular) }}?', 'Restore {{ get_translated_value($bread->display_name_singular) }}?', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.$http.post(url, { _token: '{{ csrf_token() }}' }).then(response => {
                            this.$refs.browse_table.refresh();
                            this.$snotify.remove(toast.id);
                        }, response => {
                            //
                        });
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
    },
    watch: {
        withTrashed: function(value) {
            this.tableUrl = "{{ route('voyager.'.get_translated_value($bread->slug).'.data') }}?withTrashed="+value;
        }
    },
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection
