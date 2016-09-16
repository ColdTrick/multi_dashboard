require(['elgg', 'jquery', 'elgg/widgets'], function(elgg, $, widgets) {

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
	$('.elgg-menu-title-multi_dashboard .multi-dashboard-widgets-tab').not('.elgg-state-selected').droppable({
		accept: '.elgg-module-widget',
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
			var multi_dashboard_sort_stop = $('.elgg-widgets').sortable('option', 'stop');
			$('.elgg-widgets').sortable('option', 'stop', function(){
				$('.elgg-widgets').sortable('option', 'stop', multi_dashboard_sort_stop);
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
		items: 'li.multi-dashboard-tab',
		tolerance: 'pointer',
		axis: 'x',
		cursor: 'move',
		distance: 5,
		delay: 15,
		helper: 'clone',
		forceHelperSize: true,
		update: function(event, ui) {
			var $order = $('.elgg-menu-title-multi_dashboard li.multi-dashboard-tab a').map(function () { return this.id; }).get();
			
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
