<div class="form-group">
	<label>Multiple</label>
	<input type="checkbox" {{ ((isset($options['multiple']) && boolval($options['slug'])) ? 'checked' : '') }} class="form-check-input"
    name="row[][options][multiple]" value="true">
</div>

<div class="form-group">
    <label>Options</label>
    <table class="table table-responsive repeater">
        <thead>
            <tr>
                <th>Key</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse((isset($options['options']) ? $options['options'] : []) as $opt)
            <tr class="repeater-item">
                <td>
                    <input type="text" class="form-control" name="row[][options][options][{{ $loop->index }}][key]" value="{{ $opt['key'] }}">
                </td>
                <td>
                    <input type="text" class="form-control" name="row[][options][options][{{ $loop->index }}][value]" value="{{ $opt['value'] }}">
                </td>
                <td class="btn-group inline">
                    <input type="checkbox"
                        value="true"
                        name="row[][options][options][{{ $loop->index }}][default]"
                        {{ (isset($opt['default']) && boolval($opt['default']) ? 'checked' : '') }}>
                    <button class="btn btn-sm btn-success repeater-add"><i class="voyager-plus"></i></button>
                    <button class="btn btn-sm btn-danger repeater-delete"><i class="voyager-x"></i></button>
                </td>
            </tr>
            @empty
            <tr class="repeater-item">
                <td>
                    <input type="text" class="form-control" name="row[][options][options][][key]">
                </td>
                <td>
                    <input type="text" class="form-control" name="row[][options][options][][value]">
                </td>
                <td class="btn-group inline">
                    <input type="checkbox" value="true" name="row[][options][options][][default]">
                    <button class="btn btn-sm btn-success repeater-add"><i class="voyager-plus"></i></button>
                    <button class="btn btn-sm btn-danger repeater-delete"><i class="voyager-x"></i></button>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<hr>
