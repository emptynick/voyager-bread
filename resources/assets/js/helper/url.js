Vue.prototype.getUrl = function (url)
{
    for (var i = 1; i < arguments.length; i++) {
        url = url.replace('#', arguments[i]);
    }

    return url;
}