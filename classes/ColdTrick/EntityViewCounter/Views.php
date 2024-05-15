<?php

namespace ColdTrick\EntityViewCounter;

/**
 * Views
 */
class Views {
	
	/**
	 * Adds menu items to the social menu
	 *
	 * @param \Elgg\Event $event 'view_vars', 'object/elements/imprint/contents'
	 *
	 * @return null|array
	 */
	public static function addImprint(\Elgg\Event $event): ?array {
		$vars = $event->getValue();
		
		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof \ElggEntity) {
			return null;
		}
		
		if (!elgg_extract('show_entity_view_counter', $vars, true)) {
			return null;
		}
		
		if (!entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
			return null;
		}
		
		$count = entity_view_counter_get_view_count($entity);
		if (empty($count)) {
			return null;
		}
		
		$exact_count = entity_view_counter_get_view_count($entity, true);
		
		$title = elgg_echo('entity_view_counter:entity:menu:views', [$exact_count]); // eg 1024 views
		$content = elgg_echo('entity_view_counter:entity:menu:views', [$count]); // eg 1k views
		
		if ($exact_count && $entity->canEdit()) {
			$content = elgg_view('output/url', [
				'href' => elgg_http_add_url_query_elements('ajax/view/entity_view_counter/stats', [
					'guid' => $entity->guid,
				]),
				'text' => $content,
				'is_trusted' => true,
				'class' => 'elgg-lightbox',
				'data-colorbox-opts' => json_encode([
					'innerWidth' => 700,
				]),
			]);
		}
		
		$imprint = elgg_extract('imprint', $vars, []);
		$imprint['entity_view_counter'] = [
			'icon_name' => 'chart-line',
			'content' => elgg_format_element('span', ['title' => $title], $content),
		];
		$vars['imprint'] = $imprint;
		
		return $vars;
	}
}
