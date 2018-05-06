@section('formfield-tab')
<div>
    <div>
        <slot name="content"></slot>
        <ul class="nav nav-tabs">
            <li v-for="(tab, index) in options.tabs" :class="(index == 0 ? 'active' : '')">
                <a data-toggle="tab" :href="'#' + (tab.name | slugify)">@{{ tab.name }}</a>
            </li>
        </ul>

        <div class="tab-content">
            <div v-for="(tab, index) in options.tabs">
                <div :id="(tab.name | slugify)" :class="'tab-pane fade ' + (index == 0 ? 'in active': '')">
                    <h3>@{{ tab.name }}</h3>
                    <input :value="(tab.name | slugify)">
                </div>
            </div>
        </div>
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <div class="form-group">
            <label>Tabs</label>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(tab, index) in options.tabs">
                        <td><input class="form-control" v-model="tab.name"></td>
                        <td><button class="btn btn-danger" @click="deleteTab(index)">Delete</button></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-primary" @click="addTab()">Add Tab</button>
        </div>
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-tab', {
    template: `@yield('formfield-tab')`,
    props: ['field', 'options', 'mockup', 'read', 'edit', 'add', 'i'],
    methods: {
        addTab: function() {
            this.options.tabs.push({name: 'New Tab ' + (this.options.tabs.length+1)});
        },
        deleteTab: function(index) {
            this.options.tabs.splice(index, 1);
        }
    }
});
</script>
