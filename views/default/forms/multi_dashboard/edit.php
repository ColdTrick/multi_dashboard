<?php

$dashboard_type_options = [
	'widgets' => elgg_echo('multi_dashboard:types:widgets'),
	'iframe' => elgg_echo('multi_dashboard:types:iframe')
];

$entity = elgg_extract('entity', $vars);
$guid = null;
$dashboard_type = 'widgets';
$num_columns = 3;
$iframe_url = 'http://';
$iframe_height = 450;

$submit_text = elgg_echo('save');
	
if ($entity) {
	$guid = $entity->getGUID();
	$title = $entity->title;
	
	$dashboard_type = $entity->getDashboardType();
		
	$num_columns = $entity->getNumColumns();
	
	$iframe_url = $entity->getIframeUrl();
	$iframe_height = $entity->getIframeHeight();
	
	$submit_text = elgg_echo('update');
} else {
	$title = get_input('title', '');
	
	if (!empty($title)) {
		$title = str_replace(elgg_get_site_entity()->name . ': ', '', $title);
	}
}

switch ($dashboard_type) {
	case 'iframe':
		$iframe_class = '';
		$widgets_class = 'hidden';
		break;
	case 'widgets':
	default:
		$iframe_class = 'hidden';
		$widgets_class = '';
		break;
}

echo elgg_view_input('text', [
	'name' => 'title',
	'label' => elgg_echo('title'),
	'value' => $title,
	'required' => true,
]);

echo elgg_view_input('select', [
	'name' => 'dashboard_type',
	'label' => elgg_echo('multi_dashboard:types:title'),
	'options_values' => $dashboard_type_options,
	'value' => $dashboard_type,
]);

echo elgg_view_input('select', [
	'name' => 'num_columns',
	'label' => elgg_echo('multi_dashboard:num_columns:title'),
	'field_class' => ['multi-dashboard-types-widgets', $widgets_class],
	'options' => range(1, 6),
	'value' => $num_columns,
]);

echo elgg_view_input('url', [
	'name' => 'iframe_url',
	'label' => elgg_echo('multi_dashboard:iframe_url:title'),
	'help' => elgg_echo('multi_dashboard:iframe_url:description'),
	'field_class' => ['multi-dashboard-types-iframe', $iframe_class],
	'value' => $iframe_url,
]);

echo elgg_view_input('text', [
	'name' => 'iframe_height',
	'value' => $iframe_height,
	'label' => elgg_echo('multi_dashboard:iframe_height:title'),
	'placeholder' => '450',
	'field_class' => ['multi-dashboard-types-iframe', $iframe_class],
	'size' => '5',
	'maxlength' => '6',
	'style' => 'width: 100px;',
]);

echo '<div class="elgg-foot">';

echo elgg_view('input/submit', ['value' => $submit_text]);

if ($entity) {
	echo elgg_view('input/hidden', ['name' => 'guid', 'value' => $guid]);
	echo elgg_view('output/url', [
		'href' => elgg_get_site_url() . 'action/multi_dashboard/delete?guid=' . $guid,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete float-alt',
		'confirm' => true,
	]);
}

echo '</div>';