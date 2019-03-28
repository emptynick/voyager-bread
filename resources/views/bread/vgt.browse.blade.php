@extends('voyager::master')
@section('page_title', __('bread::bread.browse_name_plural', ['name' => $bread->getTranslation('name_plural')]))

@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li><a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a></li>
    <li class="active">{{ $bread->getTranslation('name_plural') }}</li>
</ol>
@endsection

@section('content')
<div class="page-content container-fluid" id="bread-browse">
    <language-picker></language-picker>
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary panel-bordered">                
                <div class="panel-heading">
                    <h3 class="panel-title panel-icon"><i class="voyager-info-circled"></i> {{ __('bread::bread.browse_name_plural', ['name' => $bread->getTranslation('name_plural')]) }}</h3>
                    <div class="panel-actions">
                        <a class="panel-action voyager-angle-up" data-toggle="panel-collapse" aria-hidden="true"></a>
                    </div>
                </div>

                <div class="panel-body" style="overflow: visible">
                    <vue-good-table :columns="columns"
                                    :rows="rows"
                                    mode="remote"
                                    :ref="'browse-table'"
                                    :total-rows="totalRecords"
                                    :sort-options="sortOptions"
                                    :select-options="selectOptions"
                                    :pagination-options="paginationOptions"
                                    :is-loading="isLoading"
                                    @on-page-change="onPageChange"
                                    @on-sort-change="onSortChange"
                                    @on-column-filter="onColumnFilter"
                                    @on-per-page-change="onPerPageChange"
                                    @on-selected-rows-change="selectionChanged">
                        <template slot="table-row" slot-scope="props">
                            <div class="text-center" v-if="props.column.field == 'computed_actions'">
                                    <a class="btn btn-primary" :href="props.formattedRow.computed_actions.read">
                                        <i class="voyager-eye"></i> Read
                                    </a>
                                    <a class="btn btn-success" :href="props.formattedRow.computed_actions.edit">
                                        <i class="voyager-edit"></i> Edit
                                    </a>
                                    <a class="btn btn-danger" :href="props.formattedRow.computed_actions.delete">
                                        <i class="voyager-trash"></i> Delete
                                    </a>
                            </div>
                            <formfield-base v-else
                                            :view="'browse'"
                                            :type="slugify(props.column.type)"
                                            :layout-type="'list'"
                                            :layout="layout"
                                            :options="props.column.options"
                                            :validation="props.column.validation"
                                            :value="props.formattedRow[props.column.field]">
                            </formfield-base>
                        </template>
                        <div slot="emptystate">
                            <div v-if="rows.length == 0" class="vgt-center-align vgt-text-disabled">
                                {{ __('bread::bread.no_matching_name_plural', ['name' => $bread->getTranslation('name_plural')]) }}
                            </div>
                        </div>
                        <span class="vgt-loading__content" slot="loadingContent">
                            {{ __('bread::generic.loading') }}...
                        </span>
                        <div slot="selected-row-actions">
                            <button class="btn btn-danger">Delete</button>
                        </div>
                    </vue-good-table>
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
    el: "#bread-browse",
    data: {
        bread: {!! json_encode($bread) !!},
        layout: {!! json_encode($layout) !!},
        columns: {!! json_encode($layout->getColumnDefinitions()) !!},
        rows: [],
        totalRecords: 0,
        isLoading: true,
        serverParams: {
            columnFilters: {},
            sort: {
                field: null,
                type: 'asc',
            },
            page: 1, 
            perPage: 10,
            _token: '{{ csrf_token() }}',
            columns: this.columns,
        },
        selectedText: '',
        tableLoaded: false,
    },
    methods: {
        updateParams: function (newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },
        onPageChange: function (params) {
            this.updateParams({page: params.currentPage});
            this.loadItems();
        },
        onPerPageChange: function (params) {
            this.updateParams({perPage: params.currentPerPage});
            this.loadItems();
        },
        onSortChange: function (params) {
            this.updateParams({
                sort: {
                    type: params[0].type,
                    field: params[0].field,
                },
            });
            this.loadItems();
        },
        onColumnFilter: function (params) {
            this.updateParams(params);
            this.loadItems();
        },
        loadItems: function () {
            if (!this.tableLoaded) {
                return;
            }
            this.isLoading = true;
            this.serverParams.locale = this.$eventHub.locale;
            console.log(this.serverParams);
            this.$http.post('{{ route('voyager.'.$bread->getTranslation('slug').'.data') }}', this.serverParams).then(response => {
                this.totalRecords = response.body.records;
                this.rows = response.body.rows;
                this.isLoading = false;
            }, response => {
                toastr.error('Loading data failed: '+response.body.message);
                this.isLoading = false;
            });

            if (history.pushState && this.tableLoaded) {
                var par = this.serverParams;
                var parameter = '?page=';
                parameter += par.page;
                parameter += '&perPage=';
                parameter += par.perPage;
                parameter += '&orderBy=';
                parameter += par.sort.field;
                parameter += '&orderDir=';
                parameter += par.sort.type;

                for (c in par.columnFilters) {
                    parameter += '&filter['+c+']=';
                    parameter += encodeURIComponent(par.columnFilters[c]);
                }
                

                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname+parameter;
                window.history.pushState({ path: newurl }, '', newurl);
            }
        },
        selectionChanged: function () {
            if (this.$refs['browse-table'].selectedRowCount == 1) {
                this.selectedText = '{{ __("bread::bread.name_selected", ["name" => $bread->getTranslation("name_singular")]) }}';
            } else {
                this.selectedText = '{{ __("bread::bread.names_selected", ["name" => $bread->getTranslation("name_plural")]) }}';
            }
        },
    },
    computed: {
        sortOptions: function () {
            return {
                enabled: true,
                initialSortBy: {
                    field: this.layout.order_by || this.layout.formfields[0].options.field,
                    type: 'asc',
                }
            };
        },
        selectOptions: function () {
            return {
                enabled: true,
                selectionText: this.selectedText,
                clearSelectionText: '{{ __("bread::generic.clear") }}',
                selectOnCheckboxOnly: true
            };
        },
        paginationOptions: function () {
            return {
                enabled: true,
                rowsPerPageLabel: '{{ __("bread::bread.pagination_name_per_page", ["name" => $bread->getTranslation("name_plural")]) }}',
                ofLabel: '{{ __("bread::bread.pagination_of") }}',
                nextLabel: '{{ __("bread::bread.pagination_next") }}',
                prevLabel: '{{ __("bread::bread.pagination_previous") }}',
                allLabel: '{{ __("bread::generic.all") }}',
            };
        },
    },
    mounted: function () {
        @localization
        var par = new URLSearchParams(window.location.search);
        if (par.toString() != '') {
            if (par.has('page')) {
                this.serverParams.page = parseInt(par.get('page'));
                this.$refs['browse-table'].changePage(this.serverParams.page);
            }
            if (par.has('perPage')) {
                this.serverParams.perPage = parseInt(par.get('perPage'));
                this.$refs['browse-table'].perPage = this.serverParams.perPage;
            }
            if (par.has('orderBy') && par.has('orderDir')) {
                this.serverParams.sort.field = par.get('orderBy');
                this.serverParams.sort.type = par.get('orderDir');
                this.$refs['browse-table'].sorts[0] = this.serverParams.sort;
            }
            if (par.has('filter')) {
                console.log('Ahjo');
            }

            for (column in this.columns) {
                var field = this.columns[column].options;
                if (field && field.field) {
                    if (par.has('filter['+field.field+']')) {
                        field = field.field;
                        value = par.get('filter['+field+']');
                        console.log(field+' : '+value);
                        this.serverParams.columnFilters[field] = value;
                        console.log(this.serverParams.columnFilters);
                    }
                }
            }
            this.$refs['browse-table'].filterRows(this.serverParams.columnFilters, false);
            this.$refs['browse-table'].handleSearch();

            this.tableLoaded = true;
            this.loadItems();
        } else {
            this.tableLoaded = true;
        }
        /**
         * 


            for (c in par.columnFilters) {
                parameter += '&filter['+c+']=';
                parameter += encodeURIComponent(par.columnFilters[c]);
            }*/
    },
    created: function () {
        this.serverParams.columns = this.columns;
    },
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection