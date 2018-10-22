@section('password')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder</label>
            <language-input type="text" v-model="options.placeholder" :input="options.placeholder" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.keep_password" value="true">Keep old value</label>
        </div>
    </div>
    <div v-else-if="show == 'read'">
        @{{ translated(options.title) }}
        <br>
        @{{ value }}
    </div>
    <div v-else-if="show == 'relationship'">
        @{{ translate }}
    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ translated(options.title) }}</label>
        <input type="password" class="form-control" :disabled="show == 'mockup'"
               :placeholder="options.placeholder"
               :name="name"
               v-model="input">
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-password', {
    template: `@yield('password')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input'],
});
</script>
