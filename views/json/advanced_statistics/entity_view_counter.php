<?php

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\Http\EntityNotFoundException;
use Elgg\Exceptions\Http\EntityPermissionsException;

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof \ElggEntity) {
	throw new EntityNotFoundException();
}

if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

$section = elgg_extract('section', $vars);
$chart = elgg_extract('chart', $vars);

$view = "advanced_statistics/{$section}/{$chart}";
if (!elgg_view_exists($view)) {
	throw new BadRequestException();
}

echo elgg_view($view, $vars);
