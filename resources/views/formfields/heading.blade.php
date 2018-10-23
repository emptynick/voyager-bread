@section('heading')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Text</label>
            <language-input type="text" v-model="options.text" :input="options.text" />
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Size</label>
            <select class="form-control" v-model="options.size">
                <option value="h1">H1</option>
                <option value="h2">H2</option>
                <option value="h3">H3</option>
                <option value="h4">H4</option>
                <option value="h5">H5</option>
                <option value="h6">H6</option>
            </select>
        </div>
    </div>
    <div v-else>
        <component :is="options.size">@{{ translated(options.text, locale) }}</component>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-heading', {
    template: `@yield('heading')`,
    props: ['show', 'options', 'type', 'locale']
});
</script>
