@extends('voyager::master')

@section('page_title', __('bread::manager.bread_manager'))

@section('page_header')
<h1 class="page-title">
    <i class="voyager-bread"></i> {{ __('bread::manager.bread_manager') }}
</h1>
@stop

@section('content')
<div class="page-content container-fluid">
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
    		                        @if (Bread\BreadFacade::hasBread($table))
                                    <a href="{{ route('voyager.bread.views.edit', $table) }}" class="btn btn-sm btn-primary">
    		                           <i class="voyager-lightbulb"></i> Views
    		                        </a>
                                    <a href="{{ route('voyager.bread.lists.edit', $table) }}" class="btn btn-sm btn-primary">
    		                           <i class="voyager-list"></i> Lists
    		                        </a>
                                    <a href="{{ route('voyager.bread.edit', ['table' => $table]) }}" class="btn btn-sm btn-primary">
    		                           <i class="voyager-edit"></i> {{ __('bread::manager.edit_bread') }}
    		                        </a>
                                    <a href="{{ route('voyager.bread.destroy', $table) }}" class="btn btn-sm btn-danger">
    		                           <i class="voyager-trash"></i> {{ __('bread::manager.delete_bread') }}
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
<script>

</script>
@endsection
