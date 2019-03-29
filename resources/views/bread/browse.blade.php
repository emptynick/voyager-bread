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
                    <div class="overlay" v-if="loading">
                        <img src="{{ voyager_asset('images/logo-icon.png') }}" alt="Voyager Loader">
                    </div>
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
                                           v-model="parameter.filter[column.field]"
                                           @input="filterBy()">
                                </th>
                                <th>
                                    
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, key) in rows" :key="'tr'+key">
                                <td><input type="checkbox" v-model="selectedItems[row.computed_actions.pk]"></td>
                                <td v-for="(column, key) in parameter.columns" :key="'tr_'+key+column.field">
                                        <formfield-base
                                                        :view="'browse'"
                                                        :type="slugify(column.type)"
                                                        :layout-type="'list'"
                                                        :layout="layout"
                                                        :options="column.options"
                                                        :validation="column.validation"
                                                        :value="getValue(row, column.field)">
                                        </formfield-base>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-primary" :href="row['computed_actions']['read']">
                                        <i class="voyager-eye"></i>
                                        {{ __('voyager::generic.view') }}
                                    </a>
                                    <a class="btn btn-sm btn-warning" :href="row['computed_actions']['edit']">
                                        <i class="voyager-edit"></i>
                                        {{ __('voyager::generic.edit') }}
                                    </a>
                                    <a class="btn btn-sm btn-danger" :href="row['computed_actions']['delete']" @click.prevent="deleteEntry(row['computed_actions']['pk'])">
                                        <i class="voyager-trash"></i>
                                        {{ __('voyager::generic.delete') }}
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="rows.length == 0">
                                <td :colspan="parameter.columns.length + 2" style="text-align:center">No results :(</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td :colspan="parameter.columns.length">
                                    <pagination
                                        :pages="pages"
                                        v-model="parameter.page"
                                        :prev-text="'Prev'"
                                        :next-text="'Next'">
                                    </pagination>
                                </td>
                                <td colspan="2">
                                    <select class="form-control pull-right" v-model="parameter.perPage" @change="loadItems()">
                                        <option :value="10">10</option>
                                        <option :value="25">25</option>
                                        <option :value="50">50</option>
                                        <option :value="100">100</option>
                                        <option :value="Number.MAX_SAFE_INTEGER">All</option>
                                    </select>
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
        loading: true,
        selectedItems: [],
        parameter: {
            columns: {!! json_encode($layout->getColumnDefinitions()) !!},
            page: 1,
            perPage: 10,
            filter: {},
            orderField: '',
            orderDir: 'asc',
            locale: null,
            _token: '{{ csrf_token() }}',
        }
    },
    methods: {
        getValue: function (row, field) {
            if (field.includes('.')) {
                var fields = field.split('.');
                
                if (row[fields[0]]) {
                    return row[fields[0]][fields[1]] || '';
                }

                return '';
            }

            return row[field];
        },
        orderBy: function (field) {
            if (this.parameter.orderField == field && this.parameter.orderDir == 'asc') {
                this.parameter.orderDir = 'desc';
            } else {
                this.parameter.orderDir = 'asc';
            }
            this.parameter.orderField = field;
            this.loadItems();
        },
        filterBy: Vue.prototype.debounce(function () {
            this.parameter.page = 1;
            this.loadItems();
        }, 300),
        loadItems: function () {
            if (!this.loading) {
                this.pushToUrl();
            }
            this.loading = true;
            this.locale = this.$eventHub.locale;
            this.$http.post('{{ route('voyager.'.$bread->getTranslation('slug').'.data') }}', this.parameter).then(response => {
                this.totalRecords = response.body.records;
                this.rows = response.body.rows;
                this.loading = false;
            }, response => {
                toastr.error('Loading data failed: '+response.body.message);
                this.loading = false;
            });
        },
        selectAll: function (select) {
            var vm = this;
            this.rows.forEach(function (row) {
                Vue.set(vm.selectedItems, row.computed_actions.pk, select);
            });
        },
        deleteEntry: function (pk) {

        },
        pushToUrl: function () {
            if (history.pushState) {
                var out = [];
                for (var key in this.parameter) {
                    if (key != 'columns' && key != '_token' && key != 'filter' && this.parameter.hasOwnProperty(key)) {
                        out.push(key + '=' + encodeURIComponent(this.parameter[key]));
                    }
                }
                for (var key in this.parameter.filter) {
                    if (this.parameter.filter[key] !== '') {
                        out.push('filter['+key+']=' + encodeURIComponent(this.parameter.filter[key]));
                    }
                }
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + out.join('&');
                window.history.pushState({ path: newurl }, '', newurl);
            }
        },
        getFromUrl: function () {
            const urlParams = new URLSearchParams(window.location.search);
            const iterator = urlParams.entries();
            let result = iterator.next();
            while (!result.done) {
                var key = result.value[0];
                var value = result.value[1];

                if (!key.startsWith('filter')) {
                    if (key == 'page' || key == 'perPage') {
                        this.parameter[key] = parseInt(value);
                    } else {
                        this.parameter[key] = value;
                    }
                } else {
                    var field = key.replace(/filter\[|\]/gi, '');
                    this.parameter.filter[field] = value;
                }

                result = iterator.next();
            }
        }
    },
    computed: {
        pages: function () {
            return Math.ceil(this.totalRecords / this.parameter.perPage);
        },
    },
    watch: {
        'parameter.page': function () {
            this.loadItems();
        }
    },
    mounted: function () {
        @localization
        this.getFromUrl();
        this.loadItems();
        
    },
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
<style>
.overlay {
    background-color: rgba(0, 0, 0, 0.05);
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0px;
    top: 0px;
    z-index: 99;
}
.overlay img{
    width: 100px;
    height: 100px;
    position: absolute;
    top: 50%;
    left: 50%;
    margin-left: -50px;
    margin-right: -50px;
    -webkit-animation: spin 1s linear infinite;
    -moz-animation: spin 1s linear infinite;
    animation: spin 1s linear infinite;
}
</style>
@endsection