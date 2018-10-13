@section('color')
<div>
    <div v-if="show == 'options'">
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
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.inline" value="true">Inline</label>
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
    </div>
    <div v-else>
        @{{ options.title }}
        <swatches :colors="colors" :inline="options.inline" v-model="color"></swatches>
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
        <input type="hidden" :name="name" :value="color">
    </div>
</div>
@endsection

<script>
Vue.component('formfield-color', {
    template: `@yield('color')`,
    props: ['show', 'options', 'type', 'name', 'input'],
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
    data: function() {
        return {
            'color': this.input,
        }
    },
});
</script>
