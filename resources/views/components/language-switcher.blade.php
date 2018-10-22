@section('language-switcher')
<div>
    <div class="language-selector" v-if="this.languages.length > 1">
        <div class="btn-group btn-group-sm" role="group" data-toggle="buttons">
            <label v-for="locale in languages" @click="switchLanguage(locale)" :class="'btn btn-primary '+(isLocale(locale) ? 'active' : '')">
                <input type="radio"> @{{ locale.toUpperCase() }}
            </label>
        </div>
    </div>
</div>
@endsection

<script>
Vue.component('language-switcher', {
    template: `@yield('language-switcher')`,
    props: ['languages'],
    data: function() {
        return {
            locale: '{{ app()->getLocale() }}',
        };
    },
    methods: {
        switchLanguage: function(locale) {
            this.$bus.$emit('setLocale', locale);
        },
        isLocale: function(locale) {
            return locale == this.locale;
        }
    },
    mounted: function() {
        this.$bus.$on('setLocale', (locale) => {
            this.locale = locale;
        });
        this.$bus.$emit('setLocale', this.locale);
    }
});
</script>
