<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    rt-studio.pl
 * @since   1.0.0
 * @package Fakturaxl
 *
 * @wordpress-plugin
 * Plugin Name:       Faktura XL
 * Plugin URI:        fakturaxl
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Rafał Tokarski
 * Author URI:        rt-studio.pl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fakturaxl
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/*
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('FAKTURAXL_VERSION', '1.0.0');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fakturaxl-activator.php
 */
function activate_fakturaxl()
{
    include_once plugin_dir_path(__FILE__).'includes/class-fakturaxl-activator.php';
    Fakturaxl_Activator::activate();

}//end activate_fakturaxl()


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fakturaxl-deactivator.php
 */
function deactivate_fakturaxl()
{
    include_once plugin_dir_path(__FILE__).'includes/class-fakturaxl-deactivator.php';
    Fakturaxl_Deactivator::deactivate();

}//end deactivate_fakturaxl()


register_activation_hook(__FILE__, 'activate_fakturaxl');
register_deactivation_hook(__FILE__, 'deactivate_fakturaxl');

/*
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__).'includes/class-fakturaxl.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_fakturaxl()
{
    $plugin = new Fakturaxl();
    $plugin->run();

}//end run_fakturaxl()


run_fakturaxl();
