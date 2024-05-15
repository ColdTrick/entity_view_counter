<?php

use Elgg\Database\AnnotationsTable;
use Elgg\Database\Delete;
use Elgg\Database\EntityTable;
use Elgg\Database\MetadataTable;

$subtype = get_input('subtype');
if (empty($subtype)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

// this could take a while
set_time_limit(0);

// cleanup tracked annotations
// use direct DB query to bulk delete and not trigger events
$annotations = Delete::fromTable(AnnotationsTable::TABLE_NAME);
$entities = $annotations->subquery(EntityTable::TABLE_NAME);
$entities->select('guid')
	->where($annotations->compare('type', '=', 'object', ELGG_VALUE_STRING))
	->andWhere($annotations->compare('subtype', '=', $subtype, ELGG_VALUE_STRING));

$annotations->where($annotations->compare('name', '=', ENTITY_VIEW_COUNTER_ANNOTATION_NAME, ELGG_VALUE_STRING))
	->andWhere($annotations->compare('entity_guid', 'in', $entities->getSQL()));

elgg()->db->deleteData($annotations);

// cleanup caching metadata
// use direct DB query to bulk delete and not trigger events
$metadata = Delete::fromTable(MetadataTable::TABLE_NAME);
$entities = $metadata->subquery(EntityTable::TABLE_NAME);
$entities->select('guid')
	->where($metadata->compare('type', '=', 'object', ELGG_VALUE_STRING))
	->andWhere($metadata->compare('subtype', '=', $subtype, ELGG_VALUE_STRING));

$metadata->where($metadata->compare('name', '=', 'entity_view_count', ELGG_VALUE_STRING))
	->andWhere($metadata->compare('entity_guid', 'in', $entities->getSQL()));

elgg()->db->deleteData($metadata);

return elgg_ok_response('', elgg_echo('entity_view_counter:action:admin:reset_counter:success'));
