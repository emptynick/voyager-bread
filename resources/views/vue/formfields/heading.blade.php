@section('heading')
<div>
    <div>
        <slot name="content"></slot>
        <component :is="options.size" v-bind:style="{ textAlign: options.align, color: options.color }">@{{ text }}</component>
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <div class="form-group">
            <label>Text</label>
            <input type="text" class="form-control" v-model="options.text" v-on:change="updateItemsHeight()">
        </div>
        <div class="form-group">
            <label>Size</label>
            <select class="form-control" v-model="options.size" v-on:change="updateItemsHeight()">
                <option value="h1">H1</option>
                <option value="h2">H2</option>
                <option value="h3">H3</option>
                <option value="h4">H4</option>
                <option value="h5">H5</option>
                <option value="h6">H6</option>
            </select>
        </div>
        <div class="form-group">
            <label>Align</label>
            <select class="form-control" v-model="options.align">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
            </select>
        </div>
        <div class="form-group">
            <label>Color</label>
            <vue-swatches v-model="options.color" :colors="{!! config_get_colors() !!}" inline />
        </div>
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('heading', {
    template: `@yield('heading')`,
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
            text: '',
            translatable: ['text'],
        }
    },
    methods: {
        updateItemsHeight: function() {
            this.$bus.$emit('updateItemsHeight');
        }
    }
});
</script>
