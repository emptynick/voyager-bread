@section('radiobuttons')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
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
        <label v-if="options.title.length > 0">@{{ transltedoptions.title, locale) }}</label>
        <div :class="'radio '+(show == 'mockup' ? 'disabled' : '')" v-for="option in options.options">
            <label><input type="radio" :name="name" :value="option.key" :disabled="show == 'mockup'">
                @{{ option.value }}
            </label>
        </div>
        <small v-if="options.help_text.length > 0">@{{ transltedoptions.help_text, locale) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-radiobuttons', {
    template: `@yield('radiobuttons')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input', 'locale'],
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
    },
});
</script>
