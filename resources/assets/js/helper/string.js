Vue.prototype.slugify = require('slugly');

Vue.prototype.kebab_case = function (input)
{
    return input.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase();
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

Vue.filter('uppercase', function (value) {
    return value.toUpperCase();
});

Vue.directive('default', function (el, binding, vnode) {
    vnode.context.$nextTick(function () {
        if (!binding.value) return;
        if (el.getAttribute('data-touched')) return;
        if (el.value || el.value === binding.value) {
            el.setAttribute('data-touched', true)
            return;
        }

        el.value = binding.value;

        var model = vnode.data.directives.find(dir => dir.rawName === 'v-model');
        if (!model || !model.expression) return;
        vnode.context.$watch(model.expression, () => el.setAttribute('data-touched', true));
    });
});