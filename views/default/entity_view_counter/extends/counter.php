<?php

	/**
	 * This view is prepended on all configured entity types/subtypes
	 * to count that an entity is viewed
	 *
	 */

	if (($entity = elgg_extract("entity", $vars)) && elgg_extract("full_view", $vars, false)) {
		entity_view_counter_add_view($entity);
	}