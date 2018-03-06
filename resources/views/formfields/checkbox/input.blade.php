@if(isset($content))
    <?php $checked = boolval($content); ?>
@else
<?php $checked = isset($options['checked']) && $options['checked'] ? true : false; ?>
@endif
@if(isset($options->on) && isset($options->off))
<input type="checkbox" name="{{ $name }}" class="toggleswitch"
           data-on="{{ $options['on'] }}" {{ $checked ? 'checked' : '' }}
           data-off="{{ $options['off'] }}">
@else
    <input type="checkbox" name="{{ $name }}" class="toggleswitch" data-name="{{ $options['display_name'] or '' }}"
           @if($checked) checked @endif>
@endif
