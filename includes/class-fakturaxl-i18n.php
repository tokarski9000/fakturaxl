<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link  rt-studio.pl
 * @since 1.0.0
 *
 * @package    Fakturaxl
 * @subpackage Fakturaxl/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fakturaxl
 * @subpackage Fakturaxl/includes
 * @author     Rafał Tokarski <kontakt@rt-studio.pl>
 */
class Fakturaxl_i18n
{


    /**
     * Load the plugin text domain for translation.
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'fakturaxl',
            false,
            dirname(dirname(plugin_basename(__FILE__))).'/languages/'
        );

    }//end load_plugin_textdomain()


}//end class
