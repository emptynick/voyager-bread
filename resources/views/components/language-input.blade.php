@section('language-input')
<div>
    <input type="text" class="form-control" v-bind:value="currentTranslated" v-on:input="input($event.target.value)">
</div>
@endsection

<script>
Vue.component('language-input', {
    template: `@yield('language-input')`,
    props: ['value', 'name', 'slug', 'prefill'],
    data: function() {
        return {
            translated: this.value,
            locales: {!! json_encode($locales) !!},
            locale: '{{ $locale }}',
        };
    },
    methods: {
        input: function(input) {
            if (this.locales.length > 0) {
                this.translated[this.locale] = input;
            } else {
                this.translated = input;
            }
            this.$emit('input', this.translated);
            if (this.name) {
                this.$bus.$emit(this.name+'_change', input);
            }
        }
    },
    computed: {
        currentTranslated: function() {
            if (this.translated && this.locales.length > 0) {
                return this.translated[this.locale];
            }

            return this.translated;
        }
    },
    mounted: function() {
        if (this.locales.length > 0 && typeof this.translated !== 'object') {
            this.translated = {};
            for (i in this.locales) {
                if (this.prefill || this.locales[i] == this.locale) {
                    Vue.set(this.translated, this.locales[i], this.value);
                } else {
                    Vue.set(this.translated, this.locales[i], '');
                }
            }
        }

        this.$bus.$on(this.slug+'_change', (input) => {
            if (this.slug != '' && typeof input === 'string') {
                //Todo: slugify
                this.input(input);
            }
        });
        this.$bus.$on('setLocale', (locale) => {
            this.locale = locale;
        });
    }
});
</script>
