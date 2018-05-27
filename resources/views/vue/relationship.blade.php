@section('relationship')
<div>
    ...
</div>
@endsection
<script>

Vue.component('relationship', {
    template: `@yield('relationship')`,
    props: [ 'element' ],
    data: function() {
        return {

        }
    },
    computed: {

    },
    methods: {

    },
    mounted: function() {

    }
});
</script>
