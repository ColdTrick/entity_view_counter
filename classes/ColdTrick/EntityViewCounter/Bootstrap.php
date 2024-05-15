<?php

namespace ColdTrick\EntityViewCounter;

use Elgg\DefaultPluginBootstrap;

/**
 * Plugin bootstrap
 */
class Bootstrap extends DefaultPluginBootstrap {

	/**
	 * {@inheritdoc}
	 */
	public function ready() {
		$this->addViewCounter();
	}
	
	/**
	 * Add the view counter to the object views
	 *
	 * @return void
	 */
	protected function addViewCounter(): void {
		// extend views of configured entity types/subtypes
		$registered_types = elgg_entity_types_with_capability('searchable');
		if (empty($registered_types)) {
			return;
		}
		
		// let's extend the base views of these entities
		foreach ($registered_types as $type => $subtypes) {
			if (empty($subtypes) || !is_array($subtypes)) {
				// user and group don't have a subtype
				elgg_extend_view($type . '/default', 'entity_view_counter/extends/counter', 450);
				continue;
			}
			
			foreach ($subtypes as $subtype) {
				// allow for fallback views
				$views = [
					"{$type}/{$subtype}",
					"{$type}/default",
				];
				
				foreach ($views as $baseview) {
					if (!elgg_view_exists($baseview, '', false)) {
						continue;
					}
					
					elgg_extend_view($baseview, 'entity_view_counter/extends/counter', 450);
					break;
				}
			}
		}
	}
}
