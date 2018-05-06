@section('formfield-textarea')
<div>
    <div>
        <slot name="content"></slot>
        <label v-if="options.label">@{{ options.label }}</label>
        <textarea :value="options.value" :placeholder="options.placeholder" class="form-control" :disabled="true" :rows="options.rows"></textarea>
        <small v-if="options.help_text">@{{ options.help_text }}</small>
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <div class="form-group">
            <label>Label</label>
            <input type="text" class="form-control" v-model="options.label">
        </div>
        <div class="form-group">
            <label>Placeholder</label>
            <input type="text" class="form-control" v-model="options.placeholder">
        </div>
        <div class="form-group">
            <label>Help Text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
        <div class="form-group">
            <label>Default Value</label>
            <textarea class="form-control" v-model="options.value"></textarea>
        </div>
        <div class="form-group">
            <label>Rows</label>
            <input type="number" min="1" max="100" class="form-control" v-model="options.rows">
        </div>
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-textarea', {
    template: `@yield('formfield-textarea')`,
    props: ['field', 'options', 'mockup', 'read', 'edit', 'add', 'i'],
});
</script>
