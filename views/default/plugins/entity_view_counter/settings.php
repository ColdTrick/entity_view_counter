<?php

$object_types = (array) get_registered_entity_types('object');

echo elgg_view('output/longtext', [
	'value' => elgg_echo('entity_view_counter:settings:description'),
]);

$list_items = '';
foreach ($object_types as $subtype) {
	
	$item = elgg_view('input/checkbox', [
		'name' => "params[entity_types][object][{$subtype}]",
		'value' => 1,
		'default' => false,
		'checked' => entity_view_counter_is_configured_entity_type('object', $subtype),
		'class' => 'mrm',
		'label' => elgg_echo("item:object:{$subtype}"),
		'switch' => true,
	]);
	
	$list_items .= elgg_format_element('li', [], $item);
}

if (empty($list_items)) {
	return;
}

echo elgg_format_element('label', ['class' => 'elgg-field-label'], elgg_echo('entity_view_counter:settings:entity_type'));
echo elgg_format_element('ul', ['class' => 'mbm'], $list_items);
