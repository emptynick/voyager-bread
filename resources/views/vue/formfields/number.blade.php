@section('number')
<div>
    <div>
        <slot name="content"></slot>
        <label v-if="options.label">@{{ options.label }}</label>
        <input
            type="number"
            class="form-control"
            v-model="options.default_value"
            :min="options.min"
            :max="options.max"
            :step="options.step">
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
            <input type="number"
                :min="options.min"
                :max="options.max"
                :step="options.step"
                class="form-control"
                v-model="options.default_value">
        </div>
        <div class="form-group">
            <label>Minimum</label>
            <input type="number" :max="options.max" :step="options.step" class="form-control" v-model="options.min">
        </div>
        <div class="form-group">
            <label>Maximum</label>
            <input type="number" :min="options.min" :step="options.step" class="form-control" v-model="options.max">
        </div>
        <div class="form-group">
            <label>Step</label>
            <input type="number" class="form-control" v-model="options.step">
        </div>
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-number', {
    template: `@yield('number')`,
    props: {
        options: {
            required: true
        },
        i: {
            required: true
        }
    },
});
</script>
