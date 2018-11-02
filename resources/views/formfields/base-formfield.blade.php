@section('base-formfield')
<div>
    <slot></slot>
</div>
@endsection

<script>
Vue.component('base-formfield', {
    template: `@yield('base-formfield')`,
    props: [],
    data: function() {
        return {

        };
    },
    methods: {

    },
});
</script>
