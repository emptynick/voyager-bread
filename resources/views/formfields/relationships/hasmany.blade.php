@section('hasmany')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Scope</label>
            <input type="text" class="form-control" v-model="options.scope">
        </div>
    </div>
    <div v-else-if="show == 'read'">

    </div>
    <div v-else-if="show == 'relationship'">

    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ translated(options.title, locale) }}</label>
        ...
        <small v-if="options.help_text.length > 0">@{{ translated(options.help_text, locale) }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-hasmany', {
    template: `@yield('hasmany')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input', 'locale'],
});
</script>
