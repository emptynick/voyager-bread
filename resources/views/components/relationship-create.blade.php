@section('relationship-create')
<div>
    <modal :name="options.relationship+'_modal'" :ref="computed.relationship+'_modal'">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row" v-if="computed.view.isTranslatable">
                    <language-switcher :languages="{{ json_encode(config('voyager.multilingual.locales')) }}"></language-switcher>
                </div>
                <form :id="computed.relationship+'_form'" @submit.prevent="submit">
                    <div v-for="(item, key) in computed.view.elements" :class="'col-md-'+item.width">
                        <div class="panel">
                            <div class="panel-body">
                                <div :class="'form-group '+((hasError(item.field) && item.type != 'repeater') ? 'has-error' : '')">
                                    <component
                                        v-if="item.group != 'relationship' && item.froup != 'layout'"
                                        :is="'formfield-'+item.type"
                                        :options="item.options"
                                        :computed="item.computed"
                                        :name="item.field"
                                        :show="'add'"
                                        :input="''"
                                        :locale="'{{ app()->getLocale() }}'"
                                        :errors="item.type == 'tabcontrol' ? this.errors : getErrors(item.field)"
                                    ></component>
                                    <span class="help-block" style="color:#f96868" v-if="hasError(item.field) && item.type != 'repeater'">
                                        <ul>
                                            <li v-for="msg in getErrors(item.field)">
                                                @{{ msg }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary">Save</button>
                        <button class="btn btn-primary" @click.prevent="close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </modal>
</div>
@endsection

<script>
Vue.component('relationship-create', {
    template: `@yield('relationship-create')`,
    props: ['name', 'options', 'computed'],
    data: function() {
        return {
            errors: [],
        };
    },
    methods: {
        submit: function(e) {
            var data = new FormData(e.target);
            data.append('_token', '{{ csrf_token() }}');
            this.$http.post(this.computed.create_url, data).then(response => {
                var key = response.body;
                this.$bus.$emit(this.computed.relationship+'Added', key);
                this.close();
            }, response => {
                if (response.status == 422) {
                    //Validation failed
                    this.errors = response.body.errors;
                } else {
                    vm.$snotify.error('There was a problem:\n' + response.body.message, 'Error');
                }
            });
        },
        close: function() {
            this.$modal.hide(this.computed.relationship+'_modal');
        },
        hasError: function(field) {
            return (this.getErrors(field).length > 0);
        },
        getErrors: function(field, strict = false) {
            let errors = this.errors[field];
            if (!errors && !strict) {
                errors = [];
                for (var key in this.errors) {
                    if (key.startsWith(field+'.')) {
                        errors.push({ [key]: this.errors[key] });
                    }
                }

                if (errors.length == 0) {
                    return [];
                }
            }

            return errors;
        },
    },
    mounted: function() {
        var vm = this;
        this.$bus.$on(this.computed.relationship+'modalShow', function() {
            if (vm.computed.relationship != '') {
                vm.$modal.show(vm.computed.relationship+'_modal');
            }
        });
        this.$bus.$on(this.computed.relationship+'modalHide', function() {
            if (vm.computed.relationship != '') {
                vm.$modal.hide(vm.computed.relationship+'_modal');
            }
        });
    }
});
</script>
