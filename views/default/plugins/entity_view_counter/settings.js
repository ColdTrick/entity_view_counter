define(function(require) {
	var $ = require('jquery');
	var spinner = require('elgg/spinner');
	
	$(document).on('click', 'a.entity-view-counter-reset', function() {
		spinner.start();
	});
});
