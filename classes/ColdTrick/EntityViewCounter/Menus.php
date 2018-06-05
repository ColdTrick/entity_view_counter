<?php

namespace ColdTrick\EntityViewCounter;

class Menus {
	
	/**
	 * Adds menu items to the entity menu
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerEntity($hook, $entity_type, $returnvalue, $params) {
		$result = $returnvalue;
	
		if (empty($params) || !is_array($params)) {
			return;
		}
		
		$entity = elgg_extract('entity', $params);
		if (!($entity instanceof \ElggEntity)) {
			return;
		}
		
		
		if (!entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
			return;
		}
		
		$text = elgg_view('entity_view_counter/text', ['entity' => $entity]);
		if (empty($text)) {
			return;
		}
		
		$count = $entity->entity_view_count;
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'view_counter',
			'text' => $text,
			'href' => false,
			'priority' => 300,
		]);
		
		return $result;
	}
}