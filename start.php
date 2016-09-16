<?php

define('MULTI_DASHBOARD_MAX_TABS', 7);

// register default Elgg events
elgg_register_event_handler('init', 'system', 'multi_dashboard_init');

/**
 * Used to perform initialization of the multi_dashboard features.
 *
 * @return void
 */
function multi_dashboard_init() {

	if (!elgg_is_logged_in()) {
		return;
	}
	
	$base_dir = dirname(__FILE__);
	
	elgg_extend_view('css/elgg', 'css/multi_dashboard');
	
	elgg_register_ajax_view('multi_dashboard/forms/edit');
	
	// dashboard
	elgg_register_plugin_hook_handler('entity:url', 'object', 'multi_dashboard_entity_url');

	elgg_register_event_handler('create', 'object', '\ColdTrick\MultiDashboard\Widgets::linkWidgetToMultiDashboard');
	elgg_register_plugin_hook_handler('route', 'dashboard', '\ColdTrick\MultiDashboard\Router::routeDashboard');
	elgg_register_plugin_hook_handler('action', 'widgets/add', '\ColdTrick\MultiDashboard\Widgets::setMultiDashboardInput');
	elgg_register_plugin_hook_handler('register', 'menu:title', '\ColdTrick\MultiDashboard\Menus::registerMultiDashboardMenuItems');
	elgg_register_plugin_hook_handler('view_vars', 'navigation/menu/elements/section', '\ColdTrick\MultiDashboard\Menus::addTitleMenuSectionStyling');
	elgg_register_plugin_hook_handler('view_vars', 'page/layouts/widgets/add_panel', '\ColdTrick\MultiDashboard\Widgets::setUserWidgets');

	elgg_register_action('multi_dashboard/edit', $base_dir . '/actions/multi_dashboard/edit.php');
	elgg_register_action('multi_dashboard/delete', $base_dir . '/actions/multi_dashboard/delete.php');
	elgg_register_action('multi_dashboard/drop', $base_dir . '/actions/multi_dashboard/drop.php');
	elgg_register_action('multi_dashboard/reorder', $base_dir . '/actions/multi_dashboard/reorder.php');
}


/**
 * This function replaces default Elgg function elgg_widgets
 * Default dashboard tab widgets have no relationship with a custom dashboard
 *
 * @param int    $user_guid guid of the widget owner
 * @param string $context   context of the widgets
 *
 * @return array
 */
function multi_dashboard_get_widgets($user_guid, $context) {
	
	$widgets = elgg_get_entities_from_private_settings([
		'type' => 'object',
		'subtype' => 'widget',
		'owner_guid' => $user_guid,
		'private_setting_name' => 'context',
		'private_setting_value' => $context,
		'wheres' => [
			"NOT EXISTS (
				SELECT 1 FROM " . elgg_get_config('dbprefix') . "entity_relationships r
				WHERE r.guid_one = e.guid
					AND r.relationship = '" . \MultiDashboard::WIDGET_RELATIONSHIP . "')",
		],
		'limit' => 0,
	]);
	if (empty($widgets)) {
		return [];
	}

	$sorted_widgets = [];
	foreach ($widgets as $widget) {
		if (!isset($sorted_widgets[(int)$widget->column])) {
			$sorted_widgets[(int)$widget->column] = [];
		}
		$sorted_widgets[(int)$widget->column][$widget->order] = $widget;
	}

	foreach ($sorted_widgets as $col => $widgets) {
		ksort($sorted_widgets[$col]);
	}

	return $sorted_widgets;
}


/**
 * Sets default dashboard entity URL
 *
 * @param string $hook   "entity:url"
 * @param string $type   "object"
 * @param string $return URL
 * @param array  $params Hook params
 * @return string
 */
function multi_dashboard_entity_url($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params);
	if (!$entity instanceof \MultiDashboard) {
		return;
	}
	return elgg_normalize_url("dashboard/{$entity->guid}");
}