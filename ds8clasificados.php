<?php
/**
 * @package DS8 clasificados
 */
/*
Plugin Name: DS8 Clasificados
Plugin URI: https://deseisaocho.com/
Description: FD <strong>Clasificados</strong>
Version: 1.0
Author: JLMA
Author URI: https://deseisaocho.com/wordpress-plugins/
License: GPLv2 or later
Text Domain: ds8clasificados
*/


if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'DS8CLASIFICADOS_VERSION', '3.4' );
define( 'DS8CLASIFICADOS_MINIMUM_WP_VERSION', '5.0' );
define( 'DS8CLASIFICADOS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'DS8Clasificados', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'DS8Clasificados', 'plugin_deactivation' ) );

require_once DS8CLASIFICADOS_PLUGIN_DIR . '/includes/helpers.php';
require_once( DS8CLASIFICADOS_PLUGIN_DIR . 'class.ds8clasificados.php' );

add_action( 'init', array( 'DS8Clasificados', 'init' ) );

/*if ( is_admin() ) {
	require_once( DS8CLASIFICADOS__PLUGIN_DIR . 'class.ds8clasificado-admin.php' );
	add_action( 'init', array( 'DS8Clasificado_Admin', 'init' ) );
}*/