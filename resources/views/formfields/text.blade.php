@section('formfield-text')
<div>
    <div v-if="show == 'options'">
        <div v-if="layoutType == 'view'">
            <div class="form-group">
               <label>Placeholder</label>
               <language-input v-model="options.placeholder" />
           </div>
           <div class="form-group">
               <label>Slug from</label>
               <select class="form-control" v-model="options.slug_from">
                   <option v-for="field in fields">
                       @{{ field }}
                   </option>
               </select>
           </div>
        </div>
        <div v-else>
            <div class="form-group">
                <label>Display-Length</label>
                <input type="number" class="form-control" v-model="options.display_length" />
            </div>
        </div>
    </div>
    <div v-else-if="show == 'mockup'">
        <input class="form-control" type="text" :placeholder="translate(options.placeholder)" disabled>
    </div>
    <div v-else-if="show == 'edit' || show == 'add'">
        <input class="form-control" type="text" v-model="inputLocal">
    </div>
    <div v-else-if="show == 'browse' || show == 'read'">

    </div>
</div>
@endsection

<script>
Vue.component('formfield-text', {
    template: `@yield('formfield-text')`,
    props: ['show', 'options', 'input', 'layout-type', 'fields'],
    computed: {
        inputLocal: {
            get: function() {
                return this.input;
            },
            set: function(value) {
                this.$emit('update:input', value);
            }
        }
    }
});
</script>
