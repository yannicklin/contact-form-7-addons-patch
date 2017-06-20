<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/yannicklin/contact-form-7-addons-patch
 * @since      1.0.0
 *
 * @package    Contact_Form_7_Addons_Patch
 * @subpackage Contact_Form_7_Addons_Patch/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Contact_Form_7_Addons_Patch
 * @subpackage Contact_Form_7_Addons_Patch/admin
 * @author     Yannick Lin <yannicklin@twoudia.com>
 */
class Contact_Form_7_Addons_Patch_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/contact-form-7-addons-patch-admin.css',
            array(),
            $this->version, 'all'
        );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/contact-form-7-addons-patch-admin.js',
            array( 'jquery' ),
            $this->version, false
        );
	}

    /**
     * Add an options page under the Settings submenu
     *
     * @since    1.0.0
     */
    public function add_options_page() {

        $this->plugin_screen_hook_suffix = add_options_page(
            __( 'Patches of Contact Form 7 Addons - Settings', 'contact-form-7-addons-patch' ),
            __( 'Patches of CF7 Addons', 'contact-form-7-addons-patch' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_options_page' )
        );
    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_options_page() {
        include_once 'partials/contact-form-7-addons-patch-admin-display.php';
    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function plugin_active_check_html_output($plugin_name = '') {
        $result = '';

        switch ($plugin_name) {
            case 'contact-form-7-datepicker':
                $name_detect  = 'ContactForm7Datepicker';
                $method_detect = 'CLASS';
                break;
            case 'contact-form-7-extras':
                $name_detect  = 'cf7_extras';
                $method_detect = 'CLASS';
                break;
            case 'cf7-image-captcha':
                $name_detect  = 'contact-form-7-image-captcha/cf7-image-captcha.php';
                $method_detect = 'PATH';
                break;
            case 'wpshore_cf7_lead_tracking':
                $name_detect  = 'contact-form-7-lead-info-with-country/wpshore_cf7_lead_tracking.php';
                $method_detect = 'PATH';
                break;
            case 'contact-form-7-textarea-wordcount':
                $name_detect  = 'contact-form-7-textarea-wordcount/cf7-textarea-wordcount.php';
                $method_detect = 'PATH';
                break;
            case 'contact-form-submissions':
                $name_detect  = 'contact-form-submissions/contact-form-submissions.php';
                $method_detect = 'PATH';
                break;
            case 'multifile-upload-field-for-contact-form-7':
                $name_detect  = 'multifile-upload-field-for-contact-form-7/multifile-for-contact-form-7.php';
                $method_detect = 'PATH';
                break;
            case 'rich-text-editor-field-for-contact-form-7':
                $name_detect  = 'Contact_Form7_Rich_Text';
                $method_detect = 'CLASS';
                break;
            default:
                $name_detect  = '';
                $method_detect = '';
                break;
        }

        if ( $this->check_plugin_active_or_not($name_detect, $method_detect) ) {
            $result = '<div class="dashicons-before dashicons-thumbs-up cf7pa-3rdparty-plugin active">' . __('ACTIVE', 'contact-form-7-addons-patch') . '</div>';
        } else {
            $result = '<div class="dashicons-before dashicons-thumbs-down cf7pa-3rdparty-plugin inactive">' . __('INACTIVE', 'contact-form-7-addons-patch') . '</div>';
        }

        return $result;
    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function check_plugin_active_or_not ($string = '', $method = 'CLASS') {
        $result = false;

        if ('CLASS' === $method) {
            if (class_exists( $string )) {
                $result = true;
            }
        } elseif ('PATH' === $method) {
            if (is_plugin_active( $string )) {
                $result = true;
            }
        }
        return $result;
    }

}
