<?php

define('ENTITY_VIEW_COUNTER_ANNOTATION_NAME', 'view_counter');

elgg_register_event_handler('init', 'system', 'entity_view_counter_init');
elgg_register_event_handler('ready', 'system', 'entity_view_counter_ready');

function entity_view_counter_init() {
	// register plugin hooks
	elgg_register_plugin_hook_handler('permissions_check:annotate', 'all', '\ColdTrick\EntityViewCounter\Permissions::canAnnotate');
	elgg_register_plugin_hook_handler('register', 'menu:entity', '\ColdTrick\EntityViewCounter\Menus::registerEntity', 502);
	elgg_register_plugin_hook_handler('setting', 'plugin', '\ColdTrick\EntityViewCounter\Settings::saveSettingEntityTypes');
}

function entity_view_counter_ready() {
	// extend views of configured entity types/subtypes
	$registered_types = elgg_get_config('registered_entities');
	if (empty($registered_types)) {
		return;
	}
	
	// let's extend the base views of these entities
	foreach ($registered_types as $type => $subtypes) {
		
		if (!empty($subtypes) && is_array($subtypes)) {
			foreach ($subtypes as $subtype) {
				elgg_extend_view($type . '/' . $subtype, 'entity_view_counter/extends/counter', 450);
			}
		} else {
			// user and group don't have a subtype
			elgg_extend_view($type . '/default', 'entity_view_counter/extends/counter', 450);
		}
	}
}

function entity_view_counter_is_configured_entity_type($type, $subtype = '') {
	
	$setting = elgg_get_plugin_setting('entity_types', 'entity_view_counter');
	if (empty($setting)) {
		return false;
	}
	
	$setting = json_decode($setting, true);
	
	$configured_subtypes = elgg_extract($type, $setting);
	if ($configured_subtypes === null) {
		// no types
		return false;
	}
	
	if (empty($subtype) && empty($configured_subtypes)) {
		// no subtype requested and none are configured
		return true;
	}
	
	return (bool) elgg_extract($subtype, $configured_subtypes, false);
}
