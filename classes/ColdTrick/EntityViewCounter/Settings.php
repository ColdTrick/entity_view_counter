<?php

namespace ColdTrick\EntityViewCounter;

class Settings {
	
	/**
	 * Modifies the value of the entity_types setting
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function saveSettingEntityTypes($hook, $entity_type, $returnvalue, $params) {
	
		$plugin = elgg_extract('plugin', $params);
		if ($plugin->getID() !== 'entity_view_counter') {
			return;
		}
		
		$setting = elgg_extract('name', $params);
		if ($setting !== 'entity_types') {
			return;
		}
	
		return json_encode(elgg_extract('value', $params));
	}
}