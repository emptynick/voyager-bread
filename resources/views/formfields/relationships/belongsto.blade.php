@section('belongsto')
<div>
    <div v-if="show == 'options'">
        <div class="form-group">
            <label>Title</label>
            <language-input type="text" v-model="options.title" :input="options.title" />
        </div>
        <div class="form-group">
            <label>Help text</label>
            <language-input type="text" v-model="options.help_text" :input="options.help_text" />
        </div>
        <div class="form-group">
            <label>{{ __("bread::generic.list") }}</label>
            <select class="form-control" v-model="options.list">
                <option v-for="list in lists">
                    @{{ list.name }}
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>Scope</label>
            <input type="text" class="form-control" v-model="options.scope" />
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.allow_empty" value="true">Allow empty select</label>
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.allow_add" value="true">Allow adding</label>
        </div>
        <div class="form-group">
            <label>{{ __("bread::generic.view") }}</label>
            <select class="form-control" v-model="options.add_view" :disabled="options.allow_add">
                <option v-for="view in views">
                    @{{ view.name }}
                </option>
            </select>
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
Vue.component('formfield-belongsto', {
    template: `@yield('belongsto')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input', 'locale', 'lists', 'views'],
});
</script>
