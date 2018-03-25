@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.$bread->display_name_singular)

@section('page_header')
<h1 class="page-title">
    <i class="{{ $bread->icon }}"></i>
    {{ __('voyager::generic.view').' '.$bread->display_name_singular }}
</h1>
@include('voyager::multilingual.language-selector')
@stop

@section('content')
<div class="page-content edit-add container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    @foreach($breadView->rows as $row)
                    @if($row->field != 'design_element')
                    <div class="col-md-{{ $row->width or 12 }}">
                        <div class="panel panel-bordered">
                            <div class="panel-body">
                                <label><strong>{!! $row->options['label'] or '' !!}</strong></label>
                                @if ($row->field == 'relationship')
                                <?php
                                $relationship = $bread->model->relationships[$row->options['relationship']];
                                $breadRelView = Bread::model('BreadView')->find($row->options['list_id']);
                                $type = get_unqualified_class($relationship);
                                ?>
                                <br><small>{!! $row->options['helptext'] or '&nbsp;' !!}</small>
                                @include('bread::bread.partials.relationship', [
                                    'breadRow'     => $row,
                                    'multiple'     => ($type != 'BelongsTo' && $type != 'HasOne'),
                                    'name'         => $row->options['relationship'],
                                    'breadView'    => $breadRelView,
                                ])
                                @else
                                {!! $row->formfield->createOutput($breadContent->{$row->field}, $row->options, $row->field) !!}
                                <small>{!! $row->options['helptext'] or '&nbsp;' !!}</small>
                                @endif
                                <!-- Todo: Add AfterFormfields here -->
                            </div>
                        </div>
                    </div>
                    @else
                    {!! $row->formfield->createOutput($breadContent->{$row->field}, $row->options, $row->field) !!}
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script src="{{ asset('vendor/bread/js/jquery.debounce.min.js') }}"></script>
<script>
$('document').ready(function () {
    $('.relationship-table').each(function() {
        var relationship_id = $(this).attr('id');
        var multiple = $(this).data('multiple');
        $('#'+relationship_id).DataTable({
            'responsive': true,
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': true,
            'processing': true,
            'serverSide': true,
            'pageLength': 10,
            'bSortCellsTop': true,
            'lengthChange': false,
            'ajax': {
                url :  $(this).data('src'),
                type : 'POST',
            },
            'language': {!! json_encode(__('voyager::datatable')) !!},
        });
        $('#'+relationship_id).find('.searchable').on('keyup change', $.debounce(250, function(e) {
    		var index = $(this).data('column');
    		var col = $(this).closest('table').DataTable().column(index);
    		if (col.search() !== $(this).val()) {
    			col.search($(this).val()).draw();
    		}
    	}));
    });
});
</script>
@append
