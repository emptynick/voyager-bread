@section('list-builder')
<table class="table table-bordered">
    <thead>
        <tr>
            <th></th>
            <th>Field</th>
            <th>Type</th>
            <th>Title</th>
            <th>Searchable</th>
            <th>Orderable</th>
            <th>First ordered</th>
            <th>Translatable</th>
            <th>Actions</th>
        </tr>
    </thead>
    <draggable v-model="layout.elements" :options="{ handle: '.voyager-handle', group: 'elements' }" :element="'tbody'">
        <tr v-for="(element, id) in layout.elements" :key="id">
            <td>
                <button class="btn btn-default voyager-handle"></button>
            </td>
            <td>
                <select class="form-control" v-model="element.field">
                    <optgroup label="Fields">
                        <option v-for="field in fields" :value="{type: 'field', name: field}">
                            @{{ field }}
                        </option>
                    </optgroup>
                    <optgroup label="Accessors" v-if="accessors.length > 0">
                        <option v-for="accessor in accessors" :value="{type: 'accessor', name: accessor}">
                            @{{ accessor }}
                        </option>
                    </optgroup>
                    <optgroup v-for="relationship in relationships" :label="relationship.name">
                        <option v-for="field in relationship.fields" :value="{type: 'relationship', relationship: relationship.name, name: field}">
                            @{{ field }}
                        </option>
                    </optgroup>
                </select>
            </td>
            <td>@{{ element.name }}</td>
            <td>
                <language-input v-model="element.options.title" />
            </td>
            <td>
                <input type="checkbox" :disabled="element.field.type != 'field'" v-model="element.options.searchable" />
            </td>
            <td>
                <input type="checkbox" :disabled="element.field.type != 'field'" v-model="element.options.orderable" />
            </td>
            <td>
                <input type="radio" :disabled="element.field.type != 'field'" v-model="layout.first_ordered" :value="id" />
            </td>
            <td>
                <input type="checkbox" v-model="element.options.is_translatable" />
            </td>
            <td>
                <button v-tooltip.notrigger="{ html: id+'_options', visible: isOptionsOpen(id), class: 'options-tooltip', placement: 'bottom' }"
                v-on:click="openOptions(id)"
                class="btn btn-primary">
                    Options
                </button>
                <div :id="id+'_options'">
                    <component
                        :is="'base-'+element.group"
                        :show="'options'"
                        :layout-type="'list'"
                        :parent="self"
                        :fields="fields"
                        :accessors="accessors"
                        :relationships="relationships"
                        v-bind="element"
                        :ref="id">
                    </component>
                </div>
                <button class="btn btn-danger" v-on:click.prevent="deleteElement(id)">Delete</button>
            </td>
        </tr>
    </draggable>
</table>
@endsection

<script>
Vue.component('list-builder', {
    template: `@yield('list-builder')`,
    props: ['parent', 'layout', 'fields', 'accessors', 'relationships'],
    data: function() {
        return {
            current_options: null,
            current_resize: null,
        };
    },
    computed: {
        self: function() {
            return this;
        }
    },
    methods: {
        isOptionsOpen: function (id) {
            return (this.current_options == id);
        },
        openOptions: function (id) {
            if (this.isOptionsOpen(id)) {
                this.current_options = null;
            } else {
                this.current_options = id;
            }
        },
        deleteElement: function(id) {
            this.$snotify.confirm('Are you sure you want to delete this Element?', 'Delete Element?', {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [{
                    text: 'Yes',
                    action: (toast) => {
                        this.layout.elements.splice(id, 1);
                        this.$snotify.remove(toast.id);
                    }, bold: false
                }, {
                    text: 'No',
                    action: (toast) => this.$snotify.remove(toast.id)
                }]
            });
        },
    },
});
</script>
