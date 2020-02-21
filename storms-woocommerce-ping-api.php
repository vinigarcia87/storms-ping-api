<?php
/**
 * Plugin Name: Storms WooCommerce Ping API
 * Plugin URI: https://github.com/vinigarcia87/storms-woocommerce-ping-api
 * Description: API para que aplicações externas possam verificar o status do ecommerce
 * Author: Storms Websolutions - Vinicius Garcia
 * Author URI: http://storms.com.br/
 * Copyright: (c) Copyright 2012-2020, Storms Websolutions
 * License: GPLv2 - GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * Version: 1.0
 *
 * WC requires at least: 3.9.2
 * WC tested up to: 3.9.2
 *
 * Text Domain: storms
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// Register Prosoftin API classes
	function storms_wc_ping_api_register_api() {
		include_once __DIR__ . '/class-storms-wc-ping-api.php';
		$controller = new Storms_WC_Ping_API();
		$controller->register_routes();
	}
	add_action( 'rest_api_init', 'storms_wc_ping_api_register_api' );

}
