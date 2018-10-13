@section('dynamicselect')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <input type="text" class="form-control" v-model="options.help_text">
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
        <label v-if="options.title.length > 0">@{{ options.title }}</label>
        <multiselect v-model="ttt" :options="options.options" track-by="key" label="value" :multiselect="options.multiple">
        </multiselect>
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-dynamicselect', {
    template: `@yield('dynamicselect')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input', 'field'],
    data: function() {
        return {
            ttt: '',
        };
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
        }
    },
});
</script>
