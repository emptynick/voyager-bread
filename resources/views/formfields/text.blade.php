@section('formfield-text')
<div>
    <div v-if="show == 'options'">
        <div v-if="layoutType == 'view'">
            <div class="form-group">
                <label>Placeholder</label>
                <language-input v-model="options.placeholder" />
            </div>
            <div class="form-group">
                <label>Slug from</label>
                <select class="form-control" v-model="options.slug_from">
                    <option v-for="field in fields">
                        @{{ field }}
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group" v-if="layoutType == 'list'">
            <label>Display-Length</label>
            <input type="number" class="form-control" v-model="options.display_length" />
        </div>
    </div>
    <div v-else-if="show == 'mockup'">
        <input type="text" class="form-control" disabled :placeholder="translate(options.placeholder)">
    </div>
    <div v-else-if="show == 'edit' || show == 'add'">
        <label v-if="computed.title">@{{ computed.title }}</label>
        <div v-if="isTranslatable">
            <input
            class="form-control"
            v-for="l in locales"
            type="text"
            :placeholder="computed.placeholder"
            v-model="value[l]"
            v-on:input="$bus.$emit('input_change', name, $event.target.value)"
            :name="name+'['+l+']'"
            :style="(l != locale ? 'display:none' : '')" />
        </div>
        <input
        v-else
        type="text"
        class="form-control"
        :value="value"
        :name="name"
        v-on:input="$bus.$emit('input_change', name, $event.target.value)"
        :placeholder="computed.placeholder">
        <small v-if="computed.help_text">@{{ computed.help_text }}</small>
    </div>
    <div v-else-if="show == 'browse'">@{{ translate(input) ? translate(input).substring(0, options.display_length) : '' }}</div>
    <div v-else-if="show == 'read'">@{{ translate(input) }}</div>
</div>
@endsection

<script>
Vue.component('formfield-text', {
    template: `@yield('formfield-text')`,
    props: ['show', 'options', 'computed', 'name', 'layoutType', 'fields', 'input', 'isTranslatable'],
    data: function() {
        return {
            locales: {!! json_encode($locales) !!},
            locale: '{{ $locale }}',
            value: this.input,
        };
    },
    mounted: function() {
        this.$bus.$on('setLocale', (locale) => {
            this.locale = locale;
        });
        this.$bus.$on('input_change', (name, value) => {
            if (this.options.slug_from != '' && this.options.slug_from == name) {
                var slugged = this.slugify(value);
                if (this.isTranslatable) {
                    Vue.set(this.value, this.locale, slugged);
                } else {
                    this.value = slugged;
                }
            }
        });
    }
});
</script>
