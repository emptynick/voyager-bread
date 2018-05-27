@section('colorpicker')
<div>
    <div v-if="this.$type != 'list'">
        <slot name="content"></slot>
        <vue-swatches v-model="options.color" :colors="colors" inline />
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <div class="form-group">
            <label>Palette</label>
            <select class="form-control" v-model="options.palette">
                <option value="basic">Basic</option>
                <option value="text-basic">Text Basic</option>
                <option value="text-advances">Text Advanced</option>
                <option value="material-basic">Material Basic</option>
                <option value="material-light">Material Light</option>
                <option value="material-dark">Material Dark</option>
                <option value="">Custom colors</option>
            </select>
        </div>
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('colorpicker', {
    template: `@yield('colorpicker')`,
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
            translatable: [ 'title' ],
        }
    },
    computed: {
        colors: function() {
            if (this.options.palette == "") {
                if (this.options.custom_colors.length == 0) {
                    return "basic";
                }

                //Todo: Return hex-colors as an array
            }

            return this.options.palette;
        }
    },
    methods: {
        updateItemsHeight: function() {
            this.$bus.$emit('updateItemsHeight');
        }
    }
});
</script>
