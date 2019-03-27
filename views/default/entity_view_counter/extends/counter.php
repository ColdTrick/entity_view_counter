<?php
/**
 * This view is prepended on all configured entity types/subtypes
 * to count that an entity is viewed
 */

$entity = elgg_extract('entity', $vars);
$full_view = elgg_extract('full_view', $vars, false);

if (!($entity instanceof ElggEntity) || !$full_view) {
	return;
}

// a full view in a widget is not something we want to count
if (elgg_in_context('widgets')) {
	return;
}
	
// first check if we're allowed to count the views
if (!$entity->canAnnotate(elgg_get_logged_in_user_guid(), ENTITY_VIEW_COUNTER_ANNOTATION_NAME)) {
	return;
}

// check if we didn't already view this entity
// views are locked by session id
$session = elgg_get_session();

$viewed_guids = $session->get('entity_view_counter', []);
if (!is_array($viewed_guids)) {
	$viewed_guids = [];
}

if (in_array($entity->guid, $viewed_guids)) {
	return;
}

// write to session, for speed
$viewed_guids[] = $entity->guid;
$session->set('entity_view_counter', $viewed_guids);

$session_id = $session->getId();

// log the user who is viewing
// if no logged in user, log by entity
$owner_guid = elgg_get_logged_in_user_guid() ?: $entity->guid;

$annotation_id = $entity->annotate(ENTITY_VIEW_COUNTER_ANNOTATION_NAME, $session_id, ACCESS_PUBLIC, $owner_guid);
if (!$annotation_id) {
	// someone prevented the creation of the annotation
	return;
}

// store total count in metadata
$current_count = $entity->entity_view_count;
if (is_null($current_count)) {
	// check the annotation count (this includes the just created annotation)
	$current_count = $entity->countAnnotations(ENTITY_VIEW_COUNTER_ANNOTATION_NAME);
} else {
	// update the current count
	$current_count = (int) $current_count;
	
	$current_count++;
}

elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity, $current_count) {
	$entity->entity_view_count = $current_count;
});
