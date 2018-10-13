@section('maskedinput')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'list'">
            <label>Display length</label>
            <input type="number" min="1" class="form-control" v-model="options.length">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Default value</label>
            <input type="text" class="form-control" v-model="options.default_value">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder</label>
            <input type="text" class="form-control" v-model="options.placeholder">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Mask</label>
            <input type="text" class="form-control" v-model="options.mask">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder character</label>
            <input type="text" class="form-control" v-model="options.mask_char">
        </div>
        <ul v-if="type == 'view'">
            <li>1 – number</li>
            <li>a – letter</li>
            <li>A – letter, forced to upper case when entered</li>
            <li>* – alphanumeric</li>
            <li># – alphanumeric, forced to upper case when entered</li>
            <li>+ – any character</li>
            <li>\ – escape any of the above characters</li>
        </ul>
    </div>
    <div v-else-if="show == 'read'">
        @{{ options.title }}
        <br>
        @{{ value }}
    </div>
    <div v-else-if="show == 'relationship'">
        @{{ translate }}
    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ options.title }}</label>
        <masked-input v-model="value"
            :mask="options.mask"
            :placeholder="options.placeholder"
            :placeholderChar="options.mask_char"
            class="form-control" />
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-maskedinput', {
    template: `@yield('maskedinput')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input'],
    data: function() {
        return {
            'value': (this.input == null ? this.options.default_value : this.input),
        }
    },
});
</script>
