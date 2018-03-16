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
					<h3 class="panel-title">{{ __('bread::manager.tables_with_bread') }}</h3>
				</div>
				<div class="panel-body">
		            <table class="table table-bordered table-striped">
		                <thead>
		                    <tr>
		                        <th>{{ __('bread::generic.name') }}</th>
								<th>{{ __('bread::manager.views') }}</th>
								<th>{{ __('bread::manager.lists') }}</th>
		                        <th style="text-align:right">{{ __('bread::generic.actions') }}</th>
		                    </tr>
		                </thead>
		                @foreach($breads as $bread)
		                <tr>
		                    <td>
		                        <strong class="name">
		                            <a href="{{ route('voyager.bread.edit', $bread->table_name) }}">{{ $bread->table_name }}</a>
		                        </strong>
		                    </td>
							<td>
                                @foreach ($bread->views as $view)
                                <a href="{{ route('voyager.bread.edit.view', $view) }}">
                                    {{ $view->name }}
                                </a>
                                @if(!$loop->last) , @endif
                                @endforeach
                                <div class="actions pull-right">
                                    <a href="#" class="btn btn-sm btn-success add-view-modal" data-type="view" data-id="{{ $bread->id }}">
                                        <i class="voyager-plus"></i> {{ __('bread::manager.add_view') }}
                                    </a>
                                </div>
							</td>
							<td>
                               <div class="actions">
                                    @foreach ($bread->lists as $list)
                                    <a href="{{ route('voyager.bread.edit.view', $list) }}">
                                        {{ $list->name }}
                                    </a>
                                    @if(!$loop->last) , @endif
                                    @endforeach
                                    <div class="pull-right">
                                        <a href="#" class="btn btn-sm btn-success add-view-modal" data-type="list" data-id="{{ $bread->id }}">
                                            <i class="voyager-plus"></i> {{ __('bread::manager.add_list') }}
                                        </a>
                                    </div>
                                </div>
							</td>
		                    <td>
		                        <div class="actions pull-right">
                                    <form method="post" action="{{ route('voyager.bread.destroy', $bread->id) }}">
                                        {{ csrf_field() }}
                                        <input name="_method" type="hidden" value="DELETE">
                                        <a href="{{ route('voyager.'.$bread->slug.'.index') }}" class="btn btn-sm btn-warning">
                                           <i class="voyager-eye"></i> {{ __('voyager::generic.view') }}
                                        </a>
                                        <a href="{{ route('voyager.bread.edit', $bread->table_name) }}" class="btn btn-sm btn-primary">
                                           <i class="voyager-edit"></i> {{ __('voyager::generic.edit') }}
                                        </a>

                                        <button type="submit" class="btn btn-sm btn-danger delete-bread" style="padding: 5px 10px; font-size: 12px;">
                                           <i class="voyager-trash"></i> {{ __('voyager::generic.delete') }}
                                        </button>
                                    </form>
		                        </div>
		                    </td>
		                </tr>
		                @endforeach
		            </table>
				</div>
			</div>
        </div>

		<div class="col-md-12">
			<div class="panel panel-bordered panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">{{ __('bread::manager.tables_wo_bread') }}</h3>
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
		                        <p class="name">{{ $table }}</p>
		                    </td>

		                    <td>
                                <div class="actions pull-right">
    		                        <a href="{{ route('voyager.bread.create', $table) }}" class="btn btn-sm btn-success">
    		                           <i class="voyager-plus"></i> {{ __('bread::manager.add_bread') }}
    		                        </a>
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

<div class="modal fade" id="addviewmodal" tabindex="-1" role="dialog" aria-labelledby="addviewmodallabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('voyager.bread.store.view') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="addviewmodallabel">{{ __('bread::manager.add_view') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="bread_id" value="">
                    <input type="hidden" name="view_type" value="view">
                    <div class="form-group">
                        <label class="col-form-label">{{ __('voyager::generic.name') }}:</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="submit" name="create_default" value="true" class="btn btn-primary">{{ __('bread::manager.create_default') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('bread::generic.create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
$('.add-view-modal').on('click', function(e) {
    e.preventDefault();
    var type = $(this).data('type');
    var id = $(this).data('id');
    $modal = $('#addviewmodal');
    if(type == 'view') {
        $modal.find('#addviewmodallabel').text('{{ __('bread::manager.add_view') }}');
        $modal.find('input[name="view_type"]').val('view');
    } else {
        $modal.find('#addviewmodallabel').text('{{ __('bread::manager.add_list') }}');
        $modal.find('input[name="view_type"]').val('list');
    }
    $modal.find('input[name="bread_id"]').val(id);

    $modal.modal('show');
});

$('.delete-bread').on('click', function(e) {
    e.preventDefault();
    $form = $(this).closest('form');
    toastr.info('{{ __('bread::manager.delete_bread_question') }}<br /><br /><button type="submit" class="btn btn-danger final-delete">{{ __('voyager::generic.yes') }}</button>', '{{ __('bread::manager.delete_bread') }}');
    toastr.options = {
        'escapeHtml': false,
    };
    $('body').on('click', '.final-delete', function(e) {
        e.preventDefault();
        $form.submit();
    });
});
</script>
@endsection
