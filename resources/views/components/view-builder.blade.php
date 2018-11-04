@section('view-builder')
<draggable v-model="layout.elements" :options="{ handle: '.voyager-handle', group: 'elements' }" style="min-height:100px; display:block">
    <div v-for="(element, id) in layout.elements" :key="id" :class="'col-md-'+element.width">
        <component
            :is="'base-'+element.group"
            :element="element"
            :show="'mockup'"
            :layout-type="'view'"
            :parent="self"
            :fields="fields"
            :id="id">
        </component>
    </div>
</draggable>
@endsection

<script>
Vue.component('view-builder', {
    template: `@yield('view-builder')`,
    props: ['parent', 'layout', 'fields'],
    data: function() {
        return {
            current_options: null,
            current_resize: null,
        };
    },
    computed: {
        self: function() {
            return this;
        }
    },
    methods: {
        isOptionsOpen: function (uid) {
            return (this.current_options == uid);
        },
        openOptions: function (uid) {
            if (this.isOptionsOpen(uid)) {
                this.current_options = null;
            } else {
                this.current_options = uid;
            }
        },
        deleteElement: function(id) {
            this.$snotify.confirm('Are you sure you want to delete this Element?', 'Delete Element?', {
                timeout: 5000,
                showProgressBar: true,
                closeOnClick: false,
                pauseOnHover: true,
                buttons: [{
                    text: 'Yes',
                    action: (toast) => {
                        this.layout.elements.splice(id, 1);
                        this.$snotify.remove(toast.id);
                    }, bold: false
                }, {
                    text: 'No',
                    action: (toast) => this.$snotify.remove(toast.id)
                }]
            });
        },
        startResize: function (id) {
            this.current_resize = id;
        },
        endResize: function () {
            this.current_resize = null;
        },
        resize: function(e) {
            if (this.current_resize !== null) {
                e.preventDefault();
                var maxWidth = this.$el.clientWidth;
                var relative = e.clientX - this.findPos(this.$el).left;
                var threshold = maxWidth / 12;
                var size = Math.min(Math.max(Math.round(relative / threshold), 2), 12);
                this.layout.elements[this.current_resize].width = size;
            }
        },
        findPos: function(obj) {
            var curleft = curtop = 0;
            if (obj.offsetParent) {
                do {
                    curleft += obj.offsetLeft;
                    curtop += obj.offsetTop;
                } while (obj = obj.offsetParent);
            }
            return {
                left : curleft,
                top : curtop
            };
        },
    },
    mounted: function() {
        window.addEventListener('mouseup', this.endResize);
        window.addEventListener('mousemove', this.resize);
    }
});
</script>
