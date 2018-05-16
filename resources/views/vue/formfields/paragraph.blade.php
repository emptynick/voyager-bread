@section('paragraph')
<div>
    <div>
        <slot name="content"></slot>
        <nl2br tag="p" :text="options.text" />
        <slot name="content_after"></slot>
    </div>
    <div class="options" :id="i+'_options'">
        <slot name="options"></slot>
        <div class="form-group">
            <label>Text</label>
            <textarea class="form-control" rows="5" v-model="options.text"></textarea>
        </div>
        <slot name="options_after"></slot>
    </div>
</div>
@endsection

<script>
Vue.component('paragraph', {
    template: `@yield('paragraph')`,
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
