<?php

/**
 *
 * @link              https://github.com/yannicklin/contact-form-7-addons-patch/
 * @since             1.0.0
 * @package           Contact_Form_7_Addons_Patch
 *
 * @wordpress-plugin
 * Plugin Name:       Patch for 3rd party addons of Contact Form 7
 * Plugin URI:        https://github.com/yannicklin/contact-form-7-addons-patch/
 * Description:       This plugin is used as the packages of patches of common 3rd party addons for Contact Form 7.
 * Version:           1.0.0
 * Author:            Yannick Lin
 * Author URI:        https://github.com/yannicklin/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl.txt
 * Text Domain:       contact-form-7-addons-patch
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-contact-form-7-addons-patch-activator.php
 */
function activate_contact_form_7_addons_patch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-contact-form-7-addons-patch-activator.php';
	Contact_Form_7_Addons_Patch_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-contact-form-7-addons-patch-deactivator.php
 */
function deactivate_contact_form_7_addons_patch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-contact-form-7-addons-patch-deactivator.php';
	Contact_Form_7_Addons_Patch_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_contact_form_7_addons_patch' );
register_deactivation_hook( __FILE__, 'deactivate_contact_form_7_addons_patch' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-contact-form-7-addons-patch.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_contact_form_7_addons_patch() {

	$plugin = new Contact_Form_7_Addons_Patch();
	$plugin->run();

}
run_contact_form_7_addons_patch();
