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