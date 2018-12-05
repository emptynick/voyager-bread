@extends('voyager::master')
@section('page_title', 'Showing '.get_translated_value($bread->display_name_plural))

@section('content')
<div id="bread-browse">
    <vue-snotify></vue-snotify>
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $bread->icon }}"></i> {{ get_translated_value($bread->display_name_plural) }}
        </h1>
        <a href="#" @click.prevent="deleteItems()" v-if="selectedKeys.length > 0" class="btn btn-danger">
            <i class="voyager-trash"></i> <span>Bulk delete (@{{ selectedKeys.length }})</span>
        </a>
        <a href="{{ route('voyager.'.get_translated_value($bread->slug).'.create') }}" class="btn btn-success">
            <i class="voyager-plus"></i> <span>Add New</span>
        </a>
        <language-switcher></language-switcher>
    </div>
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <v-server-table :columns="columns" :options="options" ref="browse_table" @loaded="tableLoaded">
                            <template v-for="col in this.columns" :slot="col" slot-scope="data">
                                <component
                                :is="'base-'+elements[col].group"
                                v-for="(result, id) in getResultAsObject(data.row[col])"
                                show="browse"
                                :input="result"
                                v-bind="elements[col]"
                                :key="id">
                                </component>
                            </template>
                            <div slot="h__bread_delete">
                                <input type="checkbox" @click="selectAll">
                            </div>
                            <div slot="bread_delete" slot-scope="data">
                                <input type="checkbox" :value="data.row.key" v-model="selectedKeys">
                            </div>
                            <div slot="bread_actions" slot-scope="data" class="pull-right">
                                <a :href="data.row.read_url" class="btn btn-sm btn-primary">
                                    <i class="voyager-eye"></i> <span>Read</span>
                                </a>
                                <a :href="data.row.edit_url" class="btn btn-sm btn-warning">
                                    <i class="voyager-edit"></i> <span>Edit</span>
                                </a>
                                <a href="#" @click.prevent="deleteItems(data.row.delete_url)" class="btn btn-sm btn-danger">
                                    <i class="voyager-trash"></i> <span>Delete</span>
                                </a>
                            </div>
                            <div slot="h__bread_actions">
                                <div class="pull-right">Actions</div>
                            </div>
                            <div slot="beforeLimit">

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
@include('bread::components.language-switcher')
@include('bread::components.base-formfield')
@include('bread::components.base-layout-element')
@include('bread::components.base-relationship')
@foreach(\Bread\BreadFacade::formfields() as $formfield)
    @include($formfield->getComponent())
@endforeach
<script>
new Vue({
    el: "#bread-browse",
    data: {
        selectedKeys: [],
        elements: {!! $layout->elements->keyBy('computed.field') !!},
        columns: {!! $layout->elements->pluck('computed.field')->prepend('bread_delete')->push('bread_actions')->toJson() !!},
        options: {
            requestFunction: function (parameter) {
                parameter['_token'] = '{{ csrf_token() }}';
                return this.$http.post('{{ route('voyager.'.get_translated_value($bread->slug).'.data') }}', parameter).then(response => {
                    return response.body;
                }, response => {
                    this.$snotify.error(response.body.message);
                });
            },
            filterable: {!! $layout->elements->where('options.searchable', true)->pluck('computed.field')->toJson() !!},
            filterByColumn: true,
            sortable: {!! $layout->elements->where('options.orderable', true)->pluck('computed.field')->toJson() !!},
            headings: {
                @foreach ($layout->elements as $el)
                '{{ $el->computed['field'] }}': '{{ get_translated_value($el->options['title']) }}',
                @endforeach
            },
            orderBy: {
                column: '{!! $layout->elements->slice($layout->first_ordered ?? 0, 1)->pluck('computed.field')->first() !!}',
            },
            pagination: {
                edge: true
            },
            highlightMatches: true,
        },
    },
    methods: {
        selectAll: function(e) {
            this.selectedKeys = [];
            this.$refs.browse_table.data.forEach((el) => {
                if (e.target.checked) {
                    this.selectedKeys.push(el.key);
                }
            });
        },
        deleteItems: function(url = null) {
            if (!url) {
                var message = this.selectedKeys.length+' Slugs deleted.';
                url = '{{ route('voyager.'.get_translated_value($bread->slug).'.destroy', 0) }}';
            } else {
                var message = '1 Slug deleted.';
            }
            this.$http.post(url, {
                keys: this.selectedKeys,
                _token: '{{ csrf_token() }}',
                _method: 'DELETE',
            }).then(response => {
                this.$refs.browse_table.refresh();
                this.$snotify.success(message);
            }, response => {
                this.$snotify.error(response.body.message);
            });
        },
        getResultAsObject: function(input) {
            if (typeof input !== 'object') {
                return [input];
            }
            return input;
        },
        tableLoaded: function() {
            this.$bus.$emit('setLocale', this.locale);
        }
    },
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection
