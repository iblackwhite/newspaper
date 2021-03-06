<?php
/*
Plugin Name: Google Analytics by Yoast Premium (shared on wplocker.com)
Plugin URI: https://yoast.com/wordpress/plugins/google-analytics/#utm_source=wordpress&utm_medium=plugin&utm_campaign=wpgaplugin&utm_content=v504
Description: This plugin makes it simple to add Google Analytics to your WordPress site, adding lots of features, e.g. error page, search result and automatic outgoing links and download tracking.
Author: Team Yoast
Version: 1.2.1
Requires at least: 3.8
Author URI: https://yoast.com/
License: GPL v3
Text Domain: yoast-google-analytics-premium
Domain Path: /languages

Google Analytics for WordPress
Copyright (C) 2008-2014, Team Yoast - support@yoast.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// This plugin was originally based on Rich Boakes' Analytics plugin: http://boakes.org/analytics, but has since been rewritten and refactored multiple times.
define( 'GA_YOAST_PREMIUM_VERSION', '1.2.1' );
define( 'GAWP_VERSION', '5.3.2' );

define( 'GAWP_FILE', __FILE__ );

define( 'GAWP_PATH', plugin_basename( __FILE__ ) );

define( 'GAWP_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

if ( file_exists( dirname( GAWP_FILE ) . '/vendor/autoload_52.php' ) ) {
	require dirname( GAWP_FILE ) . '/vendor/autoload_52.php';
}

if ( ! class_exists( 'Yoast_GA_Premium_Autoload', false ) ) {
	require_once 'premium/class-ga-autoload.php';
}

if ( ! class_exists( 'Yoast_GA_Premium', false ) ) {
	Yoast_GA_Premium::init();
}

// Only require the needed classes
if ( is_admin() ) {
	global $yoast_ga_admin;
	$yoast_ga_admin = new Yoast_GA_Admin;

} else {
	global $yoast_ga_frontend;
	$yoast_ga_frontend = new Yoast_GA_Frontend;
}

register_deactivation_hook( __FILE__, array( 'Yoast_GA_Admin', 'ga_deactivation_hook' ) );
