<div class="form-group">
    <label>Tabs</label>
    <table class="table table-responsive repeater">
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse((isset($options['tabs']) ? $options['tabs'] : []) as $tab)
            <tr class="repeater-item">
                <td>
                    <input type="text" class="form-control" name="row[][options][tabs][{{ $loop->index }}][name]" value="{{ $tab['name'] or '' }}">
                </td>
                <td class="btn-group inline">
                    <button class="btn btn-sm btn-success repeater-add"><i class="voyager-plus"></i></button>
                    <button class="btn btn-sm btn-danger repeater-delete"><i class="voyager-x"></i></button>
                </td>
            </tr>
            @empty
            <tr class="repeater-item">
                <td>
                    <input type="text" class="form-control" name="row[][options][tabs][][name]">
                </td>
                <td class="btn-group inline">
                    <button class="btn btn-sm btn-success repeater-add"><i class="voyager-plus"></i></button>
                    <button class="btn btn-sm btn-danger repeater-delete"><i class="voyager-x"></i></button>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<hr>
