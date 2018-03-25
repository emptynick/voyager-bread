@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$bread->display_name_plural)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $bread->icon }}"></i> {{ $bread->display_name_plural }}
        </h1>
        {{-- can('add', app($bread->model_name)) --}}
            <a href="{{ route('voyager.'.$bread->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        {{-- endcan-->
        {{-- @can('delete', app($bread->model_name)) --}}
            @include('bread::bread.partials.bulk-delete')
        {{-- @endcan --}}
    </div>
@stop

@section('content')
<div class="page-content browse container-fluid">
    @include('voyager::alerts')
    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-striped responsive" width="100%">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="rows_all"></th>
                                    @foreach($breadView->visible_rows as $row)
                                    <th>
                                    	{{ $row->options['label'] }}
                                    </th>
                                    @endforeach
                                    <th class="action text-right">{{ __('voyager::generic.actions') }}</th>
                                </tr>
                                <tr>
                                    <?php $i = 0; ?>
                                    <th></th>
                                    @foreach($breadView->visible_rows as $index => $row)
                                    <th>
                                    	@if ($row->is_searchable)
                                        <?php $i++; ?>
                                    	<input type="text" placeholder="Search {{ $row->options['label'] }}" class="form-control searchable" data-column="{{ $index }}">
                                    	@endif
                                    </th>
                                    @endforeach
                                    <th class="text-right">
                                        @if($i > 0)
                                        <button type="button" class="btn btn-xs btn-primary clear-filter">Clear Filter</button>
                                        @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script src="{{ asset('vendor/bread/js/jquery.debounce.min.js') }}"></script>
<script>
$(document).ready(function() {
    var datatable = $('#datatable').DataTable({
        'responsive': true,
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': true,
        'order': [[({{ $breadView->first_orderable_row }}+1), 'asc']],
        'info': true,
        'autoWidth': true,
        'processing': true,
        'serverSide': true,
        'pageLength': 10,
		'bSortCellsTop': true,
        'ajax': {
            url :  '{{ route("voyager.".$bread->slug.".data") }}',
            type : 'POST',
        },

        'columns': [
            { name: 'table_checkbox', searchable: false, sortable: false },
            {!! $breadView->getColumnDefinitions() !!},
            { name: 'table_actions', searchable: false, sortable: false },
        ],
        'language': {!! json_encode(__('voyager::datatable')) !!},
    });

	$('.searchable').on('keyup change', $.debounce(250, function(e) {
		var index = $(this).data('column') + 1; // +1 for Checkbox
		var col = datatable.column(index);
		if (col.search() !== $(this).val()) {
			col.search($(this).val()).draw();
		}
	}));

	$('.clear-filter').on('click', function(e) {
		e.preventDefault();
		$('.searchable').val('').trigger('change');
	});

    $('#datatable').on('change', 'input[name="rows_all"]', function(e) {
        $('input[name="row_id"]').prop('checked', $(this).prop('checked'));
    });
});
</script>
@stop
