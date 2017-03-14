<?php

namespace ColdTrick\EntityViewCounter;

class Permissions {
	
	/**
	 * Returns if annotation is allowed
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function canAnnotate($hook, $entity_type, $returnvalue, $params) {

		$entity = elgg_extract('entity', $params);
		if (empty($entity)) {
			return;
		}
		
		$annotation_name = elgg_extract('annotation_name', $params);
		if ($annotation_name !== ENTITY_VIEW_COUNTER_ANNOTATION_NAME) {
			return;
		}
		
		// let's block search engine spiders/bots
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			if (preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
				return false;
			}
		}
		
		$user = elgg_extract('user', $params);
		
		// logged out users and not the owner are allowed to be counted
		if (empty($user) || ($user->guid !== $entity->owner_guid)) {
			if (entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
				return true;
			}
		}
		
		return false;
	}
}