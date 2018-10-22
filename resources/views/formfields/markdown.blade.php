@section('markdown')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'list'">
            <label>Display length</label>
            <input type="number" min="1" class="form-control" v-model="options.length">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.spellcheck" value="true">Spellcheck</label>
        </div>
    </div>
    <div v-else-if="show == 'read'">
        @{{ translated(options.title) }}
        <br>
        @{{ translate }}
    </div>
    <div v-else-if="show == 'relationship'">
        @{{ translate }}
    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ translated(options.title) }}</label>
        <markdown-editor v-model="translate" :configs="config" disabled="true" ref="mdeditor"></markdown-editor>
        <input type="hidden" :name="name" v-model="translationString">
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-markdown', {
    template: `@yield('markdown')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input'],
    created: function() {
        this.setInitialTranslation(
            this.input,
            '{{ app()->getLocale() }}',
            {!! json_encode(config('voyager.multilingual.locales')) !!},
            this.options.isTranslatable
        );
    },
    computed: {
        config: function() {
            return {
                spellChecker: this.options.spellcheck,
            };
        },
    }
});
</script>
