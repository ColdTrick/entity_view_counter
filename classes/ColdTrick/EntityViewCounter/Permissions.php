<?php

namespace ColdTrick\EntityViewCounter;

/**
 * Permissions
 */
class Permissions {
	
	/**
	 * Returns if annotation is allowed
	 *
	 * @param \Elgg\Event $event 'permissions_check:annotate', 'all'
	 *
	 * @return null|bool
	 */
	public static function canAnnotate(\Elgg\Event $event): ?bool {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return null;
		}
		
		if ($event->getParam('annotation_name') !== ENTITY_VIEW_COUNTER_ANNOTATION_NAME) {
			return null;
		}
		
		// let's block search engine spiders/bots
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			if (preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
				return false;
			}
		}
		
		$user = $event->getUserParam();
		
		// logged-out users and not the owner are allowed to be counted
		if (empty($user) || ($user->guid !== $entity->owner_guid)) {
			if (entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
				return true;
			}
		}
		
		return false;
	}
}
