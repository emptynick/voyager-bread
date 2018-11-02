@section('base-layout-element')
<div>

</div>
@endsection

<script>
Vue.component('base-layout-element', {
    template: `@yield('base-layout-element')`,
    props: [],
    data: function() {
        return {

        };
    },
    methods: {

    },
});
</script>
