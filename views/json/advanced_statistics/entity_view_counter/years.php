<?php

use Elgg\Database\Select;

$entity = elgg_extract('entity', $vars);

$result = advanced_statistics_get_default_chart_options('bar');

$qb = Select::fromTable('annotations', 'a');
$qb->select("FROM_UNIXTIME(a.time_created, '%Y') AS year");
$qb->addSelect('count(*) AS total');
$qb->where($qb->compare('a.entity_guid', '=', $entity->guid, ELGG_VALUE_GUID));
$qb->andWhere($qb->compare('a.name', '=', ENTITY_VIEW_COUNTER_ANNOTATION_NAME, ELGG_VALUE_STRING));
$qb->groupBy("FROM_UNIXTIME(a.time_created, '%Y')");
$qb->orderBy('year', 'ASC');

$query_result = $qb->execute()->fetchAllAssociative();

$data = [];
if ($query_result) {
	foreach ($query_result as $row) {
		$data[] = [
			'x' => $row['year'],
			'y' => (int) $row['total'],
		];
	}
}

$result['data']['datasets'][] = ['data' => $data];

echo json_encode($result);
