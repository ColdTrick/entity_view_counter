<?php

namespace ColdTrick\EntityViewCounter;

class Views {
	
	/**
	 * Adds menu items to the social menu
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'object/elements/imprint/contents'
	 *
	 * @return array
	 */
	public static function addImprint(\Elgg\Hook $hook) {
		
		$vars = $hook->getValue();
		
		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof \ElggEntity) {
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
		
		$title = elgg_echo('entity_view_counter:entity:menu:views', [$exact_count]); // eg 1024 views
		$content = elgg_echo('entity_view_counter:entity:menu:views', [$count]); // eg 1k views
		
		$imprint = elgg_extract('imprint', $vars, []);
		$imprint['entity_view_counter'] = [
			'icon_name' => 'eye',
			'content' => elgg_format_element('span', ['title' => $title], $content),
		];
		$vars['imprint'] = $imprint;
		
		return $vars;
	}
}
