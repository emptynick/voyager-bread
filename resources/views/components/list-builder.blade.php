@section('list-builder')
<draggable v-model="list.elements" :options="{ handle: '.voyager-handle', group: 'elements' }" style="min-height:100px; display:block">
    <div v-for="(element, id) in list.elements" :key="id" :class="'col-md-'+element.width">
        
    </div>
</draggable>
@endsection

<script>
Vue.component('list-builder', {
    template: `@yield('list-builder')`,
    props: ['parent', 'list'],
    data: function() {
        return {

        };
    },
    methods: {

    },
});
</script>
