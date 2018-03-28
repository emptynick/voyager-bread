@extends((!$compact ? 'voyager::master' : 'bread::bread.add-compact'))

@section('page_title', __('voyager::generic.'.(isset($breadContent) ? 'edit' : 'add')).' '.$bread->display_name_singular)

@section('page_header')
<h1 class="page-title">
    <i class="{{ $bread->icon }}"></i>
    {{ __('voyager::generic.'.(isset($breadContent) ? 'edit' : 'add')).' '.$bread->display_name_singular }}
</h1>
@include('voyager::multilingual.language-selector')
@stop

@section('content')
<div class="page-content edit-add container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <form role="form"
                @if (isset($breadContent))
                action="{{ route('voyager.'.$bread->slug.'.update', $breadContent->{$breadContent->getKeyName()}) }}"
                @else
                action="{{ route('voyager.'.$bread->slug.'.store') }}"
                @endif
                method="POST" enctype="multipart/form-data">
                @if (isset($breadContent))
                {{ method_field("PUT") }}
                @endif
                {{ csrf_field() }}
                <div class="panel-body">
                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <h4>{{ trans_choice('bread::generic.errors', count($errors)) }}</h4>
                        {{ trans_choice('bread::generic.error_message', count($errors)) }}:
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @foreach($breadView->rows as $row)
                    @if($row->field != 'design_element')
                    <div class="col-md-{{ $row->width or 12 }}">
                        <div class="panel panel-bordered panel-primary">
                            @if ($row->field == 'relationship')
                            <?php
                            $relationship = $bread->model->relationships[$row->options['relationship']];
                            $breadRelView = Bread::model('BreadView')->find($row->options['list_id']);
                            $type = get_unqualified_class($relationship);
                            ?>
                            <div class="panel-heading">
                                <h3 class="panel-title">{!! $row->options['label'] or '' !!}</h3>
                                <div class="panel-actions">
                                    @if (isset($row->options['view_id']) && $row->options['view_id'] != 'null')
                                    <a href="{{ route('voyager.'.$breadRelView->bread->slug.'.create', $row->options['view_id']) }}"
                                        data-bread="{{ $breadRelView->bread->display_name_singular }}"
                                        data-action="{{ route('voyager.'.$breadRelView->bread->slug.'.store', $row->options['view_id']) }}"
                                        data-remote="false" data-toggle="modal"
                                        data-rowid="{{ $row->id }}"
                                        data-target="#addViewModal" class="btn btn-success">
                                        Add {{ $row->getRelationshipBread()->display_name_singular }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                            <div class="panel-body">

                                <div class="form-group has-feedback {{ ($errors->has($row->field) ? 'has-error' : '') }}">

                                    @if ($row->field == 'relationship')
                                    <label><strong>{!! $row->options['helptext'] or '' !!}</strong></label>
                                    @include('bread::bread.partials.relationship', [
                                        'breadRow'     => $row,
                                        'multiple'     => ($type != 'BelongsTo' && $type != 'HasOne'),
                                        'name'         => $row->options['relationship'],
                                        'breadView'    => $breadRelView,
                                    ])
                                    @else
                                    <label><strong>{!! $row->options['label'] or '&nbsp;' !!}</strong></label>
                                    {!! $row->formfield->createInput(
                                        old($row->field, (isset($breadContent) ? $breadContent->{$row->field} : null)),
                                        $row->options, $row->field)
                                    !!}
                                    <small>{!! $row->options['helptext'] or '&nbsp;' !!}</small>
                                    @endif
                                    @if($errors->has($row->field))
                                    <small class="text-danger error-message">
                                        <ul>
                                            @foreach ($errors->get($row->field) as $message)
                                            <li>{{ $message }}</li>
                                            @endforeach
                                        </ul>
                                    </small>
                                    @endif
                                    <!-- Todo: Add AfterFormfields here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    {!! $row->formfield->createInput(
                        old($row->field, (isset($breadContent) ? $breadContent->{$row->field} : null)),
                        $row->options, $row->field)
                    !!}
                    @endif
                    @endforeach
                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="addViewModal" tabindex="-1" role="dialog" aria-labelledby="addViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addViewModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form method="post" action="" class="modal-form">
                    {{ __('bread::generic.please_wait') }}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary add-new-save">Save</button>
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
$modal = null;
var link = null;
$('#addViewModal').on('show.bs.modal', function(e) {
    $modal = $(this);
    link = $(e.relatedTarget);

    $modal.find('.modal-form').load(link.attr('href'));
    $modal.find('.modal-title').text('Add ' + link.attr('data-bread'));
});

$('#addViewModal').on('click', '.add-new-save', function(e) {
    e.preventDefault();

    var action = link.attr('data-action');
    var bread = link.attr('data-bread');
    var rowid = link.attr('data-rowid');

    $modal.find('.modal-form').load(link.attr('href'));
    $modal.find('.modal-title').text('Add '+bread);

    var jqxhr = $.post(action, $modal.find('.modal-form').serialize(), function(data) {

        $modal.find(':input').closest('.form-group').removeClass('has-error');
        $modal.find(':input').closest('.form-group').find('.error-message').text('');

        if ($.isNumeric(data)) {
            $table = $('#dt_'+rowid);
            $dt = $table.DataTable();
            $dt.ajax.reload(function() {
                /** @todo: this only works if the row is on the actual page. **/
                var multiple = $table.data('multiple');
                $row = $table.find('tr#'+data);
                rel_id = $table.attr('id');
                selectRow($row, multiple, rel_id);

            }, false);
        } else { /** @todo: Do something? **/ }

        $modal.modal('hide');
    })
    .fail(function(data) {
        $modal.find(':input').closest('.form-group').removeClass('has-error');
        $modal.find(':input').closest('.form-group').find('.error-message').text('');
        $.each(data.responseJSON.errors, function(field, msg) {
            $modal.find(':input[name="'+field+'"]').closest('.form-group').addClass('has-error');
            $modal.find(':input[name="'+field+'"]').closest('.form-group').find('.error-message').html('<br>'+msg);
        });
    });
});

$('document').ready(function () {
    $('.toggleswitch').bootstrapToggle();
    //Init datepicker for date fields if data-datepicker attribute defined
    //or if browser does not handle date inputs
    $('.form-group input[type=date]').each(function (idx, elt) {
        if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
            elt.type = 'text';
            $(elt).datetimepicker($(elt).data('datepicker'));
        }
    });

    $('input[data-slug-origin]').each(function(i, el) {
        $(el).slugify({
            forceupdate: true,
        });
    });
    $('[data-toggle="tooltip"]').tooltip();

    $('.relationship-table').each(function() {
        setInputNames($(this).find('thead'));
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

        $('#'+relationship_id).find('tbody').on('dblclick', 'tr', function(e) {
            selectRow($(this), multiple, relationship_id);
            setInputNames($(this).closest('table').find('thead'));
        });

        $('#'+relationship_id).find('thead').on('dblclick', 'tr', function(e) {
            $head = $(this).closest('thead');
            $(this).has('input[type="hidden"]').remove();
            setInputNames($head);
        });
    });

    $('.relationship-select2').each(function() {
        $(this).select2({
            ajax: {
                url: $(this).data('src'),
                data: function (params) {
                    var query = {
                        'search': 1,
                        'search_value': params.term,
                        'page': params.page || 1,
                        'length': 10,
                        'columns': { },
                    }
                    return query;
                },
                dataType: 'json',
                method: 'POST',
                delay: 250,
            },
            templateResult: function(state) {
                return state.text;
            }
        });
    });
});

