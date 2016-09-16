<?php

$guid = (int) get_input('guid');
$title = get_input('title');
$dashboard_type = get_input('dashboard_type', 'widgets');
$num_columns = (int) get_input('num_columns', 3);
$iframe_url = get_input('iframe_url');
$iframe_height = (int) get_input('iframe_height');

$forward_url = REFERER;

if (empty($title)) {
	register_error(elgg_echo('multi_dashboard:actions:edit:error:input'));
	forward(REFERER);
}

if (!empty($guid)) {
	if ($entity = get_entity($guid)) {
		if (!elgg_instanceof($entity, 'object', MultiDashboard::SUBTYPE)) {
			unset($entity);
			register_error(elgg_echo('error:missing_data'));
		}
	} else {
		register_error(elgg_echo('error:missing_data'));
	}
} else {
	$entity = new MultiDashboard();
	if (!$entity->save()) {
		unset($entity);
		register_error(elgg_echo('save:fail'));
	}
}

if (!empty($entity) && $entity->canEdit()) {
	// set title
	$entity->title = $title;
	
	// set type
	switch ($dashboard_type) {
		case 'iframe':
			$entity->setDashboardType('iframe');
			
			$entity->setIframeUrl($iframe_url);
			$entity->setIframeHeight($iframe_height);
			
			break;
		case 'widgets':
		default:
			$entity->setDashboardType('widgets');
			
			$entity->setNumColumns($num_columns);
			break;
	}
	
	if ($entity->save()) {
		$forward_url = $entity->getURL();
		
		system_message(elgg_echo('multi_dashboard:actions:edit:success'));
	} else {
		register_error(elgg_echo('save:fail'));
	}
}

forward($forward_url);
