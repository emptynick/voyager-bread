@section('language-output')
<component :is="type">
    @{{ content }}
</component>
@endsection

<script>
Vue.component('language-output', {
    props: ['input', 'once', 'prefill', 'type'],
    template: `@yield('language-output')`,
    data: function() {
        return {
            locale: '{{ $locale }}',
            value: this.input,
        };
    },
    computed: {
        content: function() {
            return this.value[this.locale];
        }
    },
    mounted: function() {
        this.$bus.$on('setLocale', (locale) => {
            if (!this.once) {
                this.locale = locale;
            }
        });

        if (this.value === null || this.value == '') {
            this.value = {};
        } else if (typeof this.value !== 'object') {
            let pivot = this.value;
            try {
                this.value = JSON.parse(pivot);
            } catch {
                this.value = {};
                Vue.set(this.value, this.locale, pivot);
            }
        }
    }
});
</script>
