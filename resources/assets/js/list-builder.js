(function($){

	$.fn.listBuilder = function(options) {

		var $obj = $(this);
		var settings = $.extend({
			row                   : 'tr',
			rows                  : 'tbody',
			minrows               : 1,
			copyRow               : null,
			copyValues            : false,
			increment             : null,
			handle_add            : '[data-add]:not(.disabled)',
			handle_remove         : '[data-remove]:not(.disabled)',
			handle_move           : '[data-move]',
			index_start           : 0,
		}, options);

		var data = $obj.data();
		if (typeof data == 'object') {
			$.each(data, function(key, value) {
				if (typeof settings[ key ] != 'undefined') {
					if (typeof value == 'string' && value.length == 0) { value = true; }
					settings[ key ] = value;
				}
			});
		}

		settings.rows = $(settings.rows, this);

		if (settings.handle_add) {
			$obj.on('click', settings.handle_add, function(obj) {
				obj.preventDefault();
				addRow(this);
			});
		}

		if (settings.handle_remove) {
			$obj.on('click', settings.handle_remove, function(obj) {
				obj.preventDefault();
				removeRow(this);
			});
		}

		if (settings.handle_move && $(settings.handle_move, settings.rows).length > 0) {

			var el = $(settings.rows, this)[0];

			$(settings.handle_move, settings.rows).each(function() {
				var $row = $(this).closest(settings.row);
				$row.addClass('listBuilder-row');
				$row.find(settings.handle_move).addClass('listBuilder-move');
			});

			var sortable = $(el).sortable({
				handle: '.listBuilder-move',
				draggable: '.listBuilder-row',
				onUpdate: function(event){
					updateFormNames();
				}
			});
		}

		function addRow(handle) {
			var row = $(handle).closest(settings.row);
			if (settings.copyRow) {
				var row_new = $(handle).closest(settings.rows).find(settings.row + ':nth-child(' + settings.copyRow + ')').clone(true);
			} else {
				var row_new = row.clone(true);
			}
			if (row_new.length == 0) { return false; }
			cleanFormElems(row_new, true);
			if (settings.copyValues) {
				copyFormElemsValues(row, row_new);
			}
			if ($.fn.datepick) { $('input.datepicker', row_new).datepick('destroy').datepick(); }
			row_new.insertAfter(row);
			updateFormNames();
		}

		function removeRow(handle) {
			var row = $(handle).closest(settings.row);
			var rows = $(row).closest(settings.rows);
			var rows_count = $('> ' + settings.row, rows).length;
			if (rows_count > settings.minrows) {
				row.remove();
				updateFormNames();
				row = null;
			}
			else {
				cleanFormElems(row);
			}
		}

		function updateFormNames() {
			var name_regex = /(.*?)(\[\d+?\])(?!\[\d+?\])(.*)/g;
			var current_index = settings.index_start - 1;
			$('> ' + settings.row, settings.rows).each(function(){
				var $row = $(this);
				current_index++;
				$row.find(':input').each(function() {
					$(this).attr('name', function(i, name) {
						if (name === undefined) return true;
						return name.replace(name_regex, function replacer(match, p1, p2, p3, offset, string){
							return p1 + '[' + current_index + ']' + p3;
						});
					}).removeAttr('id');
				});
				if (settings.increment) {
					$row.find(settings.increment).html( current_index + 1 );
				}
			});
		}

		function copyFormElemsValues(row, row_new){
			var root = this;
			row.find(':text, textarea, select:not(multiple)').each(function() {
				var $el = $(this);
				var name = $el.attr('name');
				row_new.find('[name="' + name + '"]').val( $el.val() );
			});
			row.find(':checkbox, :radio').each(function() {
				var $el = $(this);
				var name = $el.attr('name');
				row_new.find('[name="' + name + '"]').prop('checked', $el.prop('checked') );
			});
		}

		function cleanFormElems(row, copy){
			var root = this;
			$('.disabled', row).removeClass('disabled');
			$('input[type="hidden"]', row).val('');
			if (!copy || settings.copyValues === false) {
				$(':text, textarea', row).val('');
				//$(':checkbox, :radio', row).prop('checked', false);
				$('select option', row).removeAttr('selected').find(':first').prop('selected', true);
			}
		}


		$('.repeater').each(function() {
			$(this).repeater();
		});

		updateFormNames();
	};

}(jQuery));
