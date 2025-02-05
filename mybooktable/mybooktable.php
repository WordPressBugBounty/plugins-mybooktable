<?php
/*
Plugin Name: MyBookTable Bookstore by Stormhill Media
Plugin URI: https://stormhillmedia.com/all-products/mybooktable/
Description: A WordPress Bookstore Plugin to help authors boost book sales on sites like Amazon and Apple iBooks with great-looking book pages.
Author: Stormhill Media
Author URI: https://www.stormhillmedia.com
Text Domain: mybooktable
Domain Path: /i18n/
Version: 3.5.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define("MBT_VERSION", "3.5.5"); 

/*---------------------------------------------------------*/
/* PHP Version Check                                       */
/*---------------------------------------------------------*/

if(!defined('PHP_VERSION_ID')) {
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

if(PHP_VERSION_ID < 50309) {
	function mbt_php_version_admin_notice() {
		load_plugin_textdomain('mybooktable', false, plugin_basename(dirname(__FILE__))."/i18n");
		?>
		<div id="message" class="error">
			<p>
				<strong><?php esc_attr_e('PHP Out of Date', 'mybooktable'); ?></strong> &#8211;
				<?php 
		/* translators: %s: PHP version on this server */
		printf(esc_attr_e('MyBookTable requires at least PHP 5.3.9. You are currently running PHP %s. Please contact your hosting provider to request that they update your PHP.', 'mybooktable'), PHP_VERSION); ?>
			</p>
		</div>
		<?php
	}
	add_action('admin_notices', 'mbt_php_version_admin_notice');
	return;
}


/*---------------------------------------------------------*/
/* Includes                                                */
/*---------------------------------------------------------*/

require_once(dirname(__FILE__).'/includes/functions.php');
require_once(dirname(__FILE__).'/includes/setup.php');
require_once(dirname(__FILE__).'/includes/templates.php');
require_once(dirname(__FILE__).'/includes/buybuttons.php');
require_once(dirname(__FILE__).'/includes/admin_pages.php');
require_once(dirname(__FILE__).'/includes/post_types.php');
require_once(dirname(__FILE__).'/includes/taxonomies.php');
require_once(dirname(__FILE__).'/includes/metaboxes.php');
require_once(dirname(__FILE__).'/includes/extras/seo.php');
require_once(dirname(__FILE__).'/includes/extras/widgets.php');
require_once(dirname(__FILE__).'/includes/extras/shortcodes.php');
require_once(dirname(__FILE__).'/includes/extras/compatibility.php');
require_once(dirname(__FILE__).'/includes/extras/googleanalytics.php');
require_once(dirname(__FILE__).'/includes/extras/breadcrumbs.php');
require_once(dirname(__FILE__).'/includes/extras/goodreads.php');
require_once(dirname(__FILE__).'/includes/extras/booksorting.php');
require_once(dirname(__FILE__).'/includes/extras/getnoticed.php');
require_once(dirname(__FILE__).'/includes/extras/totallybooked.php');
require_once(dirname(__FILE__).'/includes/extras/customimport.php');
require_once(dirname(__FILE__).'/includes/extras/universalbuybutton.php');
require_once(dirname(__FILE__).'/includes/extras/blocks.php');
require_once(dirname(__FILE__).'/includes/extras/themes.php');
require_once(dirname(__FILE__).'/includes/extras/divi.php');



/*---------------------------------------------------------*/
/* Activate Plugin                                         */
/*---------------------------------------------------------*/

function mbt_activate() {
	mbt_register_post_types();
	mbt_register_taxonomies();
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'mbt_activate');

function mbt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'mbt_deactivate');



/*---------------------------------------------------------*/
/* Initialize Plugin                                       */
/*---------------------------------------------------------*/

function mbt_init() {
	load_plugin_textdomain('mybooktable', false, plugin_basename(dirname(__FILE__))."/i18n");
	mbt_load_settings();
	mbt_update_check();
	mbt_customize_plugins_page();
	//if(mbt_detect_deactivation()) { return; }
	do_action('mbt_init');
}
add_action('plugins_loaded', 'mbt_init');
 
/*
function mbt_detect_deactivation() {
	if($GLOBALS['pagenow'] == "plugins.php" and current_user_can('install_plugins') and isset($_GET['action']) and $_GET['action'] == 'deactivate' and isset($_GET['plugin']) and $_GET['plugin'] == plugin_basename(dirname(__FILE__)).'/mybooktable.php') {
		mbt_update_setting('detect_deactivated', 'detected');
		//mbt_track_event('plugin_deactivated', true);
		//mbt_send_tracking_data();
		return true;
	} else if(mbt_get_setting('detect_deactivated') === 'detected') {
		mbt_update_setting('detect_deactivated', false);
		//mbt_track_event('plugin_activated', true);
	}
	return false;
}
*/

function mbt_customize_plugins_page() {
	add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'mbt_plugin_action_links');
	add_filter('plugin_row_meta', 'mbt_plugin_row_meta', 10, 2);
}

function mbt_plugin_action_links($actions) {
	unset($actions['edit']);
	$actions['settings'] = '<a href="'.admin_url('admin.php?page=mbt_settings').'">'.__('Settings', 'mybooktable').'</a>';
	$actions['help'] = '<a href="'.admin_url('admin.php?page=mbt_help').'">'.__('Help', 'mybooktable').'</a>';
	$actions['upgrade'] = '<a href="https://www.stormhillmedia.com/all-products/mybooktable/upgrades/" target="_blank">'.__('Purchase Upgrade', 'mybooktable').'</a>';
	return $actions;
}

function mbt_plugin_row_meta($links, $file) {
	if($file == plugin_basename(__FILE__)) {
		$links[] = '<a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/mybooktable?filter=5#postform">'.__('Write a Review', 'mybooktable').'</a>';
	}
	return $links;
}