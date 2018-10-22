@section('maskedinput')
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
            <label>Mask</label>
            <language-input type="text" v-model="options.mask" :input="options.mask" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder character</label>
            <language-input type="text" v-model="options.placeholder" :input="options.mask_char" />
        </div>
        <ul v-if="type == 'view'">
            <li>1 – number</li>
            <li>a – letter</li>
            <li>A – letter, forced to upper case when entered</li>
            <li>* – alphanumeric</li>
            <li># – alphanumeric, forced to upper case when entered</li>
            <li>+ – any character</li>
            <li>\ – escape any of the above characters</li>
        </ul>
    </div>
    <div v-else-if="show == 'read'">
        @{{ translated(options.title) }}
        <br>
        @{{ value }}
    </div>
    <div v-else-if="show == 'relationship'">
        @{{ translate }}
    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ translated(options.title) }}</label>
        <masked-input
            :mask="options.mask"
            :placeholder="translated(options.placeholder)"
            :placeholderChar="translated(options.mask_char)"
            v-model="translate"
            class="form-control" />
        <input type="hidden" :name="name" v-model="translationString">
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-maskedinput', {
    template: `@yield('maskedinput')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input'],
    created: function() {
        this.setInitialTranslation(
            (this.input == null ? this.options.default_value : this.input),
            '{{ app()->getLocale() }}',
            {!! json_encode(config('voyager.multilingual.locales')) !!},
            this.options.isTranslatable
        );
    },
    watch: {
        translate: function (newVal, oldVal) {
            this.$bus.$emit(this.name+'_change', newVal, oldVal);
        }
    },
});
</script>
