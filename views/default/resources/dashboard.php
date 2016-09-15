<?php

// Ensure that only logged-in users can see this page
elgg_gatekeeper();

// Set context and title
elgg_set_context('dashboard');
elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
$title = elgg_echo('dashboard');

elgg_require_js('multi_dashboard');
		
// wrap intro message in a div
$intro_message = elgg_view('dashboard/blurb');

$params = array(
	'content' => $intro_message,
	'num_columns' => 3,
	'show_access' => false,
);

$md_guid = (int) get_input('multi_dashboard_guid');

if (!empty($md_guid)) {
	$md_object = get_entity($md_guid);
	if ($md_object) {
		if ($md_object->getDashboardType() == 'iframe') {
			$output = elgg_view('output/iframe', [
				'src' => $md_object->getIframeUrl(),
				'style' => "width: 100%; height: {$md_object->getIframeHeight()}px;",
			]);
		} else {
			$params['widgets'] = $md_object->getWidgets();
			$params['num_columns'] = $md_object->getNumColumns();
		}
	}
}

if (!isset($params['widgets'])) {
	$params['widgets'] = multi_dashboard_get_widgets(elgg_get_page_owner_guid(), 'dashboard');
}

if (empty($output)) {
	$output = elgg_view_layout('widgets', $params);
}

$body = elgg_view_layout('one_column', [
	'title' => false,
	'content' => $output,
	'class' => 'multi-dashboard-layout',
]);

echo elgg_view_page($title, $body);