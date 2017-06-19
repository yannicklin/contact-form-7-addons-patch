<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.twoudia.com/
 * @since      1.0.0
 *
 * @package    Contact_Form_7_Addons_Patch
 * @subpackage Contact_Form_7_Addons_Patch/includes
 */

/**
 * Define the internationalization functionality.
 *
 * @since      1.0.0
 * @package    Contact_Form_7_Addons_Patch
 * @subpackage Contact_Form_7_Addons_Patch/includes
 * @author     Yannick Lin <yannicklin@twoudia.com>
 */
class Contact_Form_7_Addons_Patch_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'contact-form-7-addons-patch',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
