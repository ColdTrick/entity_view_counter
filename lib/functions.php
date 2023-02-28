<?php
use Elgg\Values;

/**
 * Helper functions are defined here
 */

/**
 * Check if a type/subtype is configured to be tracked
 *
 * @param string $type    the entity type to check
 * @param string $subtype the entity subtype to check (optional)
 *
 * @return bool
 */
function entity_view_counter_is_configured_entity_type(string $type, string $subtype): bool {
	static $setting;
	
	if (!isset($setting)) {
		$setting = elgg_get_plugin_setting('entity_types', 'entity_view_counter');
		if (!empty($setting)) {
			$setting = json_decode($setting, true);
		}
	}
	
	if (empty($setting)) {
		return false;
	}
	
	$configured_subtypes = elgg_extract($type, $setting);
	if ($configured_subtypes === null) {
		// no types
		return false;
	}
	
	if (empty($subtype) && empty($configured_subtypes)) {
		// no subtype requested and none are configured
		return true;
	}
	
	return (bool) elgg_extract($subtype, $configured_subtypes, false);
}

/**
 * Get the view count of an entity
 *
 * @param \ElggEntity $entity the entity to check
 * @param bool        $exact  return the exact value
 *
 * @return int|string
 */
function entity_view_counter_get_view_count(\ElggEntity $entity, bool $exact = false): int|string {
	$count = $entity->entity_view_count;
	if ($count === null) {
		$count = $entity->countAnnotations(ENTITY_VIEW_COUNTER_ANNOTATION_NAME);
		
		// store annotation count for future usage from metadata
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($entity, $count) {
			$entity->entity_view_count = $count;
		});
	}
	
	$count = (int) $count;
	if ($count < 1000 || $exact) {
		return $count;
	}
	
	return Values::shortFormatOutput($count, 1);
}
