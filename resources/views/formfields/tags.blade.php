@section('tags')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder</label>
            <language-input type="text" v-model="options.placeholder" :input="options.placeholder" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Delimiter</label>
            <input type="text" class="form-control" v-model="options.delimiter">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
    </div>
    <div v-else-if="show == 'read'">
        @{{ translated(options.title, locale) }}
        <br>
        @{{ translate }}
    </div>
    <div v-else-if="show == 'relationship'">
        @{{ translate }}
    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ translated(options.title, locale) }}</label>
        <v-select taggable push-tags multiple v-model="translateArray" :disabled="show == 'mockup'" :closeOnSelect="false">

        </v-select>
        <input type="hidden" :name="name" v-model="translationString">
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text, locale) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-tags', {
    template: `@yield('tags')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input', 'locale'],
    created: function() {
        this.setInitialTranslation(
            this.input,
            '{{ app()->getLocale() }}',
            {!! json_encode(config('voyager.multilingual.locales')) !!},
            this.options.isTranslatable
        );
    },
    computed: {
        translateArray: {
            get: function () {
                if (!this.translate || this.translate == '')
                    return [];

                return this.translate.split(this.options.delimiter);
            },
            set: function (val) {
                this.translate = val.join(this.options.delimiter);
            }
        },
    },
    watch: {
        translate: function (newVal, oldVal) {
            this.$bus.$emit(this.name+'_change', newVal, oldVal);
        }
    },
});
</script>
