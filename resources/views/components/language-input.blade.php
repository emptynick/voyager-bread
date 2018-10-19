@section('language-input')
<div>
    <input v-model="translate"
           :type="this.type"
           class="form-control"
           :name="name+'_faker'"
           :placeholder="this.placeholder">
    <input type="hidden" :name="name" v-model="translationString">
</div>
@endsection

<script>
Vue.component('language-input', {
    template: `@yield('language-input')`,
    props: ['languages', 'input', 'name', 'type', 'placeholder', 'slug_from'],
    created: function() {
        this.setInitialTranslation(
            (this.input == null ? '' : this.input),
            '{{ app()->getLocale() }}',
            {!! json_encode(config('voyager.multilingual.locales')) !!},
            true
        );
    },
    watch: {
        translate: function (newVal, oldVal) {
            this.$bus.$emit(this.name+'_change', newVal, oldVal);
        }
    },
    mounted: function() {
        this.$bus.$on(this.slug_from+'_change', (newVal, oldVal) => {
            if (this.slug_from != '' && typeof oldVal === 'string') {
                this.translate = Vue.slugify(newVal);
            }
        });
    },
});
</script>
