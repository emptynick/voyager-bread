<template>
    <div :class="'col-md-'+width" v-if="view != 'browse' && layoutType != 'list'">
        <div class="panel panel-bordered">
            <div class="panel-heading" v-if="view == 'mockup'">
                <h3 class="panel-title">{{ type.charAt(0).toUpperCase() + type.slice(1) }}, Field: {{ options.field || 'none' }}</h3>
                <div class="panel-actions">
                    <a class="panel-action voyager-trash" @click="$parent.$parent.deleteFormfield($vnode.key)"></a>
                    <a class="panel-action voyager-settings open-settings" @click="optionsOpen = !optionsOpen"></a>
                    <a class="panel-action voyager-code" @mousedown="startResize()" @mouseup="endResize()"></a>
                    <a class="panel-action voyager-handle drag_handle"></a>
                </div>
                <popper trigger="click" :force-show="optionsOpen" :options="{ placement: 'top' }">
                    <div class="popper">
                        <div class="pull-left">
                            <h4>Options</h4>
                        </div>
                        <div class="pull-right" @click="optionsOpen = false">
                            <span class="voyager-x" style="cursor:pointer;"></span>
                        </div>
                        <language-picker></language-picker>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <label>Field</label>
                            <select class="form-control" v-model="options.field">
                                <optgroup label="Fields">
                                    <option v-for="(column, key) in columns" :key="key" :value="column">
                                        {{ column }}
                                    </option>
                                </optgroup>
                                <optgroup label="Dynamic attributes" v-if="accessors">
                                    <option v-for="(accessor, key) in accessors" :key="key" :value="accessor">
                                        {{ accessor }}
                                    </option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" :value="true" v-model="options.translatable"> Translatable</label>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <language-input classes="form-control" placeholder="Title" v-model="options.title" />
                        </div>
                        <div class="form-group" v-if="true">
                            <label>Help Text</label>
                            <language-input classes="form-control" placeholder="Help Text" v-model="options.help_text" />
                        </div>
                        <div class="form-group" v-if="true">
                            <label>Placeholder</label>
                            <language-input classes="form-control" placeholder="Placeholder" v-model="options.placeholder" />
                        </div>
                        <div class="form-group" v-if="true">
                            <label>Default value</label>
                            <language-input classes="form-control" placeholder="Default Value" v-model="options.value" />
                        </div>
                        <component :is="'formfield-'+type" view="options" :options="options" :relationships="relationships" :base="this" />
                        <validation-input v-model="validation" />
                    </div>
                    <div slot="reference"></div>
                </popper>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="alert alert-danger" v-if="errors.length > 0">
                        <ul>
                            <li v-for="(error, key) in errors" :key="'error-'+key">{{ error }}</li>
                        </ul>
                    </div>
                    <label v-if="options.title">{{ getTranslation(options.title, view != 'mockup') }}</label>
                    <component :is="'formfield-'+type" :view="view" :options="options" :layout-type="layoutType" :base="this" />
                    <input type="hidden" :name="options.field" :value="getTranslatedValue()">
                    <span v-if="options.help_text && view != 'read'">{{ getTranslation(options.help_text, view != 'mockup') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div v-else-if="view == 'mockup' && layoutType == 'list'" class="row drag_handle row-dd">
        <div class="col-xs-2">{{ type.charAt(0).toUpperCase() + type.slice(1) }}</div>
        <div class="col-xs-2">
            <select class="form-control" v-model="options.field">
                <optgroup label="Fields">
                    <option v-for="(column, key) in columns" :key="key" :value="column">
                        {{ column }}
                    </option>
                </optgroup>
                <optgroup v-for="(relationship, key) in relationships" :label="relationship.name" :key="key">
                    <option v-for="(field, key) in relationship.fields" :key="key" :value="relationship.name+'.'+field">
                        {{ field }}
                    </option>
                </optgroup>
            </select>
        </div>
        <div class="col-xs-2">
            <language-input classes="form-control" placeholder="Title" v-model="options.title" />
        </div>
        <div class="col-xs-1">
            <input v-model="options.width" type="number" min="1" max="100" class="form-control" />
        </div>
        <div class="col-xs-1"><input type="checkbox" v-model="options.searchable" value="true"></div>
        <div class="col-xs-1"><input type="checkbox" v-model="options.orderable" value="true"></div>
        <div class="col-xs-1"><input type="radio" v-model="layout.order_by" :value="options.field"></div>
        <div class="col-xs-2">
            <popper trigger="click" :force-show="optionsOpen" :options="{ placement: 'top' }">
                <div class="popper">
                    <div class="pull-left">
                        <h4>Options</h4>
                    </div>
                    <div class="pull-right" @click="optionsOpen = false">
                        <span class="voyager-x" style="cursor:pointer;"></span>
                    </div>
                    <language-picker></language-picker>
                    <div class="clearfix"></div>
                    <div class="checkbox">
                        <label><input type="checkbox" :value="true" v-model="options.translatable"> Translatable</label>
                    </div>
                    <component :is="'formfield-'+type" :view="'options'" :options="options" :layout-type="layoutType" :base="this" />
                    <!-- TODO: add static translatable AND search_in_locale checkboxes -->
                    <validation-input v-model="validation" />
                </div>
                <div slot="reference"></div>
            </popper>
            <button class="btn btn-primary" @click="optionsOpen = !optionsOpen">Options</button>
            <button class="btn btn-danger" @click="$parent.$parent.deleteFormfield($vnode.key)">Delete</button>
        </div>
    </div>
    <component v-else :is="'formfield-'+type" :view="view" :options="options" :base="this" />
</template>

<script>
module.exports = {
    props: {
        view: {
            type: String,
            default: "browse"
        },
        value: {},
        options: {
            type: Object,
            default: function() {
                return {};
            }
        },
        columns: {},
        type: {},
        validation: {
            type: Array,
            default: function() {
                return [];
            }
        },
        layoutType: {},
        layout: {},
        accessors: {},
        relationships: {},
        errors: {
            type: Array,
            default: function() {
                return [];
            }
        },
        token: {}
    },
    data: function () {
        return {
            translatedValue: '',
            fields: 1, // Can be set to 0 for field-less formfields, 2 for date-ranges, ...
            optionsOpen: false,
            resizing: false,
        };
    },
    computed: {
        width: function () {
            if (this.options.width) {
                return this.options.width;
            }

            return 12;
        },
    },
    methods: {
       getValue: function () {
           if (this.options.translatable) {
                if (typeof this.translatedValue !== 'object' || this.translatedValue === null) {
                    this.translatedValue = JSON.parse(this.value || '{}');
                }

                return this.translatedValue[this.$eventHub.locale];
            }

            return this.translatedValue || '';
       },
       setValue: function (value) {
           if (this.options.translatable) {
                Vue.set(this.translatedValue, this.$eventHub.locale, value);
            } else {
                this.translatedValue = value;
            }

            this.$emit('input', this.translatedValue);
       },
       getTranslatedValue: function () {
           if (this.options.translatable) {
                return JSON.stringify(this.translatedValue);
            }

            return this.translatedValue;
       },
       startResize: function () {
           this.resizing = true;
       },
       endResize: function () {
           this.resizing = false;
       },
       resize: function (e) {
           if (this.resizing) {
               e.preventDefault();
                var rect = this.$el.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var threshold = rect.width / this.width;
                var size = Math.min(Math.max(Math.round(x / threshold), 2), 12);

                Vue.set(this.options, 'width', size);
           }
       },
       getRelationships: function (type) {
           var relationships = [];
           this.relationships.forEach(function (relationship) {
                if (relationship.type == type) {
                    relationships.push(relationship);
                }
           });

            return relationships;
       },
       getRelationship: function (ident) {
           var relationship = null;
           this.relationships.forEach(function (r) {
                if (r.ident == ident) {
                    relationship = r;
                }
           });

            return relationship;
       },
       getRelationshipLayouts: function (name) {
           var layouts = [];
            if (name && name !== '') {
                this.relationships.forEach(function (relationship) {
                    if (relationship.ident == name) {
                        layouts = relationship.layouts;
                    }
                });
            }

            return layouts;
        },
        getRelationshipLayout: function (ident, layoutname) {
            layout = null;
            this.getRelationshipLayouts(ident).forEach(function (l) {
                if (l.name == layoutname) {
                    layout = l;
                }
            });

            return layout;
        }
    },
    watch: {
        optionsOpen: function (open) {
            if (open) {
                this.$eventHub.$emit('close-options', this._uid);
            }
        },
        value: function (value) {
            this.translatedValue = value;
        },
    },
    mounted: function () {
        var vm = this;
        window.addEventListener('mousemove', this.resize);
        window.addEventListener('mouseup', this.endResize);

        // Close options when pressing Escape
        window.addEventListener('keydown', function (e) {
            if (e.keyCode == 27) {
                vm.optionsOpen = false;
            }
        });

        // Close options if another options-popup was opened
        this.$eventHub.$on('close-options', except => {
            if (vm._uid != except) {
                vm.optionsOpen = false;
            }
        });
    },
    created: function () {
        this.translatedValue = this.value;
    }
};
</script>