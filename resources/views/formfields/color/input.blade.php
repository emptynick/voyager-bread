<input {{ (isset($options['required']) && $options['required'] == 1 ? 'required' : '') }}
       type="color"
       data-name="{{ $options['display_name'] or '' }}"
       class="form-control"
       name="{{ $name }}"
       placeholder="{{ $options['placeholder'] or '' }}"
       value="{{ $content or (isset($options['value']) ? $options['value'] : '') }}">
