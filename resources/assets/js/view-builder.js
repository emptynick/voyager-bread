(function($){

	$.fn.breadBuilder = function(options) {

		var builder = $(this);
		var draggable, droppable;
		var resizing = false;
		var resizingObj;
		var builderWidth;
		var liveUpdate = false;
		var currentOptionFormfield;

		var settings = $.extend({
			formfield             : '.formfield', //Single formfield inside builder
			formfieldContainer	  : '.formfield-container', //Container where the formfields can be dragged from
			mockupContainer		  : '.mockups', //Where the mockups are stored
			ignore				  : '.ignore', //Which items inside of builder should be ignored
			dragHandle			  : '.panel-heading', //
		}, options);

		var init = function() {
			reorderItems();
			builderWidth = setWidth();
			//Enable dragging from container to builder
			draggable = $(settings.formfieldContainer).sortable({
				sort: false,
				forceFallback: true,
				group: {
					name: 'draggable',
					pull: 'clone',
					put: false,
				}
			});

			//Enable dropping and sorting in builder
			droppable = $(builder).sortable({
				sort: true,
				animation: 150,
				forceFallback: true,
				draggable: settings.formfield,
				group: {
					name: 'droppable',
					pull: false,
					put: function(evt) {
						$(settings.ignore).addClass('hidden');
						return true;
					},
					filter: settings.ignore,
				},
				onAdd: function(evt) {
					var type = $(evt.item).data('type');
					var mockup = $(settings.mockupContainer).find("[data-type='" + type + "']").clone();
					$(evt.item).replaceWith(mockup);

					reorderItems();
				},
				onUpdate: function(evt) {
					reorderItems();
				},
			});

			//Options
			$(builder).on('click', '.cell', function() {
				var $field = $(this).closest(settings.formfield);
				var options = $field.find('.options');
				$('.options-container').html(options.html());
				copyOptions(options, $('.options-container'));
				currentOptionFormfield = $field;

				$('.live-update').trigger('change');

				$('.options-container .repeater').each(function() {
					$(this).repeater();
				});

                $('.options-container .toggler').toggles({
                    toggles: '.toggles'
                });

				$('#formfieldCollapse').collapse('hide');
				$('#relationshipCollapse').collapse('hide');
				$('#optionsCollapse').collapse('show');
			});

			$('.options-container').on('click', '.save-options', function(e) {
				e.preventDefault();
				//Load back html - used for repeater
				//currentOptionFormfield.find('.options').html($('.options-container').html());

				copyOptions($('.options-container'), currentOptionFormfield.find('.options'));
				applyOptions($('.options-container'), currentOptionFormfield);
				if (!liveUpdate) {
					toastr.success('Your options have been saved and applied.', 'Saved');
				}
			});

			$('.options-container').on('click, change', '.enables', function() {
				var options = $(this).closest('.options-container');
            	$(options).find($(this).data('enables')).prop('disabled', !$(this).prop('checked'));
			});

			$('.options-container').on('keyup', ':input:not(textarea)', function(e) {
				if(e.which == 13) {
					$('.options-container .save-options').trigger('click');
				}
			});

			$('.options-container').on('keyup change', ':input, :checkbox, :radio', function(e) {
				if(liveUpdate) {
					$('.options-container .save-options').trigger('click');
				}
				if ($(this).data('name') == 'checked') {
					var multiple = $(this).closest('.options-container').find('[data-name="multiple"]').prop('checked');
					if (!multiple) {
						//Uncheck all
						$(this).closest('.options-container').find('[data-name="checked"]').prop('checked', false);
						//Check $(this)
						$(this).prop('checked', true);
					}
				}
			});

			//Delete click
			$(builder).on('click', '.cell-delete', function(e) {
				e.preventDefault();
				var cell = $(this).closest(settings.formfield);
				toastr.info('Do you really want to delete this Formfield?<br /><br /><button type="button" class="btn btn-danger final-delete">Yes</button>', 'Delete Formfield?');
				toastr.options = {
					'escapeHtml': false,
				};
				$('body').on('click', '.final-delete', function(e) {
					e.preventDefault();
					$(cell).remove();
					if ($(builder).find('.cell').length == 0) {
						$(settings.ignore).removeClass('hidden');
					}
				});

				return false;
			});

			//Resize
			$(builder).on('mousedown', '.cell-resize', function() {
				resizing = true;
				resizingObj = $(this).closest('.cell');
			});
			$(window).on('mouseup', function() {
				resizing = false;
				resizingObj = null;
			});
			$(builder).on('mousemove', function(e) {
				if (resizing && resizingObj !== null) {
					e.preventDefault();
					var x = e.pageX - $(this).offset().left;
					var onewidth = builderWidth / 12;
					var width = Math.round(x / onewidth);
					if (width < 2 || width > 12) {
						return;
					}
					for (i = 1; i <= 12; i++) {
						$(resizingObj).removeClass('col-md-'+i);
					}
					$(resizingObj).addClass('col-md-'+width);
					$(resizingObj).find('.row-width').val(width);

					return false;
				}
			});
			$(window).on('resize', function() {
				builderWidth = setWidth();
			});

            //Live update
			$('.live-update').on('change', function() {
				liveUpdate = $(this).prop('checked');
				if (liveUpdate) {
					$('.options-container .save-options').trigger('click')
														 .removeClass('btn-success');
				} else {
					$('.options-container .save-options').addClass('btn-success');
				}
			});
		};

		var reorderItems = function() {
			var regex = /(.*?)(\[\d*\])(?!\[\d+?\])(.*)/g;
			builder.find(settings.formfield).each(function(index){
				var $formfield = $(this);
				$formfield.find(':input').each(function() { //Add selects and textarea
					$(this).attr('name', function(i, name) {
						if (name === undefined) return true;
						return name.replace(regex, function replacer(match, p1, p2, p3, offset, string){
							return p1 + '[' + index + ']' + p3;
						});
					});
				});
			});
		};

		var copyOptions = function(from, to) {

			//Touch every value from every input
			from.find(':input').each(function() {
			    $(this).attr('value', $(this).val());

			});

			//Force set selected="true"
			from.find('select').each(function() {
				var val = $(this).val();
			    $(this).find('option').attr('selected', false);
				$(this).find('option[value='+val+']').attr('selected', true);
			});

			from.find(':checkbox, :radio').each(function() {
				$(this).attr('checked', $(this).is(':checked'));
			});

			$(to).html($(from).clone().html());
		};

		var applyOptions = function(options, formfield) {
			var $content = formfield.find('.content');
			options.find(':input, :checkbox, :radio').each(function() {
				var $el = $(this);
				var name = $el.data('name');
				var value = $el.val();

				switch (name)
				{
					case 'placeholder':
						$content.find(':input').attr('placeholder', value);
						break;
					case 'value':
						$content.find(':input').val(value);
						break;
					case 'label':
						$content.find('label').html(value);
						break;
					case 'helptext':
						$content.find('small').html(value);
						break;
					case 'rows':
						$content.find('textarea').attr('rows', value);
						break;
					case 'cols':
						$content.find('textarea').attr('cols', value);
						break;
					case 'multiple':
						$content.find('select').prop('multiple', $el.prop('checked'));
						if (!$el.prop('checked')) {
							$el.closest('.options-container').find('[data-name="checked"]').prop('checked', false);
						}
						break;
					case 'checked':
						//
						break;
					case 'heading':
						$content.find('h1, h2, h3, h4, h5, h6').replaceWith(function(){
						    return $('<'+value+' />', { html: $(this).html() });
						});
						break;
					case 'html':
						$content.find('h1, h2, h3, h4, h5, h6, p').html(value);
						break;
				}
			});

			/*if (options.has('.repeater')) {
				$content.find('select').find('option').remove();
				options.find('.repeater-item').each(function() {
					var key = $(this).find('[data-name="key"]').val();
					var text = $(this).find('[data-name="value"]').val();
					var checked = $(this).find('[data-name="checked"]').prop('checked');
					$content.find('select').append($('<option>', {
						value: key,
						text : text,
						selected: checked,
					}));
				});
			}*/
		};

		var setWidth = function() {
			return $(builder).innerWidth();
		};

		init();
		return this;
	};

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
			repeater.on('click', settings.repeaterAdd, function() {
				console.log(repeater);
				var item = repeater.find(settings.repeaterItem).first().clone();
				item.find(':input').val('').prop('checked', false);
				//item.insertAfter($(this).closest(settings.repeater).find(settings.repeaterItem).last());
				item.insertAfter(repeater.find(settings.repeaterItem).last());
				nameOptions();
			});

			//Delete Item
			repeater.on('click', settings.repeaterDelete, function() {
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

    $.fn.toggles = function(options) {

        var settings = $.extend({
			toggles:      '.toggles',
            upperElement: '.options-container',
		}, options);

        var toggler = $(this);

		var init = function() {
			$(toggler).on('change', function() {
                exec($(this));
            });

            exec($(toggler));
		};

        var exec = function(el) {
            var option = $(el).find('option:selected');
            var shows = $(option).data('bread');
            var parent = $(el).closest(settings.upperElement);

            parent.find(settings.toggles).addClass('hidden');
            parent.find($(settings.toggles+'[data-bread="'+shows+'"]')).removeClass('hidden');
            parent.find('.bread_name').html($(option).data('name'));
        }

		init();
		return this;
	};
}(jQuery));
