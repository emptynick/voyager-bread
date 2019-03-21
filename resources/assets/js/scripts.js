window.Vue = require('vue');

var draggable = require('vuedraggable');
Vue.component('draggable', draggable);

import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

import Popper from 'vue-popperjs';
Vue.component('popper', Popper);

var VueResource = require('vue-resource');
Vue.use(VueResource);

Vue.prototype.$eventHub = new Vue({
    data: function () {
        return {
            locale: '',
            locales: [],
            translatable: false,
        };
    }
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
import BelongsTo from './components/Formfields/Relationships/BelongsTo';
Vue.component('formfield-belongsto', BelongsTo);
import BelongsToMany from './components/Formfields/Relationships/BelongsToMany';
Vue.component('formfield-belongstomany', BelongsToMany);
import HasOne from './components/Formfields/Relationships/HasOne';
Vue.component('formfield-hasone', HasOne);
import HasMany from './components/Formfields/Relationships/HasMany';
Vue.component('formfield-hasmany', HasMany);

Vue.prototype.getUrl = function (url)
{
    for (var i = 1; i < arguments.length; i++) {
        url = url.replace('#', arguments[i]);
    }

    return url;
}

Vue.prototype.getTranslation = function (input)
{
    if (typeof input === 'object') {
        return input[Vue.prototype.$eventHub.locale];
    }

    return input;
}

Vue.prototype.trans = function (key, replace = {})
{
    let translation = key.split('.').reduce((t, i) => t[i] || null, Vue.prototype.$eventHub.translations);

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

Vue.prototype.slugify = require('slugly');

Vue.filter('uppercase', function (value) {
    return value.toUpperCase();
});