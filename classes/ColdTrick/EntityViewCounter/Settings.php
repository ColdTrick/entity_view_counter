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
	 * @return null|string
	 */
	public static function saveSettingEntityTypes(\Elgg\Event $event): ?string {
		$plugin = $event->getParam('plugin');
		if (!$plugin instanceof \ElggPlugin || $plugin->getID() !== 'entity_view_counter') {
			return null;
		}
		
		$value = $event->getValue();
		if (!is_array($value)) {
			return null;
		}
		
		return json_encode($value);
	}
}
