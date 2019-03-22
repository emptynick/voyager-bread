@extends('voyager::master')
@section('page_title', __('bread::bread.browse_name_plural', ['name' => $bread->getTranslation('name_plural')]))

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
                                    @on-per-page-change="onPerPageChange">
                        <template slot="table-row" slot-scope="props">
                            <span>
                                <formfield-base :view="'browse'"
                                                :type="slugify(props.column.type)"
                                                :layout-type="'list'"
                                                :layout="layout"
                                                :options="props.column.options"
                                                :validation="props.column.validation"
                                                :value="props.formattedRow[props.column.field]">
                                </formfield-base>
                            </span>
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
                field: '',
                type: '',
            },
            page: 1, 
            perPage: 10,
            _token: '{{ csrf_token() }}',
            columns: this.columns,
        }
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
            this.$http.post('{{ route('voyager.'.$bread->getTranslation('slug').'.data') }}', this.serverParams).then(response => {
                this.totalRecords = response.body.records;
                this.rows = response.body.rows;
                this.isLoading = false;
            }, response => {
                toastr.error('Loading data failed: '+response.body.message);
                this.isLoading = false;
            });
        },
        getValue: function (row, field) {
            return row[field];
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
                selectionText: '{{ __("bread::bread.names_selected", ["name" => $bread->getTranslation("name_plural")]) }}',
                clearSelectionText: '{{ __("bread::generic.clear") }}',
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