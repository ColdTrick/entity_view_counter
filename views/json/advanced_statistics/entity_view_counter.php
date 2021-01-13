<?php

use Elgg\BadRequestException;

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof \ElggEntity) {
	throw new \Elgg\EntityNotFoundException();
}

if (!$entity->canEdit()) {
	throw new \Elgg\EntityPermissionsException();
}

$section = elgg_extract('section', $vars);
$chart = elgg_extract('chart', $vars);

$view = "advanced_statistics/{$section}/{$chart}";
if (!elgg_view_exists($view)) {
	throw new BadRequestException();
}

echo elgg_view($view, $vars);
