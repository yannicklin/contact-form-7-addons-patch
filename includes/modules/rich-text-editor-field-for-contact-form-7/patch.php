<?php

/**
 * Target Plugin: Rich Text Editor Field for Contact Form 7 (rich-text-editor-field-for-contact-form-7)
 * Target Version: 1.1.0
 *  Reason: To solve the compability of WPCF7_ShortcodeManager::get_instance and WPCF7_ShortcodeManager->do_shortcode, and also solve the issue of tinyMCE with dashicons display in toolbar buttons
 */

/***  Parts for fix the error in Contact_Form7_Rich_Text ***/
Contact_Form7_Rich_Text_patch::instance();

class Contact_Form7_Rich_Text_patch extends Contact_Form7_Rich_Text {
    private static $instance;

    /* Create instances of plugin classes and initializing the features  */
    public static function instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Contact_Form7_Rich_Text_patch ) ) {
            self::$instance = new Contact_Form7_Rich_Text_patch();

            //error_log("Already comes into Patch for CF7RT_Rich_Text. <br />");
            remove_action('wp_enqueue_scripts',array(Contact_Form7_Rich_Text::instance(),'load_scripts'),9);
            add_action('wp_enqueue_scripts',array(self::$instance,'load_dashicon_editor_buttons_styles'),9);
        }
        return self::$instance;
    }

    public function load_dashicon_editor_buttons_styles(){
        $version = get_bloginfo('version');
        wp_enqueue_style( 'dashicons-css', site_url() . '/wp-includes/css/dashicons.css', null, $version );
        wp_enqueue_style( 'editor-buttons-css', site_url() . '/wp-includes/css/editor.css', null, $version );
    }
}


/***  Parts for fix the error in CF7RT_Rich_Text_Editor ***/
CF7RT_Rich_Text_Editor_patch::instance();

class CF7RT_Rich_Text_Editor_patch extends CF7RT_Rich_Text_Editor
{
    public static function instance()
    {
        static $instance;

        if (!$instance)
            $instance = new self();

        return $instance;
    }

    public function __construct()
    {
        // error_log("Already comes into Patch for CF7RT_Rich_Text_Editor. <br />");

        // Worst Case: Looping $wp_filter to figure out the certain CLASS + FUNCTION + PRIORITY
        $this->CF7AP_remove_class_action( 'init', 'CF7RT_Rich_Text_Editor', 'add_shortcode_rich_text_editor', 5);
        //remove_action('init', array('CF7RT_Rich_Text_Editor', 'add_shortcode_rich_text_editor'), 5);
        add_action('wpcf7_init', array(__CLASS__, 'add_formtag_rich_text_editor'), 5);
        add_filter( 'teeny_mce_buttons', array(__CLASS__, 'RTE_CF7_MCE_button_removal'));

        //global $wp_filter;
        //error_log(print_r($wp_filter['init'], true));
    }

    public static function add_formtag_rich_text_editor()
    {
        wpcf7_remove_form_tag('rich_text_editor');
        wpcf7_remove_form_tag('rich_text_editor*');

        wpcf7_add_form_tag(array('rich_text_editor', 'rich_text_editor*'), array(__CLASS__, 'rich_text_editor_formtag_handler'), true );
    }

    public static function rich_text_editor_formtag_handler($tag)
    {

        $tag = new WPCF7_FormTag($tag);

        if (empty($tag->name))
            return '';

        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type);

        if ($validation_error)
            $class .= ' wpcf7-not-valid';

        $atts = array();

        $atts['rows'] = $tag->get_rows_option('10');
        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_option('id', 'id', true);
        $atts['tabindex'] = $tag->get_option('tabindex', 'int', true);

        if ($tag->has_option('readonly'))
            $atts['readonly'] = 'readonly';

        if ($tag->is_required())
            $atts['aria-required'] = 'true';

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $value = (string)reset($tag->values);

        if ('' !== $tag->content)
            $value = $tag->content;

