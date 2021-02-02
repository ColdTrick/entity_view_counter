<?php
/**
 * Show total views over time in a graph
 */

$user = elgg_get_page_owner_entity();
if (!$user instanceof ElggUser || !$user->canEdit()) {
	return;
}

if (!elgg_is_active_plugin('advanced_statistics')) {
	return;
}

// views graph
$count = elgg_get_annotations([
	'owner_guid' => $user->guid,
	'count' => true,
	'annotation_name' => ENTITY_VIEW_COUNTER_ANNOTATION_NAME,
]);

if ($count > 10) {
	echo elgg_view('advanced_statistics/elements/chart', [
		'title' => elgg_echo('entity_view_counter:account:views:chart'),
		'id' => 'entity-view-counter-account-views-chart',
		'date_limited' => false,
		'page' => 'entity_view_counter',
		'section' => 'entity_view_counter',
		'chart' => 'views',
		'url_elements' => [
			'guid' => $user->guid,
		],
	]);
}
