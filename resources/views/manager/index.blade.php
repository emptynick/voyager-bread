@extends('voyager::master')

@section('page_title', __('bread::manager.bread_manager'))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-bread"></i> {{ __('bread::manager.bread_manager') }}
</h1>
@stop

@section('content')
<div class="page-content container-fluid" id="bread-manager">
    <vue-snotify></vue-snotify>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ __('bread::manager.tables') }}</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('bread::generic.name') }}</th>
                                <th style="text-align:right">{{ __('bread::generic.actions') }}</th>
                            </tr>
                        </thead>
                        @foreach($tables as $table)
                        <tr>
                            <td>
                                <p class="name">
                                    <a href="{{ route('voyager.database.edit', $table) }}">{{ $table }}</a>
                                </p>
                            </td>

                            <td>
                                <div class="actions pull-right">
                                    @if (Bread\BreadFacade::hasBreadByTable($table))
                                    <a href="{{ route('voyager.bread.views.edit', $table) }}" class="btn btn-sm btn-primary">
                                        <i class="voyager-lightbulb"></i> {{ __('bread::generic.views') }}
                                    </a>
                                    <a href="{{ route('voyager.bread.lists.edit', $table) }}" class="btn btn-sm btn-primary">
                                        <i class="voyager-list"></i> {{ __('bread::generic.lists') }}
                                    </a>
                                    <a href="{{ route('voyager.bread.edit', ['table' => $table]) }}" class="btn btn-sm btn-primary">
                                        <i class="voyager-edit"></i> {{ __('bread::manager.edit_bread') }}
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger delete-bread" data-bread="{{ $table }}" @click="deleteBread($event)">
                                        <i class="voyager-trash"></i> {{ __('bread::manager.delete_bread') }}
                                        <form method="post" ref="form" action="{{ route('voyager.bread.delete', ['table' => $table]) }}" style="display:none">
                                            {{ method_field('DELETE') }} {{ csrf_field() }}
                                        </form>
                                    </a>
                                    @else
                                    <a href="{{ route('voyager.bread.create', $table) }}" class="btn btn-sm btn-success">
                                        <i class="voyager-plus"></i> {{ __('bread::manager.add_bread') }}
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script src="{{ route('voyager.bread.scripts') }}"></script>
<script>
Vue.prototype.$type = 'view';
var builder = new Vue({
    el: "#bread-manager",
    data: {

    },
    methods: {
        deleteBread(event) {
            event.preventDefault();
            this.$snotify.confirm('{{ __('bread::manager.delete_bread_question') }}', '{{ __('bread::manager.delete_bread_title') }}', {
                buttons: [
                    { text: '{{ __('voyager::generic.yes') }}', action: (toast) => {
                        this.$refs.form.submit();
                    }},
                    { text: '{{ __('voyager::generic.yes') }}', action: (toast) => this.$snotify.remove(toast.id)},
                ],
            });
        }
    }
});
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ route('voyager.bread.styles') }}">
@endsection
