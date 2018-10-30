@section('list-builder')
<div>
List
</div>
@endsection

<script>
Vue.component('list-builder', {
    template: `@yield('list-builder')`,
    data: function() {
        return {

        };
    },
    methods: {

    },
});
</script>
