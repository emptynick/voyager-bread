@section('formfield-text')
<div>
    Hallo
</div>
@endsection

<script>
Vue.component('formfield-text', {
    template: `@yield('formfield-text')`,
    props: [],
    data: function() {
        return {

        };
    },
    methods: {

    },
});
</script>
