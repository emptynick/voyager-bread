@section('tabcontrol')
<div>
    <div v-if="show == 'options'">
        <div class="pull-left">
            <h4>Tabs</h4>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12">
            <table class="table">
                <thead v-if="options.tabs.length > 0">
                    <tr>
                        <th width="90%" style="background:transparent; color:white">Name</th>
                        <th width="10%" style="background:transparent; color:white"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(tab, key) in options.tabs" v-bind:item="tab">
                        <td>
                            <language-input type="text" v-model="options.tabs[key]" :input="tab" />
                        </td>
                        <td><button class="btn btn-danger" @click="deleteTab(key)">
                            <i class="voyager-trash"></i>
                        </button></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-success" @click="addTab()"><i class="voyager-plus"></i></button>
        </div>
    </div>
    <div v-if="show == 'mockup'">
        <div v-if="options.tabs.length > 0">
            <ul class="nav nav-tabs">
                <li v-for="(tab, key) in options.tabs" :class="options.default_tab == key ? 'active' : ''">
                    <a @click="openTab(key)" href="#">@{{ translated(tab, locale) }}</a>
                </li>
            </ul>
            <div class="tab-content" style="min-height:100px;">
                <div v-for="(tab, key) in options.tabs" :class="'tab-pane fade ' + (options.default_tab == key ? 'in active' : '')">
                    <view-builder
                        v-bind:elements.sync="options.elements[key]"
                        :fields="this.fields"
                        :subid="subid+'_'+key"
                        :from-repeater="true"
                        :relationships="this.relationships"
                    />
                </div>
            </div>
        </div>
        <div v-else>
            <div class="panel panel-bordered">
                <div class="panel-body" style="text-align:center">
                    <h3>Add Tabs in the options.</h3>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div>
            <ul class="nav nav-tabs">
                <li v-for="(tab, key) in options.tabs" :class="options.default_tab == key ? 'active' : ''">
                    <a @click="openTab(key)" href="#">@{{ translated(tab, locale) }}</a>
                </li>
            </ul>
            <div class="tab-content" style="min-height:100px;">
                <div v-for="(tab, key) in options.tabs" :class="'tab-pane fade ' + (options.default_tab == key ? 'in active' : '')">
                    <div v-for="(el, key) in options.elements[key]" :class="'col-md-'+el.width">
                        <div class="panel">
                            <div class="panel-body">
                                <div :class="'form-group ' + (hasErrors(el.field) ? 'has-error' : '')">
                                    <component
                                        :is="'formfield-'+el.type"
                                        :options="el.options"
                                        :errors="[]"
                                        :name="el.field"
                                        :show="show"
                                        :input="getContent(el.attribute)"
                                        :locale="'{{ app()->getLocale() }}'"
                                        :lists="el.lists"
                                        :views="el.views"
                                    ></component>
                                    <span class="help-block" style="color:#f96868" v-if="hasErrors(el.attribute)">
                                        <ul>
                                            <li v-for="msg in getErrors(el.attribute)">
                                                @{{ msg }}
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-tabcontrol', {
    template: `@yield('tabcontrol')`,
    props: ['show', 'options', 'type', 'name', 'locale', 'fields', 'errors', 'content', 'relationships'],
    methods: {
        deleteTab: function(key) {
            this.options.tabs.splice(key, 1);
            this.$delete(this.options.elements, key);
            if (this.options.default_tab == key) {
                this.options.default_tab -= 1;
            }
        },
        addTab: function() {
            this.options.tabs.push('');
            this.options.elements.push([]);
        },
        openTab: function(key) {
            this.options.default_tab = key;
        },
        getContent: function (field) {
            //return item[attribute];
        },
        hasErrors: function(field) {
            if (this.getErrors(field).length > 0) {
                return true;
            }
            return false;
        },
        getErrors: function(field) {
            return [];
        },
    },
    computed: {
        subid: function() {
            return this._uid;
        }
    },
});
</script>
