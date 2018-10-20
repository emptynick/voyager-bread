@section('datetime')
<div>
    <div v-if="show == 'options'">
        <div class="form-group">
            <label>Type</label>
            <select class="form-control" v-model="options.type">
                <option value="date">Date</option>
                <option value="datetime">Date & Time</option>
                <option value="time">Time</option>
            </select>
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Title</label>
            <input type="text" class="form-control" v-model="options.title">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Help text</label>
            <input type="text" class="form-control" v-model="options.help_text">
        </div>
        <div class="form-group" v-if="type == 'view'">
            <label>Minimum from</label>
            <select class="form-control" v-model="options.min_from">
                <option value="">None</option>
                <option v-for="field in fields">
                    @{{ field }}
                </option>
            </select>
        </div>
    </div>
    <div v-else-if="show == 'relationship' || show == 'browse'">
        @{{ getParsedDateTime() }}
    </div>
    <div v-else>
        @{{ options.title }}
        <br v-if="options.title.length > 0">
        <datetime
            v-model="date"
            :type="options.type"
            :disabled="show == 'mockup'"
            :min-datetime="this.minDateTime"
            :phrases="{ ok: 'Continue', cancel: 'Exit' }"
            input-class="form-control"
        ></datetime>
        <input type="hidden" :name="name" v-model="date">
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection

<script>
Vue.component('formfield-datetime', {
    template: `@yield('datetime')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input'],
    data: function () {
        return {
            minDateTime: null,
            date: (this.input ? this.toISOLocal(new Date(this.input), this.input) : ''),
        };
    },
    watch: {
        date: function (newVal, oldVal) {
            this.$bus.$emit(this.name+'_change', newVal, oldVal);
        }
    },
    mounted: function() {
        this.$bus.$on(this.options.min_from+'_change', (newVal, oldVal) => {
            if (this.options.min_from != '' && typeof oldVal === 'string') {
                this.minDateTime = newVal;
            }
        });
    },
    methods: {
        toISOLocal(d, input) {
            //var ddd = LuxonDateTime.local();
            if (input.endsWith('Z')) {
                return input;
            }
            if (this.options.type == 'time') {
                d = new Date('1970-01-01T' + input + 'Z');
            }
            var z = n => (n < 10 ? '0' : '') + n;

            return d.getFullYear() + '-' + z(d.getMonth()+1) + '-' +
            z(d.getDate()) + 'T' + z(d.getHours()) + ':'  + z(d.getMinutes()) +
            ':' + z(d.getSeconds())+'.000Z';
        },
        getParsedDateTime() {
            var d = new Date(this.date);
            if (this.options.type == 'time') {
                return d.getHours()+':'+d.getMinutes();
            } else if (this.options.type == 'date') {
                return d.toLocaleDateString();
            }
            return d.toLocaleString();
        }
    }
});
</script>
