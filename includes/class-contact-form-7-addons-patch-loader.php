<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://github.com/yannicklin/contact-form-7-addons-patch/
 * @since      1.0.0
 *
 * @package    Contact_Form_7_Addons_Patch
 * @subpackage Contact_Form_7_Addons_Patch/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * @package    Contact_Form_7_Addons_Patch
 * @subpackage Contact_Form_7_Addons_Patch/includes
 * @author     Yannick Lin <yannicklin@twoudia.com>
 */
class Contact_Form_7_Addons_Patch_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

    /**
     * The array of shortcodes registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $shortcodes    The shortcodes registered with WordPress to fire when the plugin loads.
     */
    protected $shortcodes;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();
        $this->shortcodes = array();
	}

    /**
     * Add a new shortcode to the collection to be registered with WordPress
     *
     * @since     1.0.0
     * @param     string        $tag           The name of the new shortcode.
     * @param     object        $component      A reference to the instance of the object on which the shortcode is defined.
     * @param     string        $callback       The name of the function that defines the shortcode.
     * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_shortcode( $tag, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->shortcodes = $this->add( $this->shortcodes, $tag, $component, $callback, $priority, $accepted_args );
    }

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
    public function run() {

        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        foreach ( $this->shortcodes as $hook ) {
            add_shortcode( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        /* Check and Start patching */
        add_action('plugins_loaded', array($this, 'launch_patches'), 99);
    }

    /**
         * Load each patch according to target plugin existence
         *
         * @since    1.0.0
         */
    public function launch_patches() {

        if (class_exists( 'cf7_extras' )) {
            // error_log("Found CF7_extras. <br />");
            require_once dirname(__FILE__) . '/modules/contact-form-7-extras/patch.php';
        }
        if (class_exists( 'ContactForm7Datepicker' )) {
            // error_log("Found ContactForm7Datepicker. <br />");
            require_once dirname(__FILE__) . '/modules/contact-form-7-datepicker/patch.php';
        }
        if (is_plugin_active( 'contact-form-7-textarea-wordcount/cf7-textarea-wordcount.php' )) {
            // error_log("Found Contact Form 7 Textarea Wordcount. <br />");
            require_once dirname(__FILE__) . '/modules/contact-form-7-textarea-wordcount/patch.php';
        }
        if (class_exists( 'Contact_Form7_Rich_Text' )) {
            // error_log("Found Contact_Form7_Rich_Text. <br />");
            require_once dirname(__FILE__) . '/modules/rich-text-editor-field-for-contact-form-7/patch.php';
        }
        if (is_plugin_active( 'multifile-upload-field-for-contact-form-7/multifile-for-contact-form-7.php' )) {
            // error_log("Found Contact Form 7 Textarea Wordcount. <br />");
            require_once dirname(__FILE__) . '/modules/multifile-upload-field-for-contact-form-7/patch.php';
        }
        if (is_plugin_active( 'contact-form-7-lead-info-with-country/wpshore_cf7_lead_tracking.php' )) {
            // error_log("Found Contact Form 7 Lead info with country. <br />");
            require_once dirname(__FILE__) . '/modules/contact-form-7-lead-info-with-country/patch.php';
        }
        if (is_plugin_active( 'contact-form-7-image-captcha/cf7-image-captcha.php' )) {
            // error_log("Found Contact Form 7 Image Captcha. <br />");
            require_once dirname(__FILE__) . '/modules/contact-form-7-image-captcha/patch.php';
        }
        if (is_plugin_active( 'contact-form-submissions/contact-form-submissions.php' )) {
            // error_log("Found Contact Form 7 Submissions. <br />");
            require_once dirname(__FILE__) . '/modules/contact-form-submissions/patch.php';
        }
    }
}
