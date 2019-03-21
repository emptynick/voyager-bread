@extends('voyager::master')
@section('page_title', __('bread::manager.manager'))

@section('content')
<div class="page-content container-fluid" id="manager-browse">
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped database-tables">
                <thead>
                    <tr>
                        <th>{{ __('voyager::database.table_name') }}</th>
                        <th style="text-align:right">{{ __('voyager::bread.bread_crud_actions') }}</th>
                    </tr>
                </thead>
                <tr v-for="table in tables">
                    <td>
                        <p class="name">
                            <a href="#" class="desctable">
                               @{{ table }}
                            </a>
                            <i class="voyager-data" style="font-size:25px; position:absolute; margin-left:10px; margin-top:-3px;"></i>
                        </p>
                    </td>

                    <td class="actions text-right">
                        <a :href="getUrl('{{ route('voyager.bread.edit', '#') }}', table)" class="btn btn-primary btn-sm edit" v-if="tableHasBread(table)">
                            <i class="voyager-edit"></i> {{ __('voyager::generic.edit') }}
                        </a>
                        <a href="#" class="btn btn-danger btn-sm delete" v-if="tableHasBread(table)">
                            <i class="voyager-trash"></i> {{ __('voyager::generic.delete') }}
                        </a>
                        <a :href="getUrl('{{ route('voyager.bread.create', '#') }}', table)" class="_btn btn-default btn-sm pull-right" v-if="!tableHasBread(table)">
                            <i class="voyager-plus"></i> {{ __('voyager::bread.add_bread') }}
                        </a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
<script>
var builder = new Vue({
    el: "#manager-browse",
    data: {
        tables: {!! json_encode(Bread::getTables()) !!},
        breads: {!! json_encode(Bread::getBreads()) !!},
    },
    methods: {
        tableHasBread: function(table) {
            var hasBread = false;
            this.breads.forEach(function (bread) {
                if (bread.table == table) {
                    hasBread = true;
                }
            });

            return hasBread;
        }
    }
});
</script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection