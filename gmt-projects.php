<?php

/**
 * Plugin Name: GMT Projects
 * Plugin URI: https://github.com/cferdinandi/gmt-projects/
 * GitHub Plugin URI: https://github.com/cferdinandi/gmt-projects/
 * Description: Adds a Projects custom post type.
 * Version: 1.1.0
 * Author: Chris Ferdinandi
 * Author URI: http://gomakethings.com
 * License: MIT
 */


// Options & Helpers
require_once( plugin_dir_path( __FILE__ ) . 'includes/helpers.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/options.php' );

// Custom Post Type
require_once( plugin_dir_path( __FILE__ ) . 'includes/cpt.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/metabox.php' );


// Flush rewrite rules on activation and deactivation
function gmt_projects_flush_rewrites() {
	projects_add_custom_post_type();
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'gmt_projects_flush_rewrites' );