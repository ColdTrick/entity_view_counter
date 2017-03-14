<?php
/**
 * This view is prepended on all configured entity types/subtypes
 * to count that an entity is viewed
 */

$entity = elgg_extract('entity', $vars);
$full_view = elgg_extract('full_view', $vars, false);

if (!$entity || !$full_view) {
	return;
}
	
// first check if we're allowed to count the views
if (!$entity->canAnnotate(elgg_get_logged_in_user_guid(), ENTITY_VIEW_COUNTER_ANNOTATION_NAME)) {
	return;
}	

// check if we didn't already view this entity
// views are locked by session id
$session_id = session_id();

$existing_annotations = elgg_get_entities_from_annotations([
	'guid' => $entity->guid,
	'annotation_name' => ENTITY_VIEW_COUNTER_ANNOTATION_NAME,
	'annotation_value' => $session_id,
	'count' => true,
]);

if ($existing_annotations) {
	return;
}

// log the user who is viewing
// if no logged in user, log by entity
$owner_guid = elgg_get_logged_in_user_guid() ?: $entity->guid;

$entity->annotate(ENTITY_VIEW_COUNTER_ANNOTATION_NAME, $session_id, ACCESS_PUBLIC, $owner_guid);
