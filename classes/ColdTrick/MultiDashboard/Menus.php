<?php

namespace ColdTrick\MultiDashboard;

class Menus {

	/**
	 * Adds extra styling to the multi dashboard title section
	 *
	 * @param string $hook_name    name of the hook
	 * @param string $entity_type  type of the hook
	 * @param array  $return_value current return value
	 * @param array  $params       hook parameters
	 *
	 * @return boolean
	 */
	public static function addTitleMenuSectionStyling($hook_name, $entity_type, $return_value, $params) {
		$class = elgg_extract('class', $return_value);
		if (stristr($class, 'elgg-menu-title-multi_dashboard')) {
			$return_value['class'] .= ' elgg-tabs';
			return $return_value;
		}
	}
	
	/**
	 * Hook to register menu items on the dashboard pages
	 *
	 * @param string $hook_name    name of the hook
	 * @param string $entity_type  type of the hook
	 * @param array  $return_value current return value
	 * @param array  $params       hook parameters
	 *
	 * @return boolean
	 */
	public static function registerMultiDashboardMenuItems($hook_name, $entity_type, $return_value, $params) {
		if (elgg_in_context('admin') || !elgg_in_context('dashboard')) {
			return;
		}
		
		// set the multi dashboard guid on the add widgets button
		foreach ($return_value as $current_item) {
			if ($current_item->getName() == 'widgets:add') {
				// add a multi dashboard guid to the menu item
				$current_opts = $current_item->{'data-colorbox-opts'};
				if (!empty($current_opts)) {
					$current_opts = json_decode($current_opts, true);
					$current_opts['href'] = elgg_http_add_url_query_elements($current_opts['href'], ['multi_dashboard_guid' => (int) get_input('multi_dashboard_guid')]);
					
					$current_item->{'data-colorbox-opts'} = json_encode($current_opts);
				}
			}
		}
	
		// add a fake menu item for to prevent styling issues
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'force_item',
			'text' => elgg_echo('dashboard'),
			'href' => '#',
			'link_class' => 'hidden',
		]);
		
		elgg_load_js('lightbox');
		elgg_load_css('lightbox');
		
		$max_tab_title_length = 10;
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'dashboard',
			'text' => elgg_echo('dashboard'),
			'href' => 'dashboard',
			'title' => elgg_echo('dashboard'),
			'item_class' => 'multi-dashboard-widgets-tab',
			'section' => 'multi_dashboard',
			'priority' => 1,
		]);
		
		$md_entities = elgg_get_entities([
			'type' => 'object',
			'subtype' => \MultiDashboard::SUBTYPE,
			'limit' => false,
			'owner_guid' => elgg_get_logged_in_user_guid(),
			'order_by' => 'e.time_created ASC',
		]);
		
		if ($md_entities) {
			foreach ($md_entities as $entity) {
				$title = elgg_strip_tags($entity->title);
			
				if (strlen($title) > $max_tab_title_length) {
					$title = substr($title, 0, $max_tab_title_length);
				}
			
				if ($entity->canEdit()) {
					$title .= elgg_view_icon('settings-alt', ['class' => 'hidden']);
				}

				$item_class = ['multi-dashboard-tab'];
				if ($entity->getDashboardType() == 'widgets') {
					$item_class[] = 'multi-dashboard-widgets-tab';
				}
				
				$return_value[] = \ElggMenuItem::factory([
					'name' => 'dashboard_' . $entity->guid,
					'href' => $entity->getURL(),
					'text' => $title,
					'rel' => $entity->getGUID(),
					'id' => $entity->getGUID(),
					'section' => 'multi_dashboard',
					'item_class' => $item_class,
					'priority' => $entity->order ?: $entity->time_created,
				]);
				
// 				'text' => $tab_title . $edit_icon,
// 			'href' => $entity->getURL(),
// 			'title' => $entity->title,
// 			'selected' => $selected,
// 			'rel' => $entity->getGUID(),
// 			'id' => $entity->getGUID(),
// 			'class' => 'widget-manager-multi-dashboard-tab widget-manager-multi-dashboard-tab-' . $entity->getDashboardType(),

		
// 		$tabs[$order] = [
// 			'text' => $tab_title . $edit_icon,
// 			'href' => $entity->getURL(),
// 			'title' => $entity->title,
// 			'selected' => $selected,
// 			'rel' => $entity->getGUID(),
// 			'id' => $entity->getGUID(),
// 			'class' => 'widget-manager-multi-dashboard-tab widget-manager-multi-dashboard-tab-' . $entity->getDashboardType(),
// 		];
			}
		}
		
		if (is_array($md_entities) && count($md_entities) < MULTI_DASHBOARD_MAX_TABS) {
		
			$return_value[] = \ElggMenuItem::factory([
				'name' => 'dashboard_add',
				'text' => elgg_view_icon('round-plus'),
				'href' => 'javascript:return void();',
				'link_class' => 'elgg-lightbox',
				'title' => elgg_echo('add'),
				'data-colorbox-opts' => json_encode([
					'href' => elgg_normalize_url('ajax/view/multi_dashboard/forms/edit'),
					'innerWidth' => 400,
				]),
				'section' => 'multi_dashboard',
				'priority' => 9999999999999999,
			]);
		}
		
		return $return_value;
	}
}