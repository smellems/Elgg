//<script>
elgg.provide('elgg.messageboard');

elgg.deprecated_notice('messageboard.js is deprecated. Use the "elgg/messageboard" AMD module', 2.3);

elgg.messageboard.init = function() {
	var form = $('form[name=elgg-messageboard]');
	form.on('click', 'input[type=submit]', elgg.messageboard.submit);

	// remove the default binding for confirmation since we're doing extra stuff.
	// @todo remove if we add a hook to the requires confirmation callback
	form.parent().on('click', '.elgg-menu-item-delete > a', function(event) {
		// double whammy for in case the load order changes.
		$(this).unbind('click', elgg.ui.requiresConfirmation).removeAttr('data-confirm');
		
		elgg.messageboard.deletePost(this);
		event.preventDefault();
	});
};

elgg.messageboard.submit = function(e) {
	var form = $(this).parents('form');
	var data = form.serialize();

	elgg.action('messageboard/add', {
		data: data,
		success: function(json) {
			// the action always returns the full ul and li wrapped annotation.
			var ul = form.next('ul.elgg-list-annotation');

			if (ul.length < 1) {
				form.parent().append(json.output);
			} else {
				ul.prepend($(json.output).find('li:first'));
			}
			form.find('textarea').val('');
		}
	});

	e.preventDefault();
};

elgg.messageboard.deletePost = function(elem) {
	var $link = $(elem);
	var confirmText = $link.attr('title') || elgg.echo('question:areyousure');

	if (confirm(confirmText)) {
		elgg.action($link.attr('href'), {
			success: function() {
				var item = $link.closest('.elgg-item');
				item.remove();
			}
		});
	}
};

elgg.register_hook_handler('init', 'system', elgg.messageboard.init);
