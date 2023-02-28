<?php

namespace ColdTrick\EntityViewCounter;

use Elgg\Database\Delete as DBDelete;

/**
 * Delete callbacks
 */
class Delete {
	
	/**
	 * On delete of an object remove all view annotations in bulk
	 *
	 * @param \Elgg\Event $event 'delete', 'object'
	 *
	 * @return void
	 */
	public static function deleteViews(\Elgg\Event $event) {
		
		$entity = $event->getObject();
		if (!$entity instanceof \ElggEntity) {
			return;
		}
		
		// Do a bulk delete to save on performance (eg. not for every annotation a delete sequence)
		$delete = DBDelete::fromTable('annotations');
		$delete->where($delete->compare('entity_guid', '=', $entity->guid, ELGG_VALUE_GUID))
			->andWhere($delete->compare('name', '=', ENTITY_VIEW_COUNTER_ANNOTATION_NAME, ELGG_VALUE_STRING));
		
		elgg()->db->deleteData($delete);
	}
}
