window.Vue = require('vue');

//Global events
const EventBus = new Vue()

Object.defineProperties(Vue.prototype, {
    $bus: {
        get: function () {
            return EventBus
        }
    }
});

// Vue resources
import VueResource from 'vue-resource';
Vue.use(VueResource);

//Tooltip
import Tooltip from 'vue-directive-tooltip';
import 'vue-directive-tooltip/css/index.css';
Vue.use(Tooltip, {
    placement: 'right',
});

//Responsive Grid Layout
import {VueResponsiveGridLayout, VueGridItem } from 'vue-responsive-grid-layout'

Vue.component('vue-responsive-grid-layout', VueResponsiveGridLayout)
Vue.component('vue-grid-item', VueGridItem)

//Vue Draggable
import draggable from 'vuedraggable'
Vue.component('draggable', draggable)

//Vue Multi-Select
import Multiselect from 'vue-multiselect'
Vue.component('multiselect', Multiselect)

//Vue Snotify
import Snotify from 'vue-snotify';
Vue.use(Snotify)

//Slugify
Vue.filter('slugify', function(value) {
  value = value.replace(/^\s+|\s+$/g, ''); // trim
  value = value.toLowerCase();

  // remove accents, swap ñ for n, etc
  var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
  var to   = "aaaaaeeeeeiiiiooooouuuunc------";
  for (var i=0, l=from.length ; i<l ; i++) {
    value = value.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
  }

  value = value.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '-') // collapse whitespace and replace by -
    .replace(/-+/g, '-'); // collapse dashes

  return value;
});

//Swatches
import Swatches from 'vue-swatches'
import "vue-swatches/dist/vue-swatches.min.css"
Vue.component('vue-swatches', Swatches)

//Quill Editor
import { VueEditor, Quill } from 'vue2-editor'
Vue.component('vue-editor', VueEditor)

// Formfields

// Masked input
import MaskedInput from 'vue-masked-input'
Vue.component('masked-input', MaskedInput);
