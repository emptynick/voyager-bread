@section('select')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.multiple" value="true">Multiple</label>
        </div>
        <div class="col-md-12">
            <table class="table">
                <thead v-if="options.options.length > 0">
                    <tr>
                        <th width="40%" style="background:transparent; color:white">Key</th>
                        <th width="40%" style="background:transparent; color:white">Value</th>
                        <th width="10%" style="background:transparent; color:white">Default</th>
                        <th width="10%" style="background:transparent; color:white"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(option, key) in options.options" v-bind:item="option">
                        <td><input type="text" v-model="option.key" class="form-control"></td>
                        <td><input type="text" v-model="option.value" class="form-control"></td>
                        <td><input type="radio" :name="field+'_default'" v-model="options.default" :value="option.key"></td>
                        <td><button class="btn btn-danger" @click="deleteOption(key)">
                            <i class="voyager-trash"></i>
                        </button></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-success" @click="addOption()"><i class="voyager-plus"></i></button>
        </div>
    </div>
    <div v-else-if="show == 'read'">

    </div>
    <div v-else-if="show == 'relationship'">

    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ translated(options.title, locale) }}</label>
        <v-select v-model="value" :options="options.options" label="value" :multiple="options.multiple" :disabled="show == 'mockup'">

        </v-select>
        <div v-if="options.multiple">
            <div v-for="(item, key) in value">
                <input type="hidden" :name="name+'[]'" :value="options.options[key].key">
            </div>
        </div>
        <div v-else>
            <input type="hidden" :name="name" :value="value ? value.key : ''">
        </div>
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text, locale) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-select', {
    template: `@yield('select')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input', 'field', 'locale'],
    data: function () {
        return {
            value: this.parseInput(this.input),
        }
    },
    methods: {
        deleteOption: function(key) {
            this.options.options.splice(key, 1);
        },
        addOption: function() {
            let option = {
                key: "",
                value: ""
            };
            this.options.options.push(option);
        },
        parseInput: function(input) {
            if (input == null || input == '') {
                return this.options.multiple ? [] : '';
            } else {
                if (this.options.multiple) {
                    var original = JSON.parse(input);
                    var data = [];
                    original.map(function(key) {
                        for (var option in this.options.options) {
                            if (this.options.options[option].key == key) {
                                data.push({
                                    key: key,
                                    value: this.options.options[option].value,
                                });
                            }
                        }
                    }, this);
                    return data;
                } else {
                    for (var option in this.options.options) {
                        if (this.options.options[option].key == input) {
                            return {
                                key: input,
                                value: this.options.options[option].value,
                            };
                        }
                    }
                }
            }

            return input;
        }
    },
});
</script>
