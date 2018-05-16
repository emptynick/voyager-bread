@section('heading')
<div>
    <div>
        <slot name="content"></slot>
        <component :is="options.size">@{{ text }}</component>
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <div class="form-group">
            <label>Text</label>
            <input type="text" class="form-control" v-model="options.text" v-on:keyup="requestTranslation()">
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
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('heading', {
    template: `@yield('heading')`,
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
            translation: ''
        }
    },
    computed: {
        text: function()
        {
            if (this.translation != '')
                return this.translation;
            return this.options.text;
        }
    },
    methods: {
        requestTranslation: _.debounce(function (e) {
            if (this.options.text != '')
                this.$bus.$emit('translationRequested', this.options.text);
            else
                this.translation = '';
        }, 500),
        updateItemsHeight: function() {
            this.$bus.$emit('updateItemsHeight');
        }
    },
    mounted: function() {
        this.$bus.$on('translationReceived', (key, data) => {
            if (data != "" && key == this.options.text && data != key)
                this.translation = data;
        });
        this.requestTranslation();
    },
});
</script>
