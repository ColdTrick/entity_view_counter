<?php

namespace ColdTrick\EntityViewCounter;

class Permissions {
	
	/**
	 * Returns if annotation is allowed
	 *
	 * @param \Elgg\Hook $hook 'permissions_check:annotate', 'all'
	 *
	 * @return void|bool
	 */
	public static function canAnnotate(\Elgg\Hook $hook) {

		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return;
		}
		
		if ($hook->getParam('annotation_name') !== ENTITY_VIEW_COUNTER_ANNOTATION_NAME) {
			return;
		}
		
		// let's block search engine spiders/bots
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			if (preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
				return false;
			}
		}
		
		$user = $hook->getParam('user');
		
		// logged out users and not the owner are allowed to be counted
		if (empty($user) || ($user->guid !== $entity->owner_guid)) {
			if (entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
				return true;
			}
		}
		
		return false;
	}
}
