<?php
/**
 * Register classes for ElggObject subtypes on plugin activation
 */

if (get_subtype_id('object', 'multi_dashboard')) {
	update_subtype('object', 'multi_dashboard', 'MultiDashboard');
} else {
	add_subtype('object', 'multi_dashboard', 'MultiDashboard');
}
