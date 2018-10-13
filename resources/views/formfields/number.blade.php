@section('number')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="type == 'view'">
            <label>Default value</label>
            <input type="text" class="form-control" v-model="options.default_value">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Placeholder</label>
            <input type="text" class="form-control" v-model="options.placeholder">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
        <div class="form-group col-md-6">
            <label>Prefix</label>
            <input type="text" class="form-control" v-model="options.prefix">
        </div>
        <div class="form-group col-md-6">
            <label>Suffix</label>
            <input type="text" class="form-control" v-model="options.suffix">
        </div>
        <div class="form-group col-md-12">
            <label>Decimals</label>
            <input type="number" min="0" class="form-control" v-model="options.decimals">
        </div>
        <div class="form-group col-md-6">
            <label>Decimal sep.</label>
            <input type="text" class="form-control" v-model="options.dec_point">
        </div>
        <div class="form-group col-md-6">
            <label>Thousands sep.</label>
            <input type="text" class="form-control" v-model="options.thousands_sep">
        </div>
        <div class="form-group col-md-4" v-if="type == 'view'">
            <label>Min</label>
            <input type="number" min="0" class="form-control" v-model="options.min">
        </div>
        <div class="form-group col-md-4" v-if="type == 'view'">
            <label>Max</label>
            <input type="number" class="form-control" v-model="options.max">
        </div>
        <div class="form-group col-md-4" v-if="type == 'view'">
            <label>Step</label>
            <input type="number" class="form-control" v-model="options.step">
        </div>
    </div>
    <div v-else-if="show == 'relationship'">
        @{{ value }}
    </div>
    <div v-else>
        @{{ options.title }}
        <input type="number" class="form-control" :disabled="show == 'mockup'"
               :min="options.min"
               :max="options.max"
               :step="options.step"
               :placeholder="options.placeholder"
               :name="name"
               v-model="value">
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-number', {
    template: `@yield('number')`,
    props: ['show', 'options', 'type', 'name', 'input'],
    data: function() {
        return {
            'value': (this.input == '' ? options.default_value : this.input),
        }
    },
});
</script>
