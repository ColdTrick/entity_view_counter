<?php

$entity = elgg_extract('entity', $vars);
if (!($entity instanceof \ElggEntity)) {
	return;
}

$count = entity_view_counter_get_view_count($entity);
if ($count === false) {
	return;
}

$exact_count = entity_view_counter_get_view_count($entity, true);

$icon_name = elgg_extract('icon_name', $vars, 'eye');
		
echo elgg_format_element('span', [
	'title' => elgg_echo('entity_view_counter:entity:menu:views', [$exact_count]),
], elgg_view_icon($icon_name) . $count);
