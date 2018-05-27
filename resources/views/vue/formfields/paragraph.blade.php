@section('paragraph')
<div>
    <div>
        <slot name="content"></slot>
        <p
            v-bind:style="{ textAlign: options.align, color: options.color, fontSize: options.size+'px' }"
            v-html="nl2br(options.text)"></p>
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <div class="form-group">
            <label>Text</label>
            <textarea class="form-control" rows="5" v-model="options.text" v-on:change="updateItemsHeight()"></textarea>
        </div>
        <div class="form-group">
            <label>Size</label>
            <input type="number" min="5" max="20" class="form-control" v-model="options.size" v-on:change="updateItemsHeight()">
        </div>
        <div class="form-group">
            <label>Align</label>
            <select class="form-control" v-model="options.align">
                <option value="">Left</option>
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
Vue.component('paragraph', {
    template: `@yield('paragraph')`,
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
        nl2br: function(str) {
            if (typeof str === 'undefined' || str === null) {
                return '';
            }
            return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
        },
        updateItemsHeight: function() {
            this.$bus.$emit('updateItemsHeight');
        }
    }
});
</script>
