@section('coordinates')
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
    </div>
    <div v-else-if="show == 'read'">
        @{{ options.title }}
        <br>
        @{{ value }}
    </div>
    <div v-else-if="show == 'relationship'">
        @{{ translate }}
    </div>
    <div v-else>
        <label v-if="options.title.length > 0">@{{ options.title }}</label>
        <div ref="map"></div>
        <small v-if="options.help_text.length > 0">@{{ options.help_text }}</small>
    </div>
</div>
@endsection
<!--https://medium.com/founders-factory/building-a-custom-google-map-component-with-vue-js-d1c01ddd0b0a-->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBttGFf_yYcUqVWI9kRVW_UtTnXXzNYIyA"></script>
<script>
Vue.component('formfield-coordinates', {
    template: `@yield('coordinates')`,
    props: ['show', 'options', 'type', 'fields', 'name', 'input'],
    data: function() {
        return {
            data: function () {
                return {
                    mapName: this.name + "-map",
                    markerCoordinates: [{
                        latitude: 51.501527,
                        longitude: -0.1921837
                    }, {
                        latitude: 51.505874,
                        longitude: -0.1838486
                    }, {
                        latitude: 51.4998973,
                        longitude: -0.202432
                    }]
                }
            }
        };
    },
    mounted: function () {
        const element = document.getElementById(this.mapName)
        const options = {
            zoom: 14,
            center: new google.maps.LatLng(51.501527,-0.1921837)
        }
        const map = new google.maps.Map(element, options);
    }
});
</script>
