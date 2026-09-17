<?php

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\Http\EntityNotFoundException;
use Elgg\Exceptions\Http\EntityPermissionsException;

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof \ElggEntity) {
	throw new EntityNotFoundException();
}

$can_view = false;
if (elgg_get_plugin_setting('view_permission', 'entity_view_counter') === 'logged_in' && elgg_is_logged_in()) {
	$can_view = true;
} elseif ($entity->canEdit()) {
	$can_view = true;
}

if (!$can_view) {
	throw new EntityPermissionsException();
}

$section = elgg_extract('section', $vars);
$chart = elgg_extract('chart', $vars);

$view = "advanced_statistics/{$section}/{$chart}";
if (!elgg_view_exists($view)) {
	throw new BadRequestException();
}

echo elgg_view($view, $vars);