        if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
            $atts['placeholder'] = $value;
            $value = '';
        }

        if (wpcf7_is_posted() && isset($_POST[$tag->name]))
            $value = stripslashes_deep($_POST[$tag->name]);

        $atts['name'] = $tag->name;

        $pre_formated_atts = $atts;
        $atts = wpcf7_format_atts($atts);

        ob_start();

        $settings = array(
            'wpautop' => false,
            'media_buttons' => false,
            'textarea_name' => $tag->name,
            'textarea_rows' => $pre_formated_atts['rows'],
            'editor_class' => "wpcf7_form_novalidate " . $tag->get_class_option($class),
            "teeny" => true,
            "quicktags" => false,
        );
        wp_editor($value, $tag->name, $settings);

        $rich_editor = ob_get_contents();
        ob_end_clean();

        $html = '<span class="wpcf7-form-control-wrap ' . $tag->name . '">' . $rich_editor . $validation_error . '</span>
	    <script type="text/javascript">
	        jQuery(".wpcf7-form").submit(function(e){
	            jQuery("#' . $tag->name . '").val(tinyMCE.get("' . $tag->name . '").getContent());
	            return true;
	        });
	    </script>';

        return $html;
    }

    public static function RTE_CF7_MCE_button_removal( $buttons ) {
        $remove = array( 'fullscreen', );
        return array_diff( $buttons, $remove );
    }

    /**
         * Remove Class Filter Without Access to Class Object
         * In order to use the core WordPress remove_filter() on a filter added with the callback to a class, you either have to have access to that class object, or it has to be a call to a static method.  This method allows you to remove filters with a callback to a class you don't have access to.
         * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
         *
         * @param string $tag         Filter to remove
         * @param string $class_name  Class name for the filter's callback
         * @param string $method_name Method name for the filter's callback
         * @param int    $priority    Priority of the filter (default 10)
         *
         * @return bool Whether the function is removed.
         */
    function CF7AP_remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
        global $wp_filter;
        // Check that filter actually exists first
        if ( ! isset( $wp_filter[ $tag ] ) ) {
            return FALSE;
        }
        /**
         * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer a simple array, rather it is an object that implements the ArrayAccess interface.
         * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
         *
         * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
         */
        if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
            // Create $fob object from filter tag, to use below
            $fob       = $wp_filter[ $tag ];
            $callbacks = &$wp_filter[ $tag ]->callbacks;
        } else {
            $callbacks = &$wp_filter[ $tag ];
        }
        // Exit if there aren't any callbacks for specified priority
        if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
            return FALSE;
        }
        // Loop through each filter for the specified priority, looking for our class & method
        foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {
            // Filter should always be an array - array( $this, 'method' ), if not goto next
            if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
                continue;
            }
            // If first value in array is not an object, it can't be a class
            if ( ! is_object( $filter['function'][0] ) ) {
                continue;
            }
            // Method doesn't match the one we're looking for, goto next
            if ( $filter['function'][1] !== $method_name ) {
                continue;
            }
            // Method matched, now let's check the Class
            if ( get_class( $filter['function'][0] ) === $class_name ) {
                // WordPress 4.7+ use core remove_filter() since we found the class object
                if ( isset( $fob ) ) {
                    // Handles removing filter, reseting callback priority keys mid-iteration, etc.
                    $fob->remove_filter( $tag, $filter['function'], $priority );
                } else {
                    // Use legacy removal process (pre 4.7)
                    unset( $callbacks[ $priority ][ $filter_id ] );
                    // and if it was the only filter in that priority, unset that priority
                    if ( empty( $callbacks[ $priority ] ) ) {
                        unset( $callbacks[ $priority ] );
                    }
                    // and if the only filter for that tag, set the tag to an empty array
                    if ( empty( $callbacks ) ) {
                        $callbacks = array();
                    }
                    // Remove this filter from merged_filters, which specifies if filters have been sorted
                    unset( $GLOBALS['merged_filters'][ $tag ] );
                }
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
         * Remove Class Action Without Access to Class Object
         *
         * @param string $tag         Action to remove
         * @param string $class_name  Class name for the action's callback
         * @param string $method_name Method name for the action's callback
         * @param int    $priority    Priority of the action (default 10)
         *
         * @return bool               Whether the function is removed.
         */
    function CF7AP_remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
        $this->CF7AP_remove_class_filter( $tag, $class_name, $method_name, $priority );
    }
}
