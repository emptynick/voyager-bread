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
            if (locale !== null)
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
                if (typeof data !== 'object') {
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
        },
        translated: function(input) {
            if (this.isJsonString(input)) {
                var data = JSON.parse(input);
                return data[this.currentLocale];
            }

            return input;
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
            },
            set(value) {
                console.log(value);
                this.data = value;
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

//Todo: this should be at tcg/voyager
//Todo: move char-array to a data() function
const Slugify = {
    install(Vue, options) {
        Vue.slugify = function(str, sep = '-') {
            var chars =
            {
                //Latin
                'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE', 'Ç':
                'C', 'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ì': 'I', 'Í': 'I', 'Î': 'I',
                'Ï': 'I', 'Ð': 'D', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O', 'Õ': 'O', 'Ö':
                'O', 'Ő': 'O', 'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'U', 'Ű': 'U',
                'Ý': 'Y', 'Þ': 'TH', 'ß': 'ss', 'à':'a', 'á':'a', 'â': 'a', 'ã': 'a', 'ä':
                'a', 'å': 'a', 'æ': 'ae', 'ç': 'c', 'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e',
                'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i', 'ð': 'd', 'ñ': 'n', 'ò': 'o', 'ó':
                'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ő': 'o', 'ø': 'o', 'ù': 'u', 'ú': 'u',
                'û': 'u', 'ü': 'u', 'ű': 'u', 'ý': 'y', 'þ': 'th', 'ÿ': 'y',
                //Arabic
                'ا': 'a', 'أ': 'a', 'إ': 'i', 'آ': 'aa', 'ؤ': 'u', 'ئ': 'e', 'ء': 'a',
                'ب': 'b', 'ت': 't', 'ث': 'th', 'ج': 'j', 'ح': 'h', 'خ': 'kh', 'د': 'd',
                'ذ': 'th', 'ر': 'r', 'ز': 'z', 'س': 's', 'ش': 'sh', 'ص': 's', 'ض': 'dh',
                'ط': 't', 'ظ': 'z', 'ع': 'a', 'غ': 'gh', 'ف': 'f', 'ق': 'q', 'ك': 'k',
                'ل': 'l', 'م': 'm', 'ن': 'n', 'ه': 'h', 'و': 'w', 'ي': 'y', 'ى': 'a',
                'ة': 'h', 'ﻻ': 'la', 'ﻷ': 'laa', 'ﻹ': 'lai', 'ﻵ': 'laa',
                //Greek
                'α':'a', 'β':'b', 'γ':'g', 'δ':'d', 'ε':'e', 'ζ':'z', 'η':'h', 'θ':'8',
                'ι':'i', 'κ':'k', 'λ':'l', 'μ':'m', 'ν':'n', 'ξ':'3', 'ο':'o', 'π':'p',
                'ρ':'r', 'σ':'s', 'τ':'t', 'υ':'y', 'φ':'f', 'χ':'x', 'ψ':'ps', 'ω':'w',
                'ά':'a', 'έ':'e', 'ί':'i', 'ό':'o', 'ύ':'y', 'ή':'h', 'ώ':'w', 'ς':'s',
                'ϊ':'i', 'ΰ':'y', 'ϋ':'y', 'ΐ':'i',
                'Α':'A', 'Β':'B', 'Γ':'G', 'Δ':'D', 'Ε':'E', 'Ζ':'Z', 'Η':'H', 'Θ':'8',
                'Ι':'I', 'Κ':'K', 'Λ':'L', 'Μ':'M', 'Ν':'N', 'Ξ':'3', 'Ο':'O', 'Π':'P',
                'Ρ':'R', 'Σ':'S', 'Τ':'T', 'Υ':'Y', 'Φ':'F', 'Χ':'X', 'Ψ':'PS', 'Ω':'W',
                'Ά':'A', 'Έ':'E', 'Ί':'I', 'Ό':'O', 'Ύ':'Y', 'Ή':'H', 'Ώ':'W', 'Ϊ':'I',
                'Ϋ':'Y',
                //Turkish
                'ş':'s', 'Ş':'S', 'ı':'i', 'İ':'I', 'ç':'c', 'Ç':'C', 'ü':'u', 'Ü':'U',
                'ö':'o', 'Ö':'O', 'ğ':'g', 'Ğ':'G',
                //Russian
                'а':'a', 'б':'b', 'в':'v', 'г':'g', 'д':'d', 'е':'e', 'ё':'yo', 'ж':'zh',
                'з':'z', 'и':'i', 'й':'j', 'к':'k', 'л':'l', 'м':'m', 'н':'n', 'о':'o',
                'п':'p', 'р':'r', 'с':'s', 'т':'t', 'у':'u', 'ф':'f', 'х':'h', 'ц':'c',
                'ч':'ch', 'ш':'sh', 'щ':'sh', 'ъ':'', 'ы':'y', 'ь':'', 'э':'e', 'ю':'yu',
                'я':'ya',
                'А':'A', 'Б':'B', 'В':'V', 'Г':'G', 'Д':'D', 'Е':'E', 'Ё':'Yo', 'Ж':'Zh',
                'З':'Z', 'И':'I', 'Й':'J', 'К':'K', 'Л':'L', 'М':'M', 'Н':'N', 'О':'O',
                'П':'P', 'Р':'R', 'С':'S', 'Т':'T', 'У':'U', 'Ф':'F', 'Х':'H', 'Ц':'C',
                'Ч':'Ch', 'Ш':'Sh', 'Щ':'Sh', 'Ъ':'', 'Ы':'Y', 'Ь':'', 'Э':'E', 'Ю':'Yu',
                'Я':'Ya',
                //Ukranian
                'Є':'Ye', 'І':'I', 'Ї':'Yi', 'Ґ':'G', 'є':'ye', 'і':'i', 'ї':'yi', 'ґ':'g',
                //Czech
                'č':'c', 'ď':'d', 'ě':'e', 'ň': 'n', 'ř':'r', 'š':'s', 'ť':'t', 'ů':'u',
                'ž':'z', 'Č':'C', 'Ď':'D', 'Ě':'E', 'Ň': 'N', 'Ř':'R', 'Š':'S', 'Ť':'T',
                'Ů':'U', 'Ž':'Z',
                //Polish
                'ą':'a', 'ć':'c', 'ę':'e', 'ł':'l', 'ń':'n', 'ó':'o', 'ś':'s', 'ź':'z',
                'ż':'z', 'Ą':'A', 'Ć':'C', 'Ę':'e', 'Ł':'L', 'Ń':'N', 'Ó':'o', 'Ś':'S',
                'Ź':'Z', 'Ż':'Z',
                //Vietnamese
                'ạ': 'a','ả': 'a','ầ': 'a','ấ': 'a','ậ': 'a','ẩ': 'a','ẫ': 'a','ằ': 'a',
                'ắ': 'a','ặ': 'a','ẳ': 'a','ẵ': 'a','ẹ': 'e','ẻ': 'e','ẽ': 'e','ề': 'e',
                'ế': 'e','ệ': 'e','ể': 'e','ễ': 'e','ị': 'i','ỉ': 'i','ọ': 'o','ỏ': 'o',
                'ồ': 'o','ố': 'o','ộ': 'o','ổ': 'o','ỗ': 'o','ờ': 'o','ớ': 'o','ợ': 'o',
                'ở': 'o','ỡ': 'o','ụ': 'u','ủ': 'u','ừ': 'u','ứ': 'u','ự': 'u','ử': 'u',
                'ữ': 'u','ỳ': 'y','ỵ': 'y','ỷ': 'y','ỹ': 'y','Ạ': 'A','Ả': 'A','Ầ': 'A',
                'Ấ': 'A','Ậ': 'A','Ẩ': 'A','Ẫ': 'A','Ằ': 'A','Ắ': 'A','Ặ': 'A','Ẳ': 'A',
                'Ẵ': 'A','Ẹ': 'E','Ẻ': 'E','Ẽ': 'E','Ề': 'E','Ế': 'E','Ệ': 'E','Ể': 'E',
                'Ễ': 'E','Ị': 'I','Ỉ': 'I','Ọ': 'O','Ỏ': 'O','Ồ': 'O','Ố': 'O','Ộ': 'O',
                'Ổ': 'O','Ỗ': 'O','Ờ': 'O','Ớ': 'O','Ợ': 'O','Ở': 'O','Ỡ': 'O','Ụ': 'U',
                'Ủ': 'U','Ừ': 'U','Ứ': 'U','Ự': 'U','Ử': 'U','Ữ': 'U','Ỳ': 'Y','Ỵ': 'Y',
                'đ': 'd','Đ': 'D','Ỷ': 'Y','Ỹ': 'Y',
                //Latvian
                'ā':'a', 'č':'c', 'ē':'e', 'ģ':'g', 'ī':'i', 'ķ':'k', 'ļ':'l', 'ņ':'n',
                'š':'s', 'ū':'u', 'ž':'z', 'Ā':'A', 'Č':'C', 'Ē':'E', 'Ģ':'G', 'Ī':'i',
                'Ķ':'k', 'Ļ':'L', 'Ņ':'N', 'Š':'S', 'Ū':'u', 'Ž':'Z',
                //Currency
                '€': 'euro', '$': 'dollar', '₢': 'cruzeiro', '₣': 'french franc', '£': 'pound',
                '₤': 'lira', '₥': 'mill', '₦': 'naira', '₧': 'peseta', '₨': 'rupee',
                '₩': 'won', '₪': 'new shequel', '₫': 'dong', '₭': 'kip', '₮': 'tugrik',
                '₯': 'drachma', '₰': 'penny', '₱': 'peso', '₲': 'guarani', '₳': 'austral',
                '₴': 'hryvnia', '₵': 'cedi', '¢': 'cent', '¥': 'yen', '元': 'yuan',
                '円': 'yen', '﷼': 'rial', '₠': 'ecu', '¤': 'currency', '฿': 'baht',
                //Symbols
                '©':'(c)', 'œ': 'oe', 'Œ': 'OE', '∑': 'sum', '®': '(r)', '†': '+',
                '“': '"', '”': '"', '‘': "'", '’': "'", '∂': 'd', 'ƒ': 'f', '™': 'tm',
                '℠': 'sm', '…': '...', '˚': 'o', 'º': 'o', 'ª': 'a', '•': '*',
                '∆': 'delta', '∞': 'infinity', '♥': 'love', '&': 'and',
            };

            str = str.toString().toLowerCase();

            var _slug = '';
            for (var i = 0, l = str.length; i < l; i++) {
                _slug += (chars[str.charAt(i)]) ? chars[str.charAt(i)] : str.charAt(i);
            }

            str = _slug
            .replace(/[^a-z0-9]/g, sep)
            .replace(new RegExp('\\'+sep+'\\'+sep+'+', 'g'), sep)
            .replace(new RegExp('^\\'+sep+'+|\\'+sep+'+$', 'g'), '');

            return str;
        }
    }
};

export default Slugify;
Vue.use(Slugify);