function setInputNames($table_head)
{
    $table_head.find('tr:gt(1)').each(function(i, el) {
        $(this).find(':input').each(function() {
            var name = $(this).attr('name').replace(/(.*?)(\[\d+?\])(?!\[\d+?\])(.*)/g, function(match, p1, p2, p3, offset, string){
                p1 = p1.replace('dt_', '');
                return p1 + '[' + i + ']' + p3;
            });
            $(this).attr('name', name);
        });
    });
}

function selectRow($row, multiple, relationship_id)
{
    $table = $row.closest('table');
    $head = $table.find('thead');
    $body = $table.find('tbody');
    var id = $row.attr('id');

    if(!multiple) {
        $head.find('tr:gt(1)').remove();
    }

    if($head.find('tr[id="'+id+'"]').length == 0) {
        $head_row = $row.clone();
        $dt = $('#'+relationship_id).DataTable();
        $($dt.settings().init().columns).each(function(i, el) {
            if (el.name.match("^pivot")) {
                $head_row.find('td:eq('+i+')').html("{{ __('bread::generic.please_wait') }}");
                var placeholder = $($dt.column(i).header()).text().trim();

                var name = 'relationship['+relationship_id+'][0]['+el.name+']';

                var jqxhr = $.post("{{ route('voyager.bread.render.formfield') }}", {
                    type: 'input',
                    field: el.name,
                    name: name,
                    options: {
                        placeholder: placeholder
                    }
                }, function(data) {
                    $head_row.find('td:eq('+i+')').html(data);
                    setInputNames($head);
                    $(document).trigger('formFieldLoaded', [$head_row.find('td:eq('+i+')')]);
                });
            }
        });

        $head_row.append('<input type="hidden" name="relationship['+relationship_id+'][0][id]" value="'+id+'">');

        $head.append($head_row);
    }
}
</script>
@append
@section('css')
<style type="text/css">
thead tr td {
    background-color: #f0fced;
}
</style>
@endsection
