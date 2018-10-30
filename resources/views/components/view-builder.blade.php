@section('view-builder')
<div>
View
</div>
@endsection

<script>
Vue.component('view-builder', {
    template: `@yield('view-builder')`,
    data: function() {
        return {

        };
    },
    methods: {

    },
});
</script>
