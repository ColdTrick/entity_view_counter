<?php

$subtype = get_input('subtype');
if (empty($subtype)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

// this could take a while
set_time_limit(0);

// cleanup tracked annotations
elgg_delete_annotations([
	'type' => 'object',
	'subtype' => $subtype,
	'annotation_name' => ENTITY_VIEW_COUNTER_ANNOTATION_NAME,
	'limit' => false,
]);

// cleanup caching metadata
elgg_delete_metadata([
	'type' => 'object',
	'subtype' => $subtype,
	'metadata_name' => 'entity_view_count',
	'limit' => false,
]);

return elgg_ok_response('', elgg_echo('entity_view_counter:action:admin:reset_counter:success'));
