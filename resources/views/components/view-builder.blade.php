@section('view-builder')
<div>
    <draggable v-model="allElements" :options="{ handle: '.voyager-handle', group: 'elements' }" style="min-height:100px; display:block">
        <div v-for="(item, id) in allElements" :key="id" :class="'col-md-'+item.width">
            <div :class="'panel panel-bordered '+item.class"
            style="height:100%; margin-bottom:0 !important;"
            v-tooltip.notrigger="{ html: (subid ? subid+'_' : '')+id+'_options', visible: isOptionsOpen((subid ? subid+'_' : '')+id), class: 'options-tooltip', placement: 'bottom' }">
                <div class="panel-heading">
                    <h3 class="panel-title"></h3>
                    <div class="panel-actions">
                        <a class="panel-action voyager-trash" @click="deleteElement(id)"></a>
                        <a class="panel-action voyager-settings open-settings" @click="openOptions((subid ? subid+'_' : '')+id)"></a>
                        <a @mousedown="startDrag(id)" @mouseup="endDrag()" class="panel-action voyager-code drag_handle"></a>
                        <a class="panel-action voyager-handle"></a>
                    </div>
                </div>
                <div class="panel-body formfield-panel">
                    <component
                        :is="componentType(item)"
                        v-bind="item"
                        :show="'mockup'"
                        :type="'view'"
                        :translatable="'{{ $model->isTranslatable ?: false }}'"
                        :locale="null"
                        :fields="this.fields"
                        :relationships="relationships">
                    </component>
                    <div :id="(subid ? subid+'_' : '')+id+'_options'">
                        <div class="pull-left">
                            <h4>{{ __("bread::generic.options") }}</h4>
                        </div>
                        <div class="pull-right" @click="openOptions(null)">
                            <span class="voyager-x" style="cursor:pointer;"></span>
                        </div>
                        <language-switcher :languages="{{ json_encode(config('voyager.multilingual.locales')) }}"></language-switcher>
                        <div class="clearfix"></div>
                        <div class="form-group" v-if="!fromRepeater && item.group != 'relationship' && item.group != 'layout'">
                            <label>{{ __("bread::generic.field") }}</label>
                            <select class="form-control" v-model="item.field">
                                <option v-for="field in fields">
                                    @{{ field }}
                                </option>
                            </select>
                        </div>
                        <div v-if="fromRepeater" class="form-group">
                            <label>{{ __("bread::generic.attribute") }}</label>
                            <input type="text" class="form-control" v-model="item.attribute">
                        </div>
                        <component
                            :is="componentType(item)"
                            v-bind="item"
                            :show="'options'"
                            :type="'view'"
                            :fields="fields"
                            :lists="getLists(item)"
                            :views="getViews(item)"
                            :translatable="'{{ $model->isTranslatable ?: false }}'">
                        </component>
                        <validation-form v-bind="item" v-if="item.type != 'paragraph' && item.type != 'heading'" />
                    </div>
                </div>
            </div>
            <br>
        </div>
    </draggable>
</div>
@endsection

<script>
Vue.component('view-builder', {
    template: `@yield('view-builder')`,
    props: ['elements', 'fields', 'subid', 'from-repeater', 'relationships'],
    data: function() {
        return {
            currentOptionsId: -1,
            currentOptionsEl: null,
            currentDragId: -1,
            cols: 12,
        };
    },
    computed: {
        allElements: {
            get: function() {
                return this.elements;
            }, set: function(value) {
                this.$emit('update:elements', value);
            }
        }
    },
    methods: {
        addElement: function(type, group) {
            let options = [];
            let def_opt = document.getElementById(type+'_default_options');
            if (!def_opt) {
                this.$snotify.error('The formfield "'+type+'" is not supported.', 'Error');
                return;
            }
            options = JSON.parse(def_opt.value);
            let newitem = {
                width: 12,
                options: options,
                type: type,
                field: "",
                validation_rules: []
            };
            if (group == 'formfield' || group == 'layout') {
                newitem.group = group;
            } else {
                newitem.group = 'relationship';
                newitem.options.relationship = group;
            }
            this.elements.push(newitem);
        },
        deleteElement(id) {
            this.$snotify.confirm('{{ __("voyager::manager.delete_element_confirm") }}', '{{ __("voyager::manager.delete_element") }}', {
				timeout: 5000,
				showProgressBar: true,
				closeOnClick: false,
				pauseOnHover: true,
				buttons: [
					{text: '{{ __("voyager::generic.yes") }}', action: (toast) => {
                        this.elements.splice(id, 1);
                        this.$snotify.remove(toast.id);
                    }, bold: false},
					{text: '{{ __("voyager::generic.no") }}', action: (toast) => this.$snotify.remove(toast.id) },
				]
			});
        },
        isOptionsOpen: function(id) {
            return this.currentOptionsId == id;
        },
        openOptions: function(id) {
            if (id !== null) {
                this.$bus.$emit('closeOptions', this._uid);
            }
            if (this.isOptionsOpen(id)) {
                this.currentOptionsId = -1;
            } else {
                this.currentOptionsId = id;
                this.currentOptionsEl = document.getElementById(this.currentOptionsId+'_options');
            }
        },
        componentType: function(item) {
            return 'formfield-'+item.type;
        },
        startDrag: function(id) {
            this.currentDragId = id;
        },
        endDrag: function() {
            this.currentDragId = -1;
        },
        drag: function(e) {
            if (this.currentDragId > -1) {
                e.preventDefault();
                var maxWidth = this.$el.clientWidth;
                var relative = e.clientX - this.findPos(this.$el).left;
                var threshold = maxWidth / this.cols;
                var size = Math.min(Math.max(Math.round(relative / threshold), 2), this.cols);
                if (this.elements[this.currentDragId].type == 'repeater' && size <= 6) {
                    this.elements[this.currentDragId].width = 6;
                } else {
                    this.elements[this.currentDragId].width = size;
                }
            }
        },
        findPos: function(obj) {
            var curleft = curtop = 0;
            if (obj.offsetParent) {
                do {
                    curleft += obj.offsetLeft;
                    curtop += obj.offsetTop;
                } while (obj = obj.offsetParent);
            }
            return {
                left : curleft,
                top : curtop
            };
        },
        getLists: function(item) {
            if (item.group == 'relationship') {
                for (var r in this.relationships) {
                    if (this.relationships[r] && this.relationships[r].name == item.options.relationship) {
                        return this.relationships[r].lists;
                    }
                }
            }
            return [];
        },
        getViews: function(item) {
            if (item.group == 'relationship') {
                for (var r in this.relationships) {
                    if (this.relationships[r] && this.relationships[r].name == item.options.relationship) {
                        return this.relationships[r].views;
                    }
                }
            }
            return [];
        },
    },
    mounted: function() {
        var vm = this;
        window.addEventListener('mouseup', this.endDrag);
        window.addEventListener('mousemove', this.drag);
        this.$bus.$on('closeOptions', function(uid) {
            if (vm._uid != uid) {
                vm.openOptions(null);
            }
        });
        window.addEventListener('click', function(event) {
           if (!event.target.className.includes('open-settings') && vm.currentOptionsEl !== null && event.path.indexOf(vm.currentOptionsEl) == -1) {
               vm.openOptions(null);
           }
       });
    },
});
</script>
