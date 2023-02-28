<?php

use Elgg\Exceptions\Http\EntityNotFoundException;
use Elgg\Exceptions\Http\EntityPermissionsException;

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	throw new EntityNotFoundException();
}

if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

$time_created = $entity->time_created;

$get_count = function(array $options = []) use ($entity) {
	$options = array_merge([
		'distinct' => false,
		'guid' => $entity->guid,
		'annotation_name' => ENTITY_VIEW_COUNTER_ANNOTATION_NAME,
		'annotation_calculation' => 'count',
	], $options);
	
	return elgg_get_annotations($options);
};


$result = '';

$result .= '<table class="elgg-table">';

// total
$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:total') . '</th><td>' . $get_count() . '</td></tr>';

// today
$count = $get_count(['annotation_created_after' => strtotime('today')]);
$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:today') . '</th><td>' . $count . '</td></tr>';

// yesterday
if ($time_created < strtotime('today')) {
	$count = $get_count([
		'annotation_created_after' => strtotime('yesterday'),
		'annotation_created_before' => strtotime('today'),
	]);
	$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:yesterday') . '</th><td>' . $count . '</td></tr>';
}

// last 7 days
if ($time_created < strtotime('yesterday')) {
	$count = $get_count([
		'annotation_created_after' => strtotime('today -7 days'),
	]);
	$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:last7d') . '</th><td>' . $count . '</td></tr>';
}

// last 30 days
if ($time_created < strtotime('7 days ago')) {
	$count = $get_count([
		'annotation_created_after' => strtotime('today -30 days'),
	]);
	$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:last30d') . '</th><td>' . $count . '</td></tr>';
}

// last 90 days
if ($time_created < strtotime('30 days ago')) {
	$count = $get_count([
		'annotation_created_after' => strtotime('today -90 days'),
	]);
	$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:last90d') . '</th><td>' . $count . '</td></tr>';
}

// last 180 days
if ($time_created < strtotime('90 days ago')) {
	$count = $get_count([
		'annotation_created_after' => strtotime('today -180 days'),
	]);
	$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:last180d') . '</th><td>' . $count . '</td></tr>';
}

// this year
$count = $get_count([
	'annotation_created_after' => strtotime('first day of january this year'),
]);
$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:thisyear') . '</th><td>' . $count . '</td></tr>';

// last year
if ($time_created < strtotime('first day of january this year')) {
	$count = $get_count([
		'annotation_created_after' => strtotime('first day of january last year'),
		'annotation_created_before' => strtotime('first day of january this year'),
	]);
	$result .= '<tr><th>' . elgg_echo('entity_view_counter:stats:lastyear') . '</th><td>' . $count . '</td></tr>';
}

$result .= '</table>';

echo elgg_view_module('info', elgg_echo('entity_view_counter:stats:title'), $result);

if (!elgg_is_active_plugin('advanced_statistics')) {
	return;
}

$recent_count = $get_count(['annotation_created_after' => strtotime('today -180 days')]);
if ($recent_count > 10) {
	echo elgg_view('advanced_statistics/elements/chart', [
		'title' => elgg_echo('entity_view_counter:stats:chart:recent'),
		'id' => 'entity-view-counter-activity-recent',
		'date_limited' => false,
		'page' => 'entity_view_counter',
		'section' => 'entity_view_counter',
		'chart' => 'recent',
		'url_elements' => [
			'guid' => $entity->guid,
		],
	]);
}

$year_count = $get_count(['annotation_created_before' => strtotime('first day of january this year')]);
if (!empty($year_count)) {
	echo elgg_view('advanced_statistics/elements/chart', [
		'title' => elgg_echo('entity_view_counter:stats:chart:years'),
		'id' => 'entity-view-counter-activity-years',
		'date_limited' => false,
		'page' => 'entity_view_counter',
		'section' => 'entity_view_counter',
		'chart' => 'years',
		'url_elements' => [
			'guid' => $entity->guid,
		],
	]);
}
