@section('language-input')
<div>
    <textarea v-model="translate"
           class="form-control"
           v-on:input="$emit('input', translationString)"
           :placeholder="this.placeholder"
           v-if="type == 'textarea'">
    </textarea>
    <input v-model="translate"
           :type="this.type"
           class="form-control"
           v-on:input="$emit('input', translationString)"
           :placeholder="this.placeholder"
           v-else>
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
        },
    },
    mounted: function() {
        this.$bus.$on(this.slug_from+'_change', (newVal, oldVal) => {
            if (this.slug_from != '' && this.name !== undefined && typeof oldVal === 'string') {
                this.translate = Vue.slugify(newVal);
            }
        });
    },
});
</script>
