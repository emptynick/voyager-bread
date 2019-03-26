window.Vue = require('vue');

var draggable = require('vuedraggable');
Vue.component('draggable', draggable);

import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

import Popper from 'vue-popperjs';
Vue.component('popper', Popper);

import VueGoodTablePlugin from 'vue-good-table';
Vue.use(VueGoodTablePlugin);

var VueResource = require('vue-resource');
Vue.use(VueResource);

var Swatches = require('vue-swatches');
Vue.component('swatches', Swatches.default);

Vue.prototype.$eventHub = new Vue({
    data: function () {
        return {
            locale: '',
            initialLocale: null,
            locales: [],
            translatable: false,
        };
    },
});

import LanguageInput from './components/LanguageInput';
Vue.component('language-input', LanguageInput);

import LanguagePicker from './components/LanguagePicker';
Vue.component('language-picker', LanguagePicker);

import ValidationInput from './components/ValidationInput';
Vue.component('validation-input', ValidationInput);

// Formfields
import BaseFormfield from './components/Formfields/BaseFormfield';
Vue.component('formfield-base', BaseFormfield);

import Text from './components/Formfields/Text';
Vue.component('formfield-text', Text);
import Number from './components/Formfields/Number';
Vue.component('formfield-number', Number);
import Color from './components/Formfields/Color';
Vue.component('formfield-color', Color);

Vue.prototype.getUrl = function (url)
{
    for (var i = 1; i < arguments.length; i++) {
        url = url.replace('#', arguments[i]);
    }

    return url;
}

Vue.prototype.getTranslation = function (input, initial = false)
{
    if (typeof input === 'object') {
        if (initial) {
            return input[Vue.prototype.$eventHub.initialLocale];
        } else {
            return input[Vue.prototype.$eventHub.locale];
        }
    }

    return input;
}

Vue.prototype.trans = function (key, replace = {})
{
    var translations = Vue.prototype.$eventHub.translations;
    let translation = key.split('.').reduce((t, i) => t[i] || null, translations);

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }

    return translation || key;
}

Vue.prototype.__ = function (key, replace = {})
{
    return this.trans(key, replace);
}

Vue.prototype.trans_choice = function (key, count = 1, replace = {})
{
    let translation = key.split('.').reduce((t, i) => t[i] || null, Vue.prototype.$eventHub.translations).split('|');

    translation = count > 1 ? translation[1] : translation[0];

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }

    return translation;
}

Vue.prototype.number_format = function (number, decimals, dec_point, thousands_sep)
{
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

Vue.prototype.slugify = require('slugly');

Vue.filter('uppercase', function (value) {
    return value.toUpperCase();
});