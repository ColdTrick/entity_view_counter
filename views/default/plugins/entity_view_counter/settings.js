define(['jquery', 'elgg/spinner'], function($, spinner) {
	$(document).on('click', 'a.entity-view-counter-reset', function() {
		spinner.start();
	});
});
