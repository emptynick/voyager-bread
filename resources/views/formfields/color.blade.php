@section('color')
<div>
    <div v-if="show == 'options'">
        <div class="form-group">
            <label>Palette</label>
            <select class="form-control" v-model="options.palette">
                <option value="basic">Basic</option>
                <option value="text-basic">Text Basic</option>
                <option value="text-advanced">Text Advanced</option>
                <option value="material-basic">Material Basic</option>
                <option value="material-light">Material Light</option>
                <option value="material-dark">Material Dark</option>
            </select>
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.inline" value="true">Inline</label>
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
    </div>
    <div v-else>
        @{{ translated(options.title) }}
        <swatches :colors="colors" :inline="options.inline" v-model="color"></swatches>
        <input type="hidden" :name="name" v-model="this.color">
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text, locale) }}</small>
        <input type="hidden" :name="name" :value="color">
    </div>
</div>
@endsection

<script>
Vue.component('formfield-color', {
    template: `@yield('color')`,
    props: ['show', 'options', 'type', 'name', 'input', 'locale'],
    computed: {
        colors: function() {
            return this.options.palette;
        }
    },
    data: function() {
        return {
            'color': this.input,
        }
    },
});
</script>
