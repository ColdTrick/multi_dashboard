<?php

namespace ColdTrick\MultiDashboard;

class Widgets {
		
	/**
	 * Adds special data to widgets that are added on multidashboards
	 *
	 * @param string $hook_name    name of the hook
	 * @param string $entity_type  type of the hook
	 * @param string $return_value current return value
	 * @param array  $params       hook parameters
	 *
	 * @return void
	 */
	public static function setMultiDashboardInput($hook_name, $entity_type, $return_value, $params) {
		$widget_context = get_input('context'); // dashboard_<guid>;
		if (empty($widget_context)) {
			return;
		}
		
		if (stristr($widget_context, 'dashboard_') === false) {
			return;
		}
		
		list($context, $guid) = explode('_', $widget_context);
		
		set_input('context', $context);
		set_input('multi_dashboard_guid', $guid);
	}
	
	/**
	 * Links a widget to a multidashboard
	 *
	 * @param string $event       name of the system event
	 * @param string $object_type type of the event
	 * @param mixed  $object      object related to the event
	 *
	 * @return void
	 */
	public static function linkWidgetToMultiDashboard($event, $object_type, $object) {
	
		if (!elgg_instanceof($object, 'object', 'widget', 'ElggWidget')) {
			return;
		}

		$dashboard_guid = get_input('multi_dashboard_guid');
		if (empty($dashboard_guid)) {
			return;
		}
	
		$dashboard = get_entity($dashboard_guid);
		if (!elgg_instanceof($dashboard, 'object', \MultiDashboard::SUBTYPE, 'MultiDashboard')) {
			return;
		}
	
		// Adds a relation between a widget and a multidashboard object
		add_entity_relationship($object->getGUID(), \MultiDashboard::WIDGET_RELATIONSHIP, $dashboard->getGUID());
	}
	
	/**
	 * Set the user widgets for the widgets add panel
	 *
	 * @param string $hook_name    name of the hook
	 * @param string $entity_type  type of the hook
	 * @param string $return_value current return value
	 * @param array  $params       hook parameters
	 *
	 * @return void
	 */
	public static function setUserWidgets($hook_name, $entity_type, $return_value, $params) {
		if (!elgg_is_xhr()) {
			return;
		}
		$context_stack = (array) get_input('context_stack');
		if (!in_array('dashboard', $context_stack) || in_array('admin', $context_stack)) {
			return;
		}
		
		$multi_dashboard_guid = (int) get_input('multi_dashboard_guid');
		
		$md_object = get_entity($multi_dashboard_guid);
		if ($md_object) {
			$return_value['user_widgets'] = $md_object->getWidgets();
			$return_value['widget_context'] = "dashboard_{$multi_dashboard_guid}";
		} else {
			$return_value['user_widgets'] = multi_dashboard_get_widgets(elgg_get_page_owner_guid(), 'dashboard');
		}
		
		return $return_value;
	}
}