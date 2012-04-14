function multiEditAddInput( addButton ) {
	var container = addButton.siblings('div');
	var clone = container.children().last().clone();
	var editor = clone.children('input').first();
	var entryNumber = parseInt(editor.attr('id').match(/\d+/)) + 1
	var id = editor.attr('id').replace(/_\d+$/, '_' + entryNumber);
	var name = editor.attr('name').replace(/\[\d+\]$/, '[' + entryNumber + ']');
	editor.val('');
	editor.attr('id', id);
	editor.attr('name', name);
	clone.appendTo(container); 
}

function multiEditRemoveInput( removeButton ) {
	var parent = removeButton.parent("div");
	if( parent.parent("div").children().length > 1 ) {
		parent.detach();
		return;
	}
	removeButton.siblings().val('');
}