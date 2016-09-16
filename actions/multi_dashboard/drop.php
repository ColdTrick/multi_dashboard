<?php

$widget_guid = (int) get_input('widget_guid');
$multi_dashboard_guid = (int) get_input('multi_dashboard_guid');
if (!elgg_entity_gatekeeper($widget_guid, 'object', 'widget', false)) {
	register_error(elgg_echo('error:missing_data'));
	forward(REFERER);
}
$widget = get_entity($widget_guid);

// remove widget from any other multi dashboard
remove_entity_relationships($widget->getGUID(), \MultiDashboard::WIDGET_RELATIONSHIP);

// check if we dropped on a multi dashboard
if (!empty($multi_dashboard_guid)) {
	if (!elgg_entity_gatekeeper($multi_dashboard_guid, 'object', \MultiDashboard::SUBTYPE, false)) {
		register_error(elgg_echo('error:missing_data'));
		forward(REFERER);
	}
	$dashboard = get_entity($multi_dashboard_guid);
	
	
	// we need to drop the widget on the first column, last position
	$pos = 10;
	$widgets = $dashboard->getWidgets();
	if (!empty($widgets)) {
		if (isset($widgets[1])) {
			$max_pos = max(array_keys($widgets[1]));
			
			if ($max_pos >= $pos) {
				$pos = $max_pos + 10;
			}
		}
	}
	
	$widget->column = 1;
	$widget->order = $pos;
	
	$widget->save();
	
	// add the widget to the dashboard
	$widget->addRelationship($dashboard->getGUID(), \MultiDashboard::WIDGET_RELATIONSHIP);
} else {
	$widget->column = 1;
	$widget->order = 999999999999;
		
	$widget->save();
}

system_message(elgg_echo('multi_dashboard:actions:drop:success'));
