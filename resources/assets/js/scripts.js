window.Vue = require('vue');

//Global events
const EventBus = new Vue();

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

//Vue Snotify
import Snotify from 'vue-snotify';
Vue.use(Snotify);

//Tooltip
import Tooltip from 'vue-directive-tooltip';
import 'vue-directive-tooltip/css/index.css';
Vue.use(Tooltip, {
    placement: 'bottom',
});

//Vue Draggable
import draggable from 'vuedraggable';
Vue.component('draggable', draggable);

//Vue Multi-Select
import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

//Vue Datatable
import {ServerTable, Event} from 'vue-tables-2';
Vue.use(ServerTable);

//Vue Datetime
import { Datetime } from 'vue-datetime';
Vue.component('datetime', Datetime);

//Vue Swatches
import Swatches from 'vue-swatches';
Vue.component('swatches', Swatches);

import VueSimplemde from 'vue-simplemde';
Vue.use(VueSimplemde);

import MaskedInput from 'vue-masked-input';
Vue.component('masked-input', MaskedInput);

const translatable = {
    data: function() {
        return {
            currentLocale: '',
            data: {},
            isTranslatable: false,
        };
    },
    methods: {
        setLocale: function(locale) {
            this.currentLocale = locale;
        },
        setInitialTranslation: function(value, locale, languages, translatable) {
            this.setLocale(locale);
            this.isTranslatable = translatable;
            if (!translatable) {
                this.data = value;
                return;
            }

            if (this.isJsonString(value)) {
                var data = JSON.parse(value);

                //Double quoted strings are "valid" JSON, workaround:
                if (typeof data === 'string') {
                    Vue.set(this.data, locale, value);
                } else {
                    this.data = data;
                }
            } else {
                //Input seems to be a normal string
                Vue.set(this.data, locale, value);
            }

            languages.forEach(function(lang) {
                if (this.data[lang] === undefined) {
                    Vue.set(this.data, lang, '');
                } else {
                    Vue.set(this.data, lang, this.data[lang]);
                }
            }, this);
        },
        isJsonString: function(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }
    },
    computed: {
        translate: {
            get() {
                if (this.isTranslatable) {
                    for (var lang in this.data) {
                        if (lang == this.currentLocale) {
                            return this.data[lang];
                        }
                    }
                    return '';
                } else {
                    return this.data;
                }
            },
            set(value) {
                if (this.isTranslatable) {
                    if (this.data[this.currentLocale] === undefined) {
                        //Vue.set(this.data, this.currentLocale, '');
                    }
                    this.data[this.currentLocale] = value;
                } else {
                    this.data = value;
                }
            }
        },
        translationString: {
            get() {
                if (this.isTranslatable) {
                    return JSON.stringify(this.data);
                }
                return this.data;
            }
        },
    },
    created: function() {
        this.$bus.$on('setLocale', (locale) => {
            this.setLocale(locale);
        });
    }
}
Vue.mixin(translatable);
