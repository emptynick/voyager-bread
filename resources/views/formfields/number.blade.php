@section('number')
<div>
    <div v-if="show == 'options'">
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
        <div class="form-group col-md-6">
            <label>Prefix</label>
            <language-input type="text" v-model="options.prefix" :input="options.prefix" />
        </div>
        <div class="form-group col-md-6">
            <label>Suffix</label>
            <language-input type="text" v-model="options.suffix" :input="options.suffix" />
        </div>
        <div class="form-group col-md-12">
            <label>Decimals</label>
            <input type="number" min="0" class="form-control" v-model="options.decimals">
        </div>
        <div class="form-group col-md-4" v-if="type == 'view'">
            <label>Min</label>
            <input type="number" min="0" class="form-control" v-model="options.min">
        </div>
        <div class="form-group col-md-4" v-if="type == 'view'">
            <label>Max</label>
            <input type="number" class="form-control" v-model="options.max">
        </div>
        <div class="form-group col-md-4" v-if="type == 'view'">
            <label>Step</label>
            <input type="number" class="form-control" v-model="options.step">
        </div>
    </div>
    <div v-else-if="show == 'read'">
        <div v-if="options.title.length > 0">
            <strong>@{{ translated(options.title, locale) }}</strong>
            <br>
        </div>
        @{{ formatedValue }}
    </div>
    <div v-else-if="show == 'relationship' || show == 'browse'">
        @{{ formatedValue }}
    </div>
    <div v-else>
        @{{ translated(options.title, locale) }}
        <input type="number" class="form-control" :disabled="show == 'mockup'"
               :min="options.min"
               :max="options.max"
               :step="options.step"
               :placeholder="translated(options.placeholder, locale)"
               :name="name+'_faker'"
               v-model="translate">
        <input type="hidden" :name="name" v-model="translationString">
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text, locale) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-number', {
    template: `@yield('number')`,
    props: ['show', 'options', 'type', 'name', 'input', 'locale'],
    created: function() {
        this.setInitialTranslation(
            (this.input == null ? this.options.default_value : this.input),
            '{{ app()->getLocale() }}',
            {!! json_encode(config('voyager.multilingual.locales')) !!},
            this.options.isTranslatable
        );
    },
    computed: {
        formatedValue: function() {
            return translated(this.options.prefix, locale) + this.translate.toFixed(this.options.decimals) + translated(this.options.suffix, locale);
        },
    },
    watch: {
        translate: function (newVal, oldVal) {
            this.$bus.$emit(this.name+'_change', newVal, oldVal);
        }
    }
});
</script>
