@if(count($breadView->visible_rows) == 1)
<select class="form-control relationship-select2"
        name="relationship[{{ $row->id }}][0][id][]"
        data-src="{{ route('voyager.'.$breadView->bread->slug.'.data', [$breadView, $breadRow->id]) }}?select2=1"
        data-order="[[{{ $breadView->first_orderable_row }}, &quot;asc&quot;]]"
        {{ ((isset($multiple) && $multiple) ? 'multiple' : '') }}
>

</select>
@else
<div class="table-responsive"><input type="hidden" name="relationship[{{ $breadRow->id }}][0][force]" value="null">
    <table id="dt_{{ $breadRow->id }}" class="table table-hover responsive relationship-table"
        data-columns="{!! htmlspecialchars($breadView->getColumnDefinitions(true)) !!}"
        data-src="{{ route('voyager.'.$breadView->bread->slug.'.data', [$breadView, $breadRow->id]) }}"
        data-order="[[{{ $breadView->first_orderable_row }}, &quot;asc&quot;]]"
        data-multiple="{{ $multiple }}"
        width="100%">
        <thead>
            <tr>
                @foreach($breadView->visible_rows as $row)
                <th>
                    {{ $row->options['label'] }}
                </th>
                @endforeach
            </tr>
            <tr>
                @foreach($breadView->visible_rows as $index => $row)
                <th>
                    @if ($row->is_searchable)
                    <input type="text" placeholder="Search {{ $row->options['label'] }}" class="form-control searchable" data-column="{{ $index }}">
                    @endif
                </th>
                @endforeach
            </tr>
            @if(isset($breadContent))
            @foreach ($breadContent->getRelationshipContent($relationship, $breadRow->options['relationship'], $breadView->visible_rows) as $content)
            <tr id="{{ $content->{$content->getKeyName()} }}">
                @foreach($breadView->visible_rows as $row)
                <td>
                    <?php $fields = parse_field_name($row->field); ?>
                    @if ($fields['type'] == 'pivot')
                    {!! $row->formfield->createInput($content->pivot->{$fields['attribute']}, $row->options, 'relationship['.$breadRow->id.'][0]['.$row->field.']') !!}
                    @elseif ($fields['type'] == 'relationship')
                        @if ($content->{$fields['relationship']} instanceof \Illuminate\Support\Collection)
                        {!! $row->formfield->createOutput($content->{$fields['relationship']}, true, $fields['attribute']) !!}
                        @elseif(isset($content->{$fields['relationship']}->{$fields['attribute']}))
                        {!! $row->formfield->createOutput($content->{$fields['relationship']}->{$fields['attribute']}) !!}
                        @endif
                    @else
                    {!! $row->formfield->createOutput($content->{$row->field}) !!}
                    @endif

                    @if($loop->last)
                    <input type="hidden" name="relationship[{{ $breadRow->id }}][0][id]" value="{{ $content->{$content->getKeyName()} }}">
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
            @endif
        </thead>
        <tbody></tbody>
    </table>
</div>
@endif
