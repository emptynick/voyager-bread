@section('text')
<div>
    <div v-if="this.$type != 'list'">
        <slot name="content"></slot>
        <label v-if="options.label">@{{ options.label }}</label>
        <input type="text" class="form-control" v-model="options.default_text" :placeholder="options.placeholder">
        <small v-if="options.help_text">@{{ options.help_text }}</small>
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <div class="form-group" v-if="this.$type != 'list'">
            <label>Label</label>
            <input type="text" class="form-control" v-model="options.label">
        </div>
        <div class="form-group" v-if="this.$type != 'list'">
            <label>Placeholder</label>
            <input type="text" class="form-control" v-model="options.placeholder">
        </div>
        <div class="form-group" v-if="this.$type != 'list'">
            <label>Help Text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
        <div class="form-group">
            <label>Default Text</label>
            <input type="text" class="form-control" v-model="options.default_text">
        </div>
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-text', {
    template: `@yield('text')`,
    mixins: [ Translatable ],
    props: {
        options: {
            required: true
        },
        i: {
            required: true
        }
    },
    data: function() {
        return {
            translatable: ['title'],
        }
    },
});
</script>
