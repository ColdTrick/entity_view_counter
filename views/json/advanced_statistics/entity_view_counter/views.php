<?php

use Elgg\Database\Select;

$user = elgg_extract('entity', $vars);

$result = advanced_statistics_get_default_chart_options('date');

$qb = Select::fromTable('annotations', 'a');
$qb->select("FROM_UNIXTIME(a.time_created, '%x-%v') AS yearweek");
$e = $qb->joinEntitiesTable('a', 'entity_guid');
$qb->addSelect('count(*) AS total');
$qb->where($qb->compare("{$e}.owner_guid", '=', $user->guid, ELGG_VALUE_GUID));
$qb->andWhere($qb->compare('a.name', '=', ENTITY_VIEW_COUNTER_ANNOTATION_NAME, ELGG_VALUE_STRING));
$qb->groupBy("FROM_UNIXTIME(a.time_created, '%x-%v')");
$qb->orderBy('yearweek', 'ASC');

$query_result = $qb->execute()->fetchAllAssociative();

$data = [];
if ($query_result) {
	foreach ($query_result as $row) {
		list ($year, $week) = explode('-', $row['yearweek']);

		$data[] = [
			'x' => date('Y-m-d', strtotime("first monday of january {$year} + {$week} weeks")),
			'y' => (int) $row['total'],
		];
	}
}

$result['data']['datasets'][] = ['data' => $data];

echo json_encode($result);
