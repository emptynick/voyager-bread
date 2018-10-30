@section('belongsto')
<div>
    <div v-if="show == 'options'">
        <div class="form-group">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
        <div class="form-group">
            <label>{{ __("bread::generic.list") }}</label>
            <select class="form-control" v-model="options.list">
                <option v-for="list in lists">
                    @{{ list.name }}
                </option>
            </select>
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.editable" value="true">Editable</label>
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.allow_empty" value="true">Allow empty</label>
            <label><input type="checkbox" v-model="options.as_null" value="true" :disabled="!options.allow_empty">As null</label>
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.allow_add" value="true">Allow adding</label>
        </div>
        <div class="form-group">
            <label>{{ __("bread::generic.view") }}</label>
            <select class="form-control" v-model="options.add_view" :disabled="!options.allow_add">
                <option v-for="view in views">
                    @{{ view.name }}
                </option>
            </select>
        </div>
    </div>
    <div v-else-if="show == 'read'">

    </div>
    <div v-else-if="show == 'relationship'">

    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ translated(options.title, locale) }}</label>
        <div v-if="show == 'mockup'">
            <select class="form-control" disabled></select>
        </div>
        <div v-else>
            <div class="row" v-if="options.editable && options.allow_add && options.add_view != ''">
                <div class="pull-right">
                    <button class="btn btn-primary" @click.prevent="addRelationship">Add</button>
                </div>
            </div>
            <v-select :filterable="false" :options="results" v-model="selected" @search="onSearch" label="bread_key" v-if="options.editable">
                <template slot="no-options">No options</template>
                <template slot="option" slot-scope="option">
                    <component
                        :is="'formfield-'+computed.relationship_element.type"
                        :options="computed.relationship_element.options"
                        :computed="option[computed.relationship_element.field].computed"
                        :input="option[computed.relationship_element.field].data"
                        :show="'relationship'"
                        :locale="null"
                    ></component>
                </template>
                <template slot="selected-option" slot-scope="option">
                    <component
                        :is="'formfield-'+computed.relationship_element.type"
                        :options="computed.relationship_element.options"
                        :computed="option[computed.relationship_element.field].computed"
                        :input="option[computed.relationship_element.field].data"
                        :show="'relationship'"
                        :locale="null"
                    ></component>
                </template>
            </v-select>
            <div v-else>
                @{{ translated(selectedValue) }}
            </div>
            <input type="hidden" :name="this.computed.relationship" :value="this.selectedId">
        </div>
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text, locale) }}</small>
        <relationship-create :options="options" :computed="computed" v-if="options.allow_add && options.add_view != '' && show != 'mockup' && show != 'read'" />
    </div>
</div>
@endsection

<script>
Vue.component('formfield-belongsto', {
    template: `@yield('belongsto')`,
    props: ['show', 'options', 'computed', 'type', 'fields', 'name', 'input', 'locale', 'lists', 'views'],
    data: function() {
        return {
            results: [],
            selected: null,
            selectedId: this.input ? this.input.id : null,
        };
    },
    computed: {
        selectedValue: function() {
            if (this.results) {
                for (var i in this.results) {
                    if (this.results[i].bread_key == this.selectedId) {
                        return this.results[i][this.computed.relationship_element.field].data;
                    }
                }
            }
        }
    },
    methods: {
        onSearch: function(search, loading) {
            this.search(search, loading, this);
        },
        search: _.debounce((search, loading, vm, select = null) => {
            if (loading)
                loading(true);
            if (select)
                vm.selectedId = select;
            vm.$http.get(vm.computed.relationship_url+'?limit=10&page=1&list='+vm.options.list+'&query='+search+'&include='+vm.selectedId).then(response => {
                vm.results = response.body.data;
                if (vm.options.allow_empty) {
                    vm.results.unshift({
                        [vm.computed.relationship_element.field]: {
                            data: '{{ __('voyager::generic.none') }}',
                            computed: {
                                isTranslatable: false,
                            },
                        },
                        bread_key: ''
                    });
                }
                if (select) {
                    for (var i in vm.results) {
                        if (vm.results[i].bread_key == select) {
                            vm.selected = vm.results[i];
                        }
                    }
                }
                if (!vm.selected && vm.selectedId) {
                    for (var i in vm.results) {
                        if (vm.results[i].bread_key == vm.selectedId) {
                            vm.selected = vm.results[i];
                        }
                    }
                }
            }, response => {
                vm.$snotify.error('There was a problem:\n' + response.body.message, 'Error');
            });
            if (loading)
                loading(false);
        }, 350),
        addRelationship: function() {
            this.$bus.$emit(this.computed.relationship+'modalShow');
        },
    },
    watch: {
        selected: function(selected) {
            this.selectedId = selected.bread_key;
        },
    },
    mounted: function() {
        var vm = this;
        if (this.show == 'edit' || this.show == 'add') {
            this.search('', null, this);
        }

        this.$bus.$on(this.computed.relationship+'Added', function(key) {
            vm.search('', null, vm, key);
        });
    }
});
</script>
