require(['elgg', 'jquery', 'elgg/widgets'], function(elgg, $, widgets) {

	var widget_manager_multi_dashboard_dropped = false;
		
	// edit dashboard link
	$(document).on('click', '.elgg-menu-title-multi_dashboard .elgg-icon-settings-alt', function(event) {
		var href = elgg.normalize_url('ajax/view/multi_dashboard/forms/edit?guid=' + $(this).parent().attr('rel'));
		$.colorbox({
			href : href,
			innerWidth: 400
		});
		event.preventDefault();
	});

	// adds the ability to move widgets between dashboards
	$('#widget-manager-multi-dashboard-tabs .widget-manager-multi-dashboard-tab-widgets').not('.elgg-state-selected').droppable({
		accept: '.elgg-module-widget',
		activeClass: 'widget-manager-multi-dashboard-tab-active',
		hoverClass: 'widget-manager-multi-dashboard-tab-hover',
		tolerance: 'pointer',
		drop: function(event, ui) {
			
			// elgg-widget-<guid>
			var guidString = ui.draggable.attr('id');
			guidString = guidString.substr(guidString.indexOf('elgg-widget-') + 'elgg-widget-' . length);

			// tab guid
			var tabGuid = $(this).find('a:first').attr('rel');
			if (tabGuid == 'nofollow') {
				tabGuid = 0;
			}

			ui.draggable.hide();

			// prevent the widget from being moved
			widget_manager_multi_dashboard_sort_stop = $('.elgg-widgets').sortable('option', 'stop');
			$('.elgg-widgets').sortable('option', 'stop', function(){
				$('.elgg-widgets').sortable('option', 'stop', widget_manager_multi_dashboard_sort_stop);
			});
			
			elgg.action('multi_dashboard/drop', {
				data: {
					widget_guid: guidString,
					multi_dashboard_guid: tabGuid
				},
				success: function(){
					ui.draggable.remove();
				},
				error: function(){
					ui.draggable.show();
				}
			});
		}
	});

	$('.elgg-menu-title-multi_dashboard').sortable({
		items: 'li.widget-manager-multi-dashboard-tab',
		tolerance: 'pointer',
		axis: 'x',
		cursor: 'move',
		distance: 5,
		delay: 15,
		forcePlaceholderSize: true,
		update: function(event, ui) {
			$order = $(this).sortable('toArray');
			
			elgg.action('multi_dashboard/reorder', {
				data: {
					order: $order
				}
			});
		}
	});
		
	$(document).on('change', '.elgg-form-multi-dashboard-edit select[name="dashboard_type"]', function() {
		
		switch($(this).val()){
			case 'iframe':
				$('.elgg-form-multi-dashboard-edit .multi-dashboard-types-widgets').addClass('hidden');
				$('.elgg-form-multi-dashboard-edit .multi-dashboard-types-iframe').removeClass('hidden');
	
				break;
			default:
				$('.elgg-form-multi-dashboard-edit .multi-dashboard-types-iframe').addClass('hidden');
				$('.elgg-form-multi-dashboard-edit .multi-dashboard-types-widgets').removeClass('hidden');
			
				break;
		}
	});
});
