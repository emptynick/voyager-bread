@section('base-formfield')
<div>
    <div v-if="show == 'mockup'">
        <div class="panel panel-bordered"
         v-tooltip.notrigger="{ html: uid+'_options', visible: parent.isOptionsOpen(uid), class: 'options-tooltip', placement: 'bottom' }">
             <div class="panel-heading" v-if="show == 'mockup'">
                 <h3 class="panel-title"></h3>
                 <div class="panel-actions">
                     <a class="panel-action voyager-trash" @click="parent.deleteElement(uid)"></a>
                     <a class="panel-action voyager-settings open-settings" @click="parent.openOptions(uid)"></a>
                     <a @mousedown="parent.startResize(id)" @mouseup="parent.endResize()" class="panel-action voyager-code drag_handle"></a>
                     <a class="panel-action voyager-handle"></a>
                 </div>
             </div>
             <div class="panel-body">
                <language-output v-if="options.title" :input="options.title" type="label" :once="false"></language-output>
                <component
                    :is="'formfield-'+codename"
                    :show="show"
                    :options="options"
                    :computed="computed">
                </component>
                <language-output v-if="options.help_text" :input="options.help_text" type="span" :once="false"></language-output>
                <div :id="uid+'_options'" v-if="show == 'mockup'">
                    <div class="pull-left">
                        <h4>Options</h4>
                    </div>
                    <div class="pull-right" @click="parent.openOptions(null)">
                        <span class="voyager-x" style="cursor:pointer;"></span>
                    </div>
                    <language-switcher></language-switcher>
                    <div class="clearfix"></div>
                    <div class="form-group">
                        <label>Field</label>
                        <select class="form-control" :value="field" @change="$emit('update:field', $event.target.value)">
                            <option v-for="field in fields">
                                @{{ field }}
                            </option>
                        </select>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" v-model="options.is_translatable">Is Translatable
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <language-input v-model="options.title" />
                    </div>
                    <div class="form-group">
                        <label>Help Text</label>
                        <language-input v-model="options.help_text" />
                    </div>
                    <component
                        :is="'formfield-'+codename"
                        :show="'options'"
                        :layout-type="layoutType"
                        :fields="fields"
                        :options="options">
                    </component>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" v-model="options.disabled">Disabled
                        </label>
                    </div>
                    <validation-form :rules="validation"></validation-form>
                </div>
            </div>
        </div>
    </div>
    <div v-else-if="show == 'options'">
        <component
            :is="'formfield-'+codename"
            :show="'options'"
            :layout-type="layoutType"
            :fields="fields"
            :options="options">
        </component>
    </div>
    <div v-else>
        <label v-if="options.title && show != 'browse'">@{{ computed.title }}</label>
        <component
            :is="'formfield-'+codename"
            :input.sync="content"
            :show="show"
            :options="options"
            :field="field"
            :computed="computed">
        </component>
        <span v-if="options.help_text && show != 'browse'">@{{ computed.help_text }}</span>
        <input type="hidden" :name="field" :value="JSON.stringify(value)" v-if="show != 'browse'">
    </div>
</div>
@endsection
<script>
Vue.component('base-formfield', {
    template: `@yield('base-formfield')`,
    props: ['show', 'field', 'input', 'codename', 'options', 'computed', 'layout-type', 'parent', 'fields', 'id', 'validation'],
    data: function() {
        return {
            value: this.input,
            locale: '{{ $locale }}',
            uid: this._uid,
        };
    },
    computed: {
        content: {
            get: function () {
                if (this.options.is_translatable && this.value) {
                    return this.value[this.locale];
                }

                return this.value;
            },
            set: function (newValue) {
                if (this.options.is_translatable) {
                    Vue.set(this.value, this.locale, newValue);
                } else {
                    this.value = newValue;
                }

                this.$bus.$emit('change_'+this.field, newValue);
            }
        },
    },
    mounted: function() {
        this.$bus.$on('setLocale', (locale) => {
            this.locale = locale;
        });
        if (this.show != 'mockup' && this.show != 'options' && this.options.is_translatable) {
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
    },
});
</script>
