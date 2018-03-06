<input {{ (isset($options['required']) && $options['required'] == 1 ? 'required' : '') }}
       type="{{ $options['type'] or 'text' }}"
       data-name="{{ $options['display_name'] or '' }}"
       class="form-control"
       name="{{ $name }}"
       @if(isset($options['slug']) && $options['slug'] == 'true')
       data-slug-origin="{{ $options['slug_origin'] or '' }}"
       @endif
       placeholder="{{ $options['placeholder'] or '' }}"
       value="{{ $content or (isset($options['value']) ? $options['value'] : '') }}">
