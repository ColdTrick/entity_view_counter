<?php

namespace ColdTrick\EntityViewCounter;

class Settings {
	
	/**
	 * Modifies the value of the entity_types setting
	 *
	 * @param \Elgg\Hook $hook 'setting', 'plugin'
	 *
	 * @return array
	 */
	public static function saveSettingEntityTypes(\Elgg\Hook $hook) {
		
		$plugin = $hook->getParam('plugin');
		if (!$plugin instanceof \ElggPlugin || $plugin->getID() !== 'entity_view_counter') {
			return;
		}
		
		if ($hook->getParam('name') !== 'entity_types') {
			return;
		}
		
		return json_encode($hook->getParam('value'));
	}
}
