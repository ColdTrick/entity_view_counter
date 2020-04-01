<?php

$object_types = (array) get_registered_entity_types('object');

echo elgg_view('output/longtext', [
	'value' => elgg_echo('entity_view_counter:settings:description'),
]);

// listing
$row = [
	elgg_format_element('th', [], elgg_echo('entity_view_counter:settings:entity_type')),
	elgg_format_element('th', ['class' => 'center'], elgg_echo('entity_view_counter:settings:num_views')),
	elgg_format_element('th', ['class' => 'center'], elgg_echo('entity_view_counter:settings:delete')),
];

$header = elgg_format_element('thead', [], elgg_format_element('tr', [], implode(PHP_EOL, $row)));

$rows = [];
foreach ($object_types as $subtype) {
	$row = [];
	
	$row[] = elgg_format_element('td', [], elgg_view('input/checkbox', [
		'name' => "params[entity_types][object][{$subtype}]",
		'value' => 1,
		'default' => false,
		'checked' => entity_view_counter_is_configured_entity_type('object', $subtype),
		'label' => elgg_echo("item:object:{$subtype}"),
		'switch' => true,
	]));
	
	$count = elgg_get_annotations([
		'type' => 'object',
		'subtype' => $subtype,
		'annotation_name' => ENTITY_VIEW_COUNTER_ANNOTATION_NAME,
		'count' => true,
	]);
	$row[] = elgg_format_element('td', ['class' => 'center'], $count);
	
	if ($count > 0) {
		$row[] = elgg_format_element('td', ['class' => 'center'], elgg_view('output/url', [
			'text' => false,
			'href' => elgg_generate_action_url('entity_view_counter/admin/reset_counter', [
				'subtype' => $subtype,
			]),
			'icon' => 'delete',
			'title' => elgg_echo('delete'),
			'confirm' => elgg_echo('deleteconfirm:plural'),
			'class' => 'entity-view-counter-reset',
		]));
	} else {
		$row[] = elgg_format_element('td', [], '&nbsp;');
	}
	
	$rows[] = elgg_format_element('tr', [], implode(PHP_EOL, $row));
}

if (empty($rows)) {
	return;
}

elgg_require_js('plugins/entity_view_counter/settings');

$body = elgg_format_element('tbody', [], implode(PHP_EOL, $rows));

echo elgg_format_element('table', ['class' => 'elgg-table'], $header . $body);
