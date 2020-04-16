<?php

use Elgg\Database\Select;

$object_types = (array) get_registered_entity_types('object');

echo elgg_view('output/longtext', [
	'value' => elgg_echo('entity_view_counter:settings:description'),
]);

// get counts for all subtypes
// this could take a while
set_time_limit(0);

$select = Select::fromTable('annotations', 'a');
$e = $select->joinEntitiesTable('a', 'entity_guid');
$select->select('count(*) as total')
	->addSelect("{$e}.type")
	->addSelect("{$e}.subtype")
	->where($select->compare("{$e}.type", '=', 'object', ELGG_VALUE_STRING))
	->andWhere($select->compare("{$e}.subtype", 'in', $object_types, ELGG_VALUE_STRING))
	->andWhere($select->compare("a.name", '=', ENTITY_VIEW_COUNTER_ANNOTATION_NAME, ELGG_VALUE_STRING))
	->groupBy("{$e}.type")
	->addGroupBy("{$e}.subtype");

$counts = elgg()->db->getData($select, function ($row) {
	return (array) $row;
});
// order counts into easier format
$ordered_counts = [];
foreach ($counts as $count) {
	$ordered_counts[$count['subtype']] = (int) $count['total'];
}

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
	
	$count = elgg_extract($subtype, $ordered_counts, 0);
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
