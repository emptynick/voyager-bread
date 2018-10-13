@section('paragraph')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Text</label>
            <textarea class="form-control" v-model="options.text"></textarea>
        </div>
    </div>
    <div v-else>
        <p>@{{ options.text }}</p>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-paragraph', {
    template: `@yield('paragraph')`,
    props: ['show', 'options', 'type']
});
</script>
