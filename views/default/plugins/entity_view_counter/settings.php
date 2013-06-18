<?php

	$plugin = elgg_extract("entity", $vars);
	
	if ($registered_types = elgg_get_config("registered_entities")) {
		
		echo "<div>";
		echo elgg_echo("entity_view_counter:settings:description");
		echo "</div>";
		
		$content = "";
		foreach ($registered_types as $type => $subtypes) {
			// for now only track objects
			if ($type != "object") {
				continue;
			}
			
			if (!empty($subtypes) && is_array($subtypes)) {
				foreach ($subtypes as $subtype) {
					$options = array(
						"name" => "params[entity_types][" . $type . "][" . $subtype . "]",
						"value" => 1,
						"default" => false
					);
					
					if (entity_view_counter_is_configured_entity_type($type, $subtype)) {
						$options["checked"] = "checked";
					}
					
					$content .= "<tr>";
					$content .= "<td class='entity-view-couter-settings-checkbox'>";
					$content .= elgg_view("input/checkbox", $options);
					$content .= "</td>";
					$content .= "<td>" . elgg_echo("item:" . $type . ":" . $subtype) . "</td>";
					$content .= "</tr>";
				}
			} else {
				$options = array(
					"name" => "params[entity_types][" . $type . "]",
					"value" => 1,
					"default" => false
				);
					
				if (entity_view_counter_is_configured_entity_type($type)) {
					$options["checked"] = "checked";
				}
				
				$content .= "<tr>";
				$content .= "<td class='entity-view-couter-settings-checkbox'>";
				$content .= elgg_view("input/checkbox", $options);
				$content .= "</td>";
				$content .= "<td>" . elgg_echo("item:" . $type) . "</td>";
				$content .= "</tr>";
			}
		}
		
		if (!empty($content)) {
			echo "<table class='elgg-table-alt mbm'>";
			
			echo "<tr>";
			echo "<th>&nbsp;</th>";
			echo "<th>" . elgg_echo("entity_view_counter:settings:entity_type") . "</th>";
			echo "</tr>";
			
			echo $content;
			echo "</table>";
		}
	}