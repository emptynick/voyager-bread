$.fn.repeater = function(options) {

    var settings = $.extend({
        repeater		: '.repeater',
        repeaterItem	: '.repeater-item',
        repeaterDelete	: '.repeater-delete',
        repeaterAdd		: '.repeater-add',
    }, options);

    var repeater = $(this);

    var init = function() {
        nameOptions();

        //Add item
        repeater.on('click', settings.repeaterAdd, function(e) {
            e.preventDefault();
            var item = repeater.find(settings.repeaterItem).first().clone();
            item.find(':input').val('').prop('checked', false);
            //item.insertAfter($(this).closest(settings.repeater).find(settings.repeaterItem).last());
            item.insertAfter(repeater.find(settings.repeaterItem).last());
            nameOptions();
        });

        //Delete Item
        repeater.on('click', settings.repeaterDelete, function(e) {
            e.preventDefault();
            if (repeater.find(settings.repeaterItem).length >= 2) {
                $(this).closest(settings.repeaterItem).remove();
                nameOptions();
            }
        });
    };

    var nameOptions = function() {
        var regex = /(.*?)(\[[a-z0-9]*\])(\[[a-z0-9]*\])$/g;
        repeater.find(settings.repeaterItem).each(function(index) {
            $(this).find(':input').each(function(ind) {
                var name = $(this).attr('name');
                if (name === undefined)
                    return;
                name = name.replace(regex, '$1['+index+']$3');
                $(this).attr('name', name);
            });
        });
    };

    init();
    return this;
};
