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
	
		if (!empty($params) && is_array($params)) {
			$entity = elgg_extract("entity", $params);
			
			if (!empty($entity) && entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
				if ($count = $entity->countAnnotations(ENTITY_VIEW_COUNTER_ANNOTATION_NAME)) {
					$text = "<span title='" . htmlspecialchars(elgg_echo("entity_view_counter:entity:menu:views", array($count)), ENT_QUOTES, "UTF-8", false) . "'>";
					$text .= elgg_view_icon("eye") . $count;
					$text .= "</span>";
					
					$result[] = \ElggMenuItem::factory(array(
						"name" => "view_counter",
						"text" => $text,
						"href" => false,
						"priority" => 300
					));
				}
			}
		}
		
		return $result;
	}
}