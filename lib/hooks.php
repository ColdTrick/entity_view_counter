<?php

	function entity_view_counter_permissions_check_annotate_hook($hook, $type, $returnvalue, $params) {
		$result = $returnvalue;
		
		if (!empty($params) && is_array($params)) {
			$entity = elgg_extract("entity", $params);
			$user = elgg_extract("user", $params);
			$annotation_name = elgg_extract("annotation_name", $params);
			
			if (($annotation_name == ENTITY_VIEW_COUNTER_ANNOTATION_NAME) && !empty($entity)) {
				// views won't be counted unless.....
				$result = false;
				
				// logged out users and not the owner are allowed to be counted
				if (empty($user) || ($user->getGUID() != $entity->getOwnerGUID())) {
					if (entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
						$result = true;
					}
				}
				
				// let's block search engine spiders/bots
				if ($result && isset($_SERVER["HTTP_USER_AGENT"])) {
					if (preg_match('/bot|crawl|slurp|spider/i', $_SERVER["HTTP_USER_AGENT"])) {
						$result = false;
					}
				}
			}
		}
		
		return $result;
	}
	
	function entity_view_counter_entity_menu_hook($hook, $type, $returnvalue, $params) {
		$result = $returnvalue;
		
		if (!empty($params) && is_array($params)) {
			$entity = elgg_extract("entity", $params);
			
			if (!empty($entity) && entity_view_counter_is_configured_entity_type($entity->getType(), $entity->getSubtype())) {
				if ($count = $entity->countAnnotations(ENTITY_VIEW_COUNTER_ANNOTATION_NAME)) {
					$text = "<span title='" . htmlspecialchars(elgg_echo("entity_view_counter:entity:menu:views", array($count)), ENT_QUOTES, "UTF-8", false) . "'>";
					$text .= elgg_view_icon("eye") . $count;
					$text .= "</span>";
					
					$result[] = ElggMenuItem::factory(array(
						"name" => "view_counter",
						"text" => $text,
						"href" => false,
						"priority" => 300
					));
				}
			}
		}
		
		return $result;
	}
	
	function entity_view_counter_plugin_setting_hook($hook, $type, $returnvalue, $params) {
		$result = $returnvalue;
		
		if (!empty($params) && is_array($params)) {
			$plugin = elgg_extract("plugin", $params);
			$setting = elgg_extract("name", $params);
			
			if (($plugin->getID() == "entity_view_counter") && ($setting = "entity_types")) {
				$result = json_encode(elgg_extract("value", $params));
			}
		}
		
		return $result;
	}