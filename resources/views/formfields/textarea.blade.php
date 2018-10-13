@section('textarea')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'list'">
            <label>Display length</label>
            <input type="number" min="1" class="form-control" v-model="options.length">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Default value</label>
            <input type="text" class="form-control" v-model="options.default_value">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder</label>
            <input type="text" class="form-control" v-model="options.placeholder">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Rows</label>
            <input type="number" min="1" class="form-control" v-model="options.rows">
        </div>
    </div>
    <div v-else>
        @{{ options.title }}
        <textarea class="form-control" :disabled="show == 'mockup'"
               :placeholder="options.placeholder"
               :rows="options.rows"
               :name="name"
               v-model="value">
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-textarea', {
    template: `@yield('textarea')`,
    props: ['show', 'options', 'type', 'name', 'input'],
    data: function() {
        return {
            'value': (this.input == '' ? options.default_value : this.input),
        }
    },
});
</script>
