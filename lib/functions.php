<?php
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
function entity_view_counter_is_configured_entity_type($type, $subtype = '') {
	
	$setting = elgg_get_plugin_setting('entity_types', 'entity_view_counter');
	if (empty($setting)) {
		return false;
	}
	
	$setting = json_decode($setting, true);
	
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
 * @param ElggEntity $entity the entity to check
 *
 * @return false|string
 */
function entity_view_counter_get_view_count(ElggEntity $entity, $exact = false) {
	
	if (!$entity instanceof ElggEntity) {
		return false;
	}
	
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
	
	$separator = substr(elgg_echo('entity_view_counter:separator'), 0, 1);
	
	if ($count < 9999) {
		// make 1 decimal rounding (eg 1.5K)
		$count = number_format(($count / 1000), 1, '.', $separator);
		return elgg_echo('entity_view_counter:view:kilo', [$count]);
	} elseif ($count < 1000000) {
		// round to next thousand (eg. 10K)
		$count = (int) ($count / 1000);
		return elgg_echo('entity_view_counter:view:kilo', [$count]);
	} elseif ($count < 9999999) {
		// make 1 decimal rounding (eg 1.5M)
		$count = number_format(($count / 1000000), 1, '.', $separator);
		return elgg_echo('entity_view_counter:view:mega', [$count]);
	}
	
	// round to next million (eg. 10M)
	$count = (int) ($count / 1000000);
	return elgg_echo('entity_view_counter:view:mega', [$count]);
}
