<?php

gatekeeper();

$guid = (int) get_input('guid');
$entity = null;

if (!empty($guid)) {
	if ($entity = get_entity($guid)) {
		if (!elgg_instanceof($entity, 'object', MultiDashboard::SUBTYPE) || !$entity->canEdit()) {
			unset($entity);
		}
	} else {
		unset($entity);
	}
}

if (!empty($entity)) {
	$title_text = elgg_echo('multi_dashboard:edit', [$entity->title]);
} else {
	$title_text = elgg_echo('multi_dashboard:new');
}

$form = elgg_view_form('multi_dashboard/edit', ['class' => 'elgg-form-alt'], ['entity' => $entity]);

echo elgg_view_module('info', $title_text, $form, ['class' => 'man']);