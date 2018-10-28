@section('text')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'list'">
            <label>Display length</label>
            <input type="number" min="1" class="form-control" v-model="options.length">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder</label>
            <language-input type="text" v-model="options.placeholder" :input="options.placeholder" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
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
        <div v-if="options.title.length > 0">
            <strong>@{{ translated(options.title, locale) }}</strong>
            <br>
        </div>
        @{{ translate }}
    </div>
    <div v-else-if="show == 'relationship' || show == 'browse'">
        @{{ translate.substring(0, (options.length ? options.length : translate.length)) }}
    </div>
    <div v-else>
        <label v-if="options.title.length > 0">
            <span>@{{ translated(options.title, locale) }}</span>
        </label>
        <input type="text" class="form-control" :disabled="show == 'mockup'"
               :placeholder="translated(options.placeholder, (show == 'mockup' ? null : locale))"
               :name="name+'_faker'"
               v-model="translate">
        <input type="hidden" :name="name" v-model="translationString">
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text, locale) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-text', {
    template: `@yield('text')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input', 'locale'],
    created: function() {
        this.updateTranslation(this.input);
    },
    watch: {
        translate: function (newVal, oldVal) {
            this.$bus.$emit(this.name+'_change', newVal, oldVal);
        },
        input: function (newVal, oldVal) {
            //Todo: this is old, and was replaced
            //because removing an item from a repeater sets the text to the translation-string
            //this.translate = newVal;
            this.updateTranslation(newVal);
        },
    },
    mounted: function() {
        this.$bus.$on(this.options.slug_from+'_change', (newVal, oldVal) => {
            if (this.options.slug_from != '' && typeof oldVal === 'string') {
                this.translate = Vue.slugify(newVal);
            }
        });
    },
    methods: {
        updateTranslation: function(input) {
            this.setInitialTranslation(
                input,
                '{{ app()->getLocale() }}',
                {!! json_encode(config('voyager.multilingual.locales')) !!},
                this.options.isTranslatable
            );
        }
    }
});
</script>
