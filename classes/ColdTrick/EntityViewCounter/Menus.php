<?php

namespace ColdTrick\EntityViewCounter;

class Menus {
	
	/**
	 * Adds menu items to the social menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:social'
	 *
	 * @return array
	 */
	public static function registerSocial(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!($entity instanceof \ElggEntity)) {
			return;
		}
		
		if (!entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
			return;
		}
		
		$count = $entity->entity_view_count;
		if (is_null($count)) {
			$count = $entity->countAnnotations(ENTITY_VIEW_COUNTER_ANNOTATION_NAME);
			
			// store annotation count for future usage from metadata
			elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity, $count) {
				$entity->entity_view_count = $count;
			});
		}
		
		if (empty($count)) {
			return;
		}
		
		$result = $hook->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'view_counter',
			'icon' => 'eye',
			'text' => elgg_echo('entity_view_counter:entity:menu:views', [$count]),
			'title' => elgg_echo('entity_view_counter:entity:menu:views', [$count]),
			'badge' => $count,
			'href' => false,
		]);
		
		return $result;
	}
}
