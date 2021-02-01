<?php

use ColdTrick\EntityViewCounter\Bootstrap;

define('ENTITY_VIEW_COUNTER_ANNOTATION_NAME', 'view_counter');

require_once(__DIR__ . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'actions' => [
		'entity_view_counter/admin/reset_counter' => [
			'access' => 'admin',
		],
	],
	'events' => [
		'delete' => [
			'object' => [
				'\ColdTrick\EntityViewCounter\Delete::deleteViews' => [],
			],
		],
	],
	'hooks' => [
		'permissions_check:annotate' => [
			'all' => [
				'\ColdTrick\EntityViewCounter\Permissions::canAnnotate' => [],
			],
		],
		'view_vars' => [
			'object/elements/imprint/contents' => [
				'\ColdTrick\EntityViewCounter\Views::addImprint' => [
					'priority' => 600,
				],
			],
		],
		'setting' => [
			'plugin' => [
				'\ColdTrick\EntityViewCounter\Settings::saveSettingEntityTypes' => [],
			],
		],
	],
	'view_extensions' => [
		'core/settings/statistics' => [
			'entity_view_counter/extends/account/statistics/views' => [],
		],
	],
];
