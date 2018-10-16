@section('textarea')
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
            <label>Rows</label>
            <input type="number" min="1" class="form-control" v-model="options.rows">
        </div>
    </div>
    <div v-else>
        @{{ options.title }}
        <textarea class="form-control" :disabled="show == 'mockup'"
               :placeholder="options.placeholder"
               :rows="options.rows"
               :name="name+'_faker'"
               v-model="translate"></textarea>
        <input type="hidden" :name="name" v-model="translationString">
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-textarea', {
    template: `@yield('textarea')`,
    props: ['show', 'options', 'type', 'name', 'input'],
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
