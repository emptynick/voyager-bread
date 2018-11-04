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

//Lodash
//Todo: This is only used for debounce and takes ~550kb
import _ from 'lodash';

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

//Vue-JS-Modal
import VModal from 'vue-js-modal';
Vue.use(VModal);

//Vue TipTap
import { Editor } from 'tiptap';
Vue.component('tiptap', Editor);
import {
  // Nodes
  BlockquoteNode,
  BulletListNode,
  CodeBlockNode,
  CodeBlockHighlightNode,
  HardBreakNode,
  HeadingNode,
  ImageNode,
  ListItemNode,
  OrderedListNode,
  TodoItemNode,
  TodoListNode,

  // Marks
  BoldMark,
  CodeMark,
  ItalicMark,
  LinkMark,
  StrikeMark,
  UnderlineMark,

  // General Extensions
  HistoryExtension,
  PlaceholderExtension,
} from 'tiptap-extensions';

import VueSimplemde from 'vue-simplemde';
Vue.use(VueSimplemde);

import MaskedInput from 'vue-masked-input';
Vue.component('masked-input', MaskedInput);

const helper = {
    data: function() {
        return {
            locale: null,
        };
    },
    methods: {
        getUrl: function() {
            var url = arguments[0];
            for (var i = 1; i < arguments.length; i++) {
                url = url.replace('#', arguments[i]);
            }
            return url;
        },
        translate: function(input) {
            if (input) {
                if (typeof input == 'string') {
                    try {
                        var json = JSON.parse(input);
                        input = json;
                    } catch (err) {
                        return input;
                    }
                }
                return input[this.locale];
            }
            return '';
        },
        slugify: function(input) {
            //Todo: implement slugify
            return input;
        }
    },
    created: function() {
        this.$bus.$on('setLocale', (locale) => {
            this.locale = locale;
        });
    }
}
Vue.mixin(helper);
