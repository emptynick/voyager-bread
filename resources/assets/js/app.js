window.Vue = require('vue');

//Tooltip
import Tooltip from 'vue-directive-tooltip';
import 'vue-directive-tooltip/css/index.css';
Vue.use(Tooltip, {
    placement: 'right',
});

//Grid Layout
import {GridItem, GridLayout,ResponsiveGridLayout} from 'vue-grid-layout';
[GridItem, GridLayout, ResponsiveGridLayout].map(
  component => {
  Vue.component(component.name, component);
});

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
