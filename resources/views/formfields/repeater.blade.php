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
        />
    </div>
    <div v-if="show == 'edit' || show == 'add'">
        <div class="panel-group" id="accordionName">
            <div class="panel panel-default" v-for="(item, i) in this.content" :key="i">
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
                                            :show="'{{ (isset($content) && $content->getKey()) ? 'edit' : 'add' }}'"
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
        <h4 class="text-center" style="cursor:pointer;" v-on:click="addItem()"><i class="voyager-plus"></i> Add Item</h4>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-repeater', {
    template: `@yield('repeater')`,
    props: ['show', 'options', 'type', 'name', 'input', 'locale', 'fields', 'errors'],
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
        getContent: function (item, attribute) {
            return item[attribute];
        }
    },
    mounted: function() {
        try {
            this.content = JSON.parse(this.input);
        } catch {
            this.content = [];
        }
    }
});
</script>
