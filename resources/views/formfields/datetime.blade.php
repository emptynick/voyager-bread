@section('datetime')
<div>
    <div v-if="show == 'options'">
        <div class="form-group" v-if="options.range">
            <label>Field 2</label>
            <select class="form-control" v-model="options.field2">
                <option v-for="field in fields">
                    @{{ field }}
                </option>
            </select>
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Type</label>
            <select class="form-control" v-model="options.type">
                <option value="date">Date</option>
                <option value="datetime">Date & Time</option>
            </select>
        </div>
        <div class="checkbox">
            <label><input type="checkbox" v-model="options.range" value="true">Range</label>
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
    </div>
    <div v-else>
        @{{ options.title }}
        <div :class="'col-md-'+(options.range ? 6 : 12)">
            <datetime v-model="date" :type="options.type" input-class="form-control"></datetime>
        </div>
        <div class="col-md-6" v-if="options.range">
            <datetime v-model="date2" :min-datetime="date" :type="options.type" input-class="form-control"></datetime>
        </div>
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-datetime', {
    template: `@yield('datetime')`,
    data: function() {
        return {
            //Todo: split input to both dates
            date: '',
            date2: ''
        };
    },
    props: ['show', 'options', 'type', 'fields', 'name', 'input']
});
</script>
