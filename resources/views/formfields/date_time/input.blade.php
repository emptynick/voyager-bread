<input
       type="{{ $options['type'] or 'datetime' }}"
       data-name="{{ $options['display_name'] or '' }}"
       class="form-control"
       name="{{ $name }}"
       value="{{ $content or '' }}">
