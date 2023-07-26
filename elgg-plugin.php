<?php

use ColdTrick\EntityViewCounter\Bootstrap;

if (!defined('ENTITY_VIEW_COUNTER_ANNOTATION_NAME')) {
	define('ENTITY_VIEW_COUNTER_ANNOTATION_NAME', 'view_counter');
}

require_once(__DIR__ . '/lib/functions.php');

return [
	'plugin' => [
		'version' => '7.1',
	],
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
		'permissions_check:annotate:view_counter' => [
			'all' => [
				'\ColdTrick\EntityViewCounter\Permissions::canAnnotate' => [],
			],
		],
		'seeds' => [
			'database' => [
				'\ColdTrick\EntityViewCounter\Seeder::register' => ['priority' => 600],
			],
		],
		'setting' => [
			'plugin' => [
				'\ColdTrick\EntityViewCounter\Settings::saveSettingEntityTypes' => [],
			],
		],
		'view_vars' => [
			'object/elements/imprint/contents' => [
				'\ColdTrick\EntityViewCounter\Views::addImprint' => [
					'priority' => 600,
				],
			],
		],
	],
	'view_extensions' => [
		'core/settings/statistics' => [
			'entity_view_counter/extends/account/statistics/views' => [],
		],
		'entity_view_counter/extends/account/statistics/views' => [
			'entity_view_counter/extends/account/statistics/views_graph' => [],
		],
	],
	'view_options' => [
		'entity_view_counter/stats' => ['ajax' => true],
		'advanced_statistics/entity_view_counter' => ['ajax' => true],
	],
];
