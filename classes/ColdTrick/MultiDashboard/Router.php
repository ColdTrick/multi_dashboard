<?php

namespace ColdTrick\MultiDashboard;

class Router {
		
	/**
	 * Routes the multidashboard pages
	 *
	 * @param string $hook_name    name of the hook
	 * @param string $entity_type  type of the hook
	 * @param string $return_value current return value
	 * @param array  $params       hook parameters
	 *
	 * @return void
	 */
	public static function routeDashboard($hook_name, $entity_type, $return_value, $params) {
		$page = elgg_extract('segments', $return_value);
	
		$guid = (int) elgg_extract(0, $page);
		if (empty($guid)) {
			return;
		}
	
		if (get_entity($guid)) {
			set_input('multi_dashboard_guid', $guid);
		} else {
			register_error(elgg_echo('changebookmark'));
		}
	}
}