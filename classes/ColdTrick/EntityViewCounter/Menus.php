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
		
		$count = entity_view_counter_get_view_count($entity);
		if ($count === false) {
			return;
		}
		
		$exact_count = entity_view_counter_get_view_count($entity, true);
		
		$result = $hook->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'view_counter',
			'icon' => 'eye',
			'text' => $count,
			'title' => elgg_echo('entity_view_counter:entity:menu:views', [$exact_count]),
			'badge' => $count,
			'href' => false,
		]);
		
		return $result;
	}
}
