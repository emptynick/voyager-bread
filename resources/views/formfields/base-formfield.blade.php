@section('base-formfield')
<div>
    <div class="panel panel-bordered"
         v-if="show == 'edit' || show == 'add' || show == 'mockup'"
         v-tooltip.notrigger="{ html: uid+'_options', visible: parent.isOptionsOpen(uid), class: 'options-tooltip', placement: 'bottom' }">
         <div class="panel-heading" v-if="show == 'mockup'">
             <h3 class="panel-title"></h3>
             <div class="panel-actions">
                 <a class="panel-action voyager-trash" @click="parent.deleteElement(id)"></a>
                 <a class="panel-action voyager-settings open-settings" @click="parent.openOptions(uid)"></a>
                 <a @mousedown="parent.startResize(id)" @mouseup="parent.endResize()" class="panel-action voyager-code drag_handle"></a>
                 <a class="panel-action voyager-handle"></a>
             </div>
         </div>
         <div class="panel-body">
            <label v-if="element.options.title">@{{ translate(element.options.title) }}</label>
            <component
                :is="'formfield-'+element.codename"
                :show="show"
                v-bind="element">
            </component>
            <span v-if="element.options.help_text">@{{ translate(element.options.help_text) }}</span>
            <div :id="uid+'_options'" v-if="show == 'mockup'">
                <div class="pull-left">
                    <h4>Options</h4>
                </div>
                <div class="pull-right" @click="parent.openOptions(null)">
                    <span class="voyager-x" style="cursor:pointer;"></span>
                </div>
                <language-switcher></language-switcher>
                <div class="clearfix"></div>
                <div class="form-group">
                    <label>Field</label>
                    <select class="form-control" v-model="element.field">
                        <option v-for="field in fields">
                            @{{ field }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <language-input v-model="element.options.title" />
                </div>
                <div class="form-group">
                    <label>Help Text</label>
                    <language-input v-model="element.options.help_text" />
                </div>
                <component
                    :is="'formfield-'+element.codename"
                    :show="'options'"
                    :layout-type="layoutType"
                    :fields="fields"
                    v-bind="element">
                </component>
                <validation-form :rules="element.validation"></validation-form>
            </div>
        </div>
    </div>
    <div v-else-if="show == 'options' && layoutType == 'list'">
        <div :id="uid+'_options'">
            <div class="pull-left">
                <h4>Options</h4>
            </div>
            <div class="pull-right" @click="parent.openOptions(null)">
                <span class="voyager-x" style="cursor:pointer;"></span>
            </div>
            <language-switcher></language-switcher>
            <div class="clearfix"></div>
            <component
                :is="'formfield-'+element.codename"
                :show="'options'"
                :layout-type="layoutType"
                :fields="fields"
                v-bind="element">
            </component>
        </div>
    </div>
</div>
@endsection

<script>
Vue.component('base-formfield', {
    template: `@yield('base-formfield')`,
    props: ['element', 'id', 'show', 'parent', 'layoutType', 'fields'],
    computed: {
        uid: function() {
            return this._uid;
        },
    },
});
</script>
