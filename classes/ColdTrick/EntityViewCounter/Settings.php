<?php

namespace ColdTrick\EntityViewCounter;

/**
 * Plugin settings callbacks
 */
class Settings {
	
	/**
	 * Modifies the value of the entity_types setting
	 *
	 * @param \Elgg\Event $event 'setting', 'plugin'
	 *
	 * @return array
	 */
	public static function saveSettingEntityTypes(\Elgg\Event $event) {
		
		$plugin = $event->getParam('plugin');
		if (!$plugin instanceof \ElggPlugin || $plugin->getID() !== 'entity_view_counter') {
			return;
		}
		
		if ($event->getParam('name') !== 'entity_types') {
			return;
		}
		
		return json_encode($event->getParam('value'));
	}
}
