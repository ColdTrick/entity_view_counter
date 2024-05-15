import 'jquery';
import spinner from 'elgg/spinner';

$(document).on('click', 'a.entity-view-counter-reset', function() {
	spinner.start();
});
