@section('repeater')
<div>
    <div v-if="show == 'mockup'">
        <view-builder
            v-bind:elements.sync="options.elements"
            :fields="this.fields"
            :subid="this._uid"
            :from-repeater="true"
        />
    </div>
    <div v-if="show == 'edit' || show == 'add'">

    </div>
</div>
@endsection

<script>
Vue.component('formfield-repeater', {
    template: `@yield('repeater')`,
    props: ['show', 'options', 'type', 'name', 'input', 'locale', 'fields'],
    created: function() {
    },
});
</script>
