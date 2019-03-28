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
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" @click="selectAll($event.target.checked)"></th>
                                <th v-for="(column, key) in parameter.columns" :key="'th_'+key" @click="orderBy(column.field)">
                                    @{{ column.label }}
                                </th>
                                <th>Actions</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th v-for="(column, key) in parameter.columns" :key="'th_search_'+key">
                                    <input type="text" class="form-control"
                                           v-if="column.searchable"
                                           :placeholder="column.search_text"
                                           @input="filterBy(column.field, $event.target.value)">
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, key) in rows" :key="'tr'+key">
                                <td><input type="checkbox" @click="selectItem($event.target.checked, row.computed_actions.pk)"></td>
                                <td v-for="(column, key) in parameter.columns" :key="'tr_'+key+column.field">
                                        <formfield-base
                                                        :view="'browse'"
                                                        :type="slugify(column.type)"
                                                        :layout-type="'list'"
                                                        :layout="layout"
                                                        :options="column.options"
                                                        :validation="column.validation"
                                                        :value="row[column.field]">
                                        </formfield-base>
                                </td>
                                <td></td>
                            </tr>
                            <tr v-if="rows.length == 0">
                                <td :colspan="parameter.columns.length + 2" style="text-align:center">No results :(</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td :colspan="parameter.columns.length + 2">
                                    <pagination :pages="pages" :page="parameter.page" :callback="openPage" class="pagination-sm" />
                                </td>
                            </tr>
                        </tfoot>
                    </table>
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
        rows: [],
        totalRecords: 0,
        parameter: {
            columns: {!! json_encode($layout->getColumnDefinitions()) !!},
            page: 1,
            perPage: 10,
            filters: {},
            orderField: '',
            orderDir: 'asc',
            locale: null,
            _token: '{{ csrf_token() }}',
        }
    },
    methods: {
        orderBy: function (field) {
            if (this.parameter.orderField == field && this.parameter.orderDir == 'asc') {
                this.parameter.orderDir = 'desc';
            } else {
                this.parameter.orderDir = 'asc';
            }
            this.parameter.orderField = field;
            this.loadItems();
        },
        filterBy: function (field, query) {
            this.parameter.filters[field] = query;
            this.loadItems();
        },
        openPage: function (page) {
            this.parameter.page = page;
            this.loadItems();
        },
        selectPerPage: function (number) {
            this.parameter.perPage = number;
            this.loadItems();
        },
        loadItems: Vue.prototype.debounce(function () {
            this.locale = this.$eventHub.locale;
            this.$http.post('{{ route('voyager.'.$bread->getTranslation('slug').'.data') }}', this.parameter).then(response => {
                this.totalRecords = response.body.records;
                this.rows = response.body.rows;
            }, response => {
                toastr.error('Loading data failed: '+response.body.message);
            });
        }, 300),
        selectAll: function (select) {
            
        },
        selectItem: function (select, pk) {

        }
    },
    computed: {
        pages: function () {
            return Math.ceil(this.totalRecords / this.parameter.perPage);
        },
    },
    mounted: function () {
        @localization
        this.loadItems();
        
    },
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection