<?php

$guid = (int) get_input('guid');

if (empty($guid)) {
	register_error(elgg_echo('error:missing_data'));
	forward('dashboard');
}

$entity = get_entity($guid);
if (!elgg_instanceof($entity, 'object', MultiDashboard::SUBTYPE)) {
	register_error(elgg_echo('error:missing_data'));
	forward('dashboard');
}

if (!$entity->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward('dashboard');
}

$title = $entity->title;

if ($entity->delete()) {
	system_message(elgg_echo('multi_dashboard:actions:delete:success', [$title]));
} else {
	register_error(elgg_echo('multi_dashboard:actions:delete:error:delete', [$title]));
}

forward('dashboard');
