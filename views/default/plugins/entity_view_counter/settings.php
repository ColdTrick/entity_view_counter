<?php

/** @var \ElggPlugin $plugin */
$plugin = elgg_extract('entity', $vars);

// permissions
$permissions = elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('entity_view_counter:settings:permissions:view_details'),
	'name' => 'params[view_permission]',
	'options_values' => [
		'edit' => elgg_echo('entity_view_counter:settings:permissions:view_details:edit'),
		'logged_in' => elgg_echo('entity_view_counter:settings:permissions:view_details:logged_in'),
	],
	'value' => $plugin->view_permission,
]);

echo elgg_view_module('info', elgg_echo('entity_view_counter:settings:permissions'), $permissions);

// tracked entity types
elgg_import_esm('plugins/entity_view_counter/settings');

$tracked_entities = elgg_view('output/longtext', [
	'value' => elgg_echo('entity_view_counter:settings:tracked_entities:description'),
]);

// need an empty input in order to be able to unset everything
$tracked_entities .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'params[entity_types]',
]);

// listing
$row = [
	elgg_format_element('th', [], elgg_echo('entity_view_counter:settings:tracked_entities:entity_type')),
	elgg_format_element('th', ['class' => 'center'], elgg_echo('entity_view_counter:settings:tracked_entities:delete')),
];

$header = elgg_format_element('thead', [], elgg_format_element('tr', [], implode(PHP_EOL, $row)));

$rows = [];

$object_types = elgg_extract('object', elgg_entity_types_with_capability('searchable'), []);
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
	
	$rows[] = elgg_format_element('tr', [], implode(PHP_EOL, $row));
}

if (empty($rows)) {
	return;
}

$body = elgg_format_element('tbody', [], implode(PHP_EOL, $rows));

$tracked_entities .= elgg_format_element('table', ['class' => 'elgg-table'], $header . $body);

echo elgg_view_module('info', elgg_echo('entity_view_counter:settings:tracked_entities'), $tracked_entities);
