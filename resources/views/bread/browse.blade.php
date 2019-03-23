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
                                    :rows="rows"
                                    :columns="columns"
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
        selectedText: ''
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
            this.isLoading = true;
            this.serverParams.locale = this.$eventHub.locale;

            if (this.serverParams.order_by || this.serverParams.order_by == '') {
                this.serverParams.order_by = this.layout.order_by || this.columns[0].field;
            }

            this.$http.post('{{ route('voyager.'.$bread->getTranslation('slug').'.data') }}', this.serverParams).then(response => {
                this.totalRecords = response.body.records;
                this.rows = response.body.rows;
                this.isLoading = false;
            }, response => {
                toastr.error('Loading data failed: '+response.body.message);
                this.isLoading = false;
            });
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
    },
    created: function () {
        this.serverParams.columns = this.columns;
    }
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection