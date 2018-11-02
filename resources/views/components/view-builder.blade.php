@section('view-builder')
<draggable v-model="view.elements" :options="{ handle: '.voyager-handle', group: 'elements' }" style="min-height:100px; display:block">
    <div v-for="(element, id) in view.elements" :key="id" :class="'col-md-'+element.width">
        <base-formfield v-if="element.type == 'formfield'">
            <component :is="'formfield-'+element.type"></component>
        </base-formfield>
    </div>
</draggable>
@endsection

<script>
Vue.component('view-builder', {
    template: `@yield('view-builder')`,
    props: ['parent', 'view'],
    data: function() {
        return {

        };
    },
    methods: {

    },
});
</script>
