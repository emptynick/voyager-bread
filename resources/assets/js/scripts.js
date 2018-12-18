window.Vue = require('vue');

//Components

//Manager
Vue.component('manager-browse', require('./components/Manager/Browse.vue'));
Vue.component('manager-edit-add', require('./components/Manager/EditAdd.vue'));

//Bread
Vue.component('bread-browse', require('./components/Bread/Browse.vue'));
Vue.component('bread-edit-add', require('./components/Bread/EditAdd.vue'));

//Formfields
Vue.component('base-formfield', require('./components/Formfields/BaseFormfield.vue'));
