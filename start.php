<?php

	define("ENTITY_VIEW_COUNTER_ANNOTATION_NAME", "view_counter");

	require_once(dirname(__FILE__) . "/lib/functions.php");
	require_once(dirname(__FILE__) . "/lib/hooks.php");
	
	elgg_register_event_handler("init", "system", "entity_view_counter_init");
	elgg_register_event_handler("pagesetup", "system", "entity_view_counter_pagesetup");
	
	function entity_view_counter_init() {
		// extend css
		elgg_extend_view("css/elgg", "css/entity_view_counter/site");
		elgg_extend_view("css/admin", "css/entity_view_counter/admin");
		
		// register plugin hooks
		elgg_register_plugin_hook_handler("permissions_check:annotate", "all", "entity_view_counter_permissions_check_annotate_hook");
		elgg_register_plugin_hook_handler("register", "menu:entity", "entity_view_counter_entity_menu_hook", 502);
		elgg_register_plugin_hook_handler("setting", "plugin", "entity_view_counter_plugin_setting_hook");
	}
	
	function entity_view_counter_pagesetup() {
		// extend views of configured entity types/subtypes
		entity_view_counter_extend_views();
	}