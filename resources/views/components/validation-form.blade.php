@section('validation-form')
<div>
    <div class="pull-left">
        <h4>Validation</h4>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <table class="table">
            <thead v-if="validation_rules.length > 0">
                <tr>
                    <th width="45%" style="background:transparent; color:white">Rule</th>
                    <th width="45%" style="background:transparent; color:white">Message</th>
                    <th width="10%" style="background:transparent; color:white"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(rule, key) in validation_rules" v-bind:item="rule">
                    <td><input type="text" v-model="rule.rule" class="form-control"></td>
                    <td>
                        <language-input type="text" v-model="rule.msg" :input="rule.msg" />
                    </td>
                    <td><button class="btn btn-danger" @click="deleteRule(key)">
                        <i class="voyager-trash"></i>
                    </button></td>
                </tr>
            </tbody>
        </table>
        <button class="btn btn-success" @click="addRule()"><i class="voyager-plus"></i></button>
    </div>
</div>
@endsection

<script>
Vue.component('validation-form', {
    template: `@yield('validation-form')`,
    props: ['validation_rules'],
    methods: {
        deleteRule: function(key) {
            this.validation_rules.splice(key, 1);
        },
        addRule: function() {
            let rule = {
                rule: "",
                msg: ""
            };
            this.validation_rules.push(rule);
        }
    }
});
</script>
