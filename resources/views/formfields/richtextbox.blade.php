@section('richtextbox')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'list'">
            <label>Display length</label>
            <input type="number" min="1" class="form-control" v-model="options.length">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Default value</label>
            <input type="text" class="form-control" v-model="options.default_value">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder</label>
            <input type="text" class="form-control" v-model="options.placeholder">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Slug</label>
            <select class="form-control" v-model="options.slug_from">
                <option value="">None</option>
                <option v-for="field in fields">
                    @{{ field }}
                </option>
            </select>
        </div>
    </div>
    <div v-else-if="show == 'read'">
        @{{ options.title }}
        <br>
        @{{ value }}
    </div>
    <div v-else-if="show == 'relationship'">
        @{{ translate }}
    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ options.title }}</label>
        <quill-editor v-model="translate" :options="this.config" />
        <input type="hidden" :name="name" v-model="translationString">
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-richtextbox', {
    template: `@yield('richtextbox')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input'],
    created: function() {
        this.setInitialTranslation(
            (this.input == null ? this.options.default_value : this.input),
            '{{ app()->getLocale() }}',
            {!! json_encode(config('voyager.multilingual.locales')) !!},
            this.options.isTranslatable
        );
    },
    data () {
        return {
            config: {
                placeholder: this.options.placeholder,
                readOnly: (this.show == 'mockup'),
            }
        }
    }
});
</script>
