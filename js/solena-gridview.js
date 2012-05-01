function setupSearchTimeout( entry, filterClass ) {
	var id = $(entry).attr('id');
	var inputSelector = '#' + id + ' .' + filterClass + ' input, ' + '#' + id + ' .' + filterClass + ' select';

	$(document).on('keyup', inputSelector, function (event) {
		var trigger = $(event.target).data("searchTrigger");
		if (typeof(trigger) != "undefined") {
			clearTimeout(trigger);
		}

		trigger = setTimeout(function() { 
			$(event.target).trigger('change');
		}, 750);
		$(event.target).data("searchTrigger", trigger);
	});

	$(document).on('change', inputSelector, function (event) {
		var grid = $(this).closest('.grid-view');
		$(document).data(grid.attr('id') + '-lastFocused', this.name);
	});
}

// Default handler for beforeAjaxUpdate event
function solenaAfterAjax(id, options)
{
	var grid = $('#' + id);
	var lastFocused = $(document).data(grid.attr('id') + '-lastFocused');

	// If the function was not activated
	if (lastFocused == null) {
		return;
	}
	// Get the control
	fe = $('[name="' + lastFocused + '"]', grid);
	// If the control exists..
	if (fe!=null) {
		if(fe.get(0).tagName == 'INPUT' && fe.attr('type') == 'text') {
			// Focus and place the cursor at the end
			fe.cursorEnd();
		} else {
			// Just focus
			fe.focus();
		}
	}
}
 
// Place the cursor at the end of the text field
jQuery.fn.cursorEnd = function()
{
	return this.each(function(){
		if(this.setSelectionRange) {
			this.focus();
			this.setSelectionRange(this.value.length,this.value.length);
		} else if (this.createTextRange) {
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', this.value.length);
			range.moveStart('character', this.value.length);
			range.select();
		}
		return false;
	});
}
