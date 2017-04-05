<?php

$entity = elgg_extract('entity', $vars);
if (!($entity instanceof \ElggEntity)) {
	return;
}

$count = $entity->entity_view_count;
if ($count == null) {
	$count = $entity->countAnnotations(ENTITY_VIEW_COUNTER_ANNOTATION_NAME);
	
	// store annotation count for future usage from metadata
	$ia = elgg_set_ignore_access(true);
	create_metadata($entity->guid, 'entity_view_count', $count, '', $entity->owner_guid, ACCESS_PUBLIC);
	elgg_set_ignore_access($ia);
}

if (empty($count)) {
	return;
}
		
echo elgg_format_element('span', [
	'title' => elgg_echo('entity_view_counter:entity:menu:views', [$count]),
], elgg_view_icon('eye') . $count);
