@section('paragraph')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Text</label>
            <language-input type="textarea" v-model="options.text" :input="options.text" />
        </div>
    </div>
    <div v-else>
        <p>@{{ translated(options.text) }}</p>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-paragraph', {
    template: `@yield('paragraph')`,
    props: ['show', 'options', 'type']
});
</script>
