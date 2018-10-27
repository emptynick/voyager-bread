@section('repeater')
<div>
    <div v-if="show == 'options'">
        <div class="form-group">
            <label>Label</label>
            <language-input type="text" v-model="options.label" :input="options.label" />
        </div>
    </div>
    <div v-if="show == 'mockup'">
        <view-builder
            v-bind:elements.sync="options.elements"
            :fields="this.fields"
            :subid="this._uid"
            :from-repeater="true"
            :relationships="this.relationships"
        />
    </div>
    <div v-if="show == 'edit' || show == 'add'">
        <div class="panel-group" id="accordionName">
            <draggable :list="this.content">
                <div :class="'panel panel-bordered ' + (hasRowErrors(i) ? 'panel-danger' : 'panel-bordered')" v-for="(item, i) in this.content" :key="i">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" :data-parent="'#'+accordionName" :href="'#'+accordionName+'_'+i">
                                @{{ translated(options.label, locale) }} #@{{ i+1 }}
                            </a>
                        </h4>
                        <div class="panel-actions">
                            <a class="panel-action voyager-handle"></a>
                            <a class="panel-action voyager-trash" @click="deleteItem(i)"></a>
                        </div>
                    </div>
                    <div :id="accordionName+'_'+i" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div v-for="(el, key) in options.elements" :class="'col-md-'+el.width">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div :class="'form-group ' + (hasErrors(i, el.attribute) ? 'has-error' : '')">
                                            <component
                                                :is="'formfield-'+el.type"
                                                :options="el.options"
                                                :name="name+'['+i+']['+el.attribute+']'"
                                                :show="'{{ (isset($content) && $content->getKey()) ? 'edit' : 'add' }}'"
                                                :input="getContent(item, el.attribute)"
                                                :locale="'{{ app()->getLocale() }}'"
                                                :lists="el.lists"
                                                :views="el.views"
                                            ></component>
                                            <span class="help-block" style="color:#f96868" v-if="hasErrors(i, el.attribute)">
                                                <ul>
                                                    <li v-for="msg in getErrors(i, el.attribute)">
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
            </draggable>
        </div>
        <h4 class="text-center" style="cursor:pointer;" v-on:click="addItem()"><i class="voyager-plus"></i> Add @{{ translated(options.label, locale) }}</h4>
        <span class="help-block" style="color:#f96868" v-if="strictErrors && strictErrors.length > 0">
            <ul>
                <li v-for="msg in strictErrors">@{{ msg }}</li>
            </ul>
        </span>
    </div>
    <div v-if="show == 'read'">
        <div class="panel panel-bordered" v-for="(item, i) in this.content" :key="i">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" :data-parent="'#'+accordionName" :href="'#'+accordionName+'_'+i">
                        @{{ translated(options.label, locale) }} #@{{ i+1 }}
                    </a>
                </h4>
            </div>
            <div :id="accordionName+'_'+i" class="panel-collapse collapse">
                <div class="panel-body">
                    <div v-for="(el, key) in options.elements" :class="'col-md-'+el.width">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="form-group">
                                    <component
                                        :is="'formfield-'+el.type"
                                        :options="el.options"
                                        :name="name+'['+i+']['+el.attribute+']'"
                                        :show="'read'"
                                        :input="getContent(item, el.attribute)"
                                        :locale="'{{ app()->getLocale() }}'"
                                        :errors="null"
                                    ></component>
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
Vue.component('formfield-repeater', {
    template: `@yield('repeater')`,
    props: ['show', 'options', 'type', 'name', 'input', 'locale', 'fields', 'errors', 'strict-errors', 'relationships'],
    data: function() {
        return {
            content: null,
            accordionName: this._uid + '_accordion'
        };
    },
    methods: {
        addItem: function() {
            let newItem = {};
            this.options.elements.map(function(value, key) {
                newItem[value.attribute] = '';
            });
            this.content.push(newItem);
        },
        deleteItem: function(id) {
            //Todo: show a confirmation here?
            //Todo: delete files via ajax?
            this.$delete(this.content, id);
        },
        getContent: function (item, attribute) {
            return item[attribute];
        },
        hasErrors: function(i, attr) {
            if (this.getErrors(i, attr).length > 0) {
                return true;
            }
            return false;
        },
        getErrors: function(i, attr) {
            var name = this.name+'.'+i+'.'+attr;
            var errors = [];
            this.errors.map(function(error) {
                for (var key in error) {
                    if (key == name) {
                        errors.push(error[name][0]);
                    }
                }
            });
            return errors;
        },
        hasRowErrors: function(i) {
            if (this.errors) {
                for (var key in this.errors) {
                    for (var err in this.errors[key]) {
                        if (err.startsWith(this.name+'.'+i)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        },
    },
    mounted: function() {
        try {
            this.content = JSON.parse(this.input);
        } catch {
            //Input can be an object already
            if (this.input instanceof Object) {
                this.content = this.input;
            } else {
                this.content = [];
            }
        }
    }
});
</script>
