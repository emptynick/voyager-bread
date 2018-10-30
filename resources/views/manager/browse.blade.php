@extends('voyager::master')
@section('page_title', 'BREAD Manager')
@section('page_header')
<h1 class="page-title">
    <i class="voyager-bread"></i> BREAD Manager
</h1>
@stop
@section('content')
<div class="page-content container-fluid" id="bread-manager">
    @include('voyager::alerts')
    <vue-snotify></vue-snotify>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Tables</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tr v-for="table in tables">
                            <td>
                                <p class="name">
                                    <a href="#">@{{ table }}</a>
                                </p>
                            </td>

                            <td>
                                <div class="actions pull-right">
                                    <div v-if="hasBread(table)">
                                        <a :href="getUrl(edit_url, table)" class="btn btn-sm btn-primary">
                                            <i class="voyager-edit"></i> Edit BREAD
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger delete-bread" v-on:click.prevent="deleteBread(table)">
                                            <i class="voyager-trash"></i> Delete BREAD
                                        </a>
                                    </div>
                                    <a v-else :href="getUrl(add_url, table)" class="btn btn-sm btn-success">
                                        <i class="voyager-plus"></i> Add BREAD
                                    </a>
                                </div>
                            </td>
                        </tr>
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
    el: "#bread-manager",
    data: {
        tables: {!! json_encode($tables) !!},
        breads: {!! json_encode($breads) !!},
        delete_url: '{{ route("voyager.bread.delete", "#") }}',
        edit_url: '{{ route("voyager.bread.edit", "#") }}',
        add_url: '{{ route("voyager.bread.create", "#") }}',
    },
    methods: {
        hasBread: function(table) {
            for (i in this.breads) {
                if (this.breads[i].table == table) {
                    return true;
                }
            }
            return false;
        },
        deleteBread: function(table) {
            this.$snotify.confirm('Are you sure you want to delete this BREAD?', 'Delete BREAD?', {
                buttons: [
                    { text: 'Yes', action: (toast) => {
                        this.$http.post(this.getUrl(this.delete_url, table), {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                        }).then(response => {
                            this.$snotify.remove(toast.id);
                            this.$snotify.success('The BREAD was deleted.');
                            //Todo: remove BREAD from this.breads
                        }, response => {
                            this.$snotify.remove(toast.id);
                            this.$snotify.error('There was a problem deleting this BREAD: ' + response.statusText);
                        });
                    }},
                    { text: 'No', action: (toast) => this.$snotify.remove(toast.id)},
                ],
            });
        },
        getUrl: function(url, variable) {
            return url.replace('#', variable);
        }
    }
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection
