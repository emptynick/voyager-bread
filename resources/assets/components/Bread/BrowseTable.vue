<template>
    <div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" @click="selectAll($event.target.checked)"></th>
                    <th v-for="(column, key) in parameter.columns" :key="'th_'+key" @click="orderBy(column.field)">
                        {{ column.label }}
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
                            View
                        </a>
                        <a class="btn btn-sm btn-warning" :href="row['computed_actions']['edit']">
                            <i class="voyager-edit"></i>
                            Edit
                        </a>
                        <a class="btn btn-sm btn-danger" :href="row['computed_actions']['delete']" @click.prevent="deleteEntry(row['computed_actions']['pk'])">
                            <i class="voyager-trash"></i>
                            Delete
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
        <div class="overlay" v-if="loading">
            <img :src="loadingImage" alt="Voyager Loader">
        </div>
    </div>
</template>

<script>
export default {
    props: ['urlData', 'urlDelete', 'nameSingular', 'namePlural', 'token', 'bread', 'layout', 'columns', 'loadingImage'],
    data: function () {
        return {
            rows: [],
            totalRecords: 0,
            loading: true,
            selectedItems: [],
            parameter: {
                columns: [],
                page: 1,
                perPage: 10,
                filter: {},
                orderField: '',
                orderDir: 'asc',
                locale: null,
                _token: '',
            }
        };
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
            this.$http.post(this.urlData, this.parameter).then(response => {
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
            var url = this.urlDelete;
            url = Vue.prototype.getUrl(url, pk);
            this.$snotify.confirm(
                '',
                '', {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [
                    {text: 'Yes', action: (toast) => {
                        this.$snotify.remove(toast.id);
                        this.$http.post(url, {
                            _token: this.token,
                            _method: 'DELETE'
                        }).then(response => {
                            this.$snotify.success('Successfully deleted...', 'Deleted');
                            this.loadItems();
                        }, response => {
                            toastr.error('Deleting failed: '+response.body.message);
                        });
                    }},
                    {text: 'No', action: (toast) => {
                        this.$snotify.remove(toast.id);
                    }},
                ]
            });
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
        this.parameter.columns = this.columns;
        this.parameter._token = this.token;
    }
};
</script>
<style scoped>
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