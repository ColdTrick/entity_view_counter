<?php
/**
 * Show your most viewed content and total views over time
 */

$user = elgg_get_page_owner_entity();
if (!$user instanceof ElggUser || !$user->canEdit()) {
	return;
}

$num_days = (int) elgg_extract('num_days', $vars, 90);

// top 5 viewed content in the past 90 days
$entities = elgg_get_entities([
	'owner_guid' => $user->guid,
	'limit' => 5,
	'annotation_created_after' => "today - {$num_days} days",
	'annotation_name' => ENTITY_VIEW_COUNTER_ANNOTATION_NAME,
	'annotation_sort_by_calculation' => 'count',
	'full_view' => false,
]);

if (!empty($entities)) {
	$body = elgg_view('output/longtext', [
		'value' => elgg_echo('entity_view_counter:account:views:top:description', [$num_days]),
	]);
	
	$lis = [];
	foreach ($entities as $entity) {
		$lis[] = elgg_format_element('li', ['class' => 'elgg-item'], elgg_view('output/url', [
			'text' => $entity->getDisplayName(),
			'href' => $entity->getURL(),
			'is_trusted' => true,
			'badge' => elgg_echo('entity_view_counter:entity:menu:views', [entity_view_counter_get_view_count($entity)]),
		]));
	}
	
	$body .= elgg_format_element('ul', ['class' => 'elgg-list'], implode(PHP_EOL, $lis));
	
	echo elgg_view_module('info', elgg_echo('entity_view_counter:account:views:top:title'), $body);
}

if (elgg_is_active_plugin('advanced_statistics')) {
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
}
