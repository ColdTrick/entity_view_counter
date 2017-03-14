<?php

$registered_types = elgg_get_config('registered_entities');
$object_types = elgg_extract('object', $registered_types, []);

echo elgg_view('output/longtext', ['value' => elgg_echo('entity_view_counter:settings:description')]);

$list_items = '';
foreach ($object_types as $subtype) {
	
	$item = elgg_view('input/checkbox', [
		'name' => "params[entity_types][object][{$subtype}]",
		'value' => 1,
		'default' => false,
		'checked' => entity_view_counter_is_configured_entity_type('object', $subtype),
		'class' => 'mrm',
	]);
	$item .= elgg_echo("item:object:{$subtype}");
	
	$list_items .= elgg_format_element('li', [], $item);
}

if (empty($list_items)) {
	return;
}

echo elgg_format_element('label', ['class' => 'elgg-field-label'], elgg_echo('entity_view_counter:settings:entity_type'));
echo elgg_format_element('ul', ['class' => 'mbm'], $list_items);
