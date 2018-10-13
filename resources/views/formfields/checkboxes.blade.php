@section('checkboxes')
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
        <div class="col-md-12">
            <table class="table">
                <thead v-if="options.options.length > 0">
                    <tr>
                        <th width="40%" style="background:transparent; color:white">Key</th>
                        <th width="40%" style="background:transparent; color:white">Value</th>
                        <th width="10%" style="background:transparent; color:white"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(option, key) in options.options" v-bind:item="option">
                        <td><input type="text" v-model="option.key" class="form-control"></td>
                        <td><input type="text" v-model="option.value" class="form-control"></td>
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
        <div :class="'checkbox '+(show == 'mockup' ? 'disabled' : '')" v-for="option in options.options">
            <label><input type="checkbox" :name="getName()" :value="option.key" :disabled="show == 'mockup'">
                @{{ option.value }}
            </label>
        </div>
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-checkboxes', {
    template: `@yield('checkboxes')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input'],
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
        getName: function(option) {
            if (this.options.length > 1) {
                return this.name+'[]';
            }
            return this.name;
        },
    },
});
</script>
