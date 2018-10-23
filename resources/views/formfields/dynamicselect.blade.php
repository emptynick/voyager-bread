@section('dynamicselect')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.multiple" value="true">Multiple</label>
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Method</label>
            <input type="text" class="form-control" v-model="options.method">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Fields</label>
            <select class="form-control" v-model="options.fields" multiple>
                <option v-for="field in fields">
                    @{{ field }}
                </option>
            </select>
        </div>
    </div>
    <div v-else-if="show == 'read'">

    </div>
    <div v-else-if="show == 'relationship'">

    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ translated(options.title, locale) }}</label>

        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text, locale) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-dynamicselect', {
    template: `@yield('dynamicselect')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input', 'field', 'locale'],
    mounted: function() {
        this.options.fields.each(function(field) {
            this.$bus.$on(field + '_change', (newVal, oldVal) => {
                //Todo: Stitch together array and call method by AJAX
            });
        });
    },
});
</script>
