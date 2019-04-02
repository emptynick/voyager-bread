let formfields = [
    'Text',
    'Number',
    'Color',
    'MaskedInput',
];

let bread_components = [
    'BrowseTable',
    'Builder'
];

let helper = [
    'i18n',
    'string',
    'url'
];

require('./vendor');

import LanguageInput from '../components/LanguageInput';
Vue.component('language-input', LanguageInput);

import LanguagePicker from '../components/LanguagePicker';
Vue.component('language-picker', LanguagePicker);

import ValidationInput from '../components/ValidationInput';
Vue.component('validation-input', ValidationInput);

import Pagination from '../components/Pagination';
Vue.component('pagination', Pagination);

// Helper
helper.forEach(function (helper) {
    require('./helper/'+helper);
});

// Formfields
import BaseFormfield from '../components/Formfields/BaseFormfield';
Vue.component('formfield-base', BaseFormfield);

formfields.forEach(function (formfield) {
    Vue.component('formfield-'+Vue.prototype.kebab_case(formfield), require('../components/Formfields/'+formfield).default);
});

// BREAD components
bread_components.forEach(function (comp) {
    Vue.component(Vue.prototype.kebab_case(comp), require('../components/Bread/'+comp).default);
});