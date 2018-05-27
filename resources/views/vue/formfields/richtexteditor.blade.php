@section('richtexteditor')
<div>
    <div>
        <slot name="content"></slot>
        <vue-editor v-model="options.content" :editorOptions="options" style="height:100%"></vue-editor>
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('richtexteditor', {
    template: `@yield('richtexteditor')`,
    mixins: [ Translatable ],
    props: {
        options: {
            required: true
        },
        i: {
            required: true
        }
    },
});
</script>
