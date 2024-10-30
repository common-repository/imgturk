<?php if (!defined('ABSPATH')) exit; ?>
<?php
/**
 * @package
 * @author    Johny Pringles <johnypringles@gmail.com>
 * @license   GPL-3.0+
 * @link      https://imgturk.com/
 * @copyright 2017 ImgTurk
 *
 * @wordpress-plugin
 * Plugin Name:       ImgTurk
 * Plugin URI:        https://imgturk.com
 * Description:       Instagram sidebar plugin for WordPress
 * Version:           1.0.1
 * Author:            Johny Pringles
 * Text Domain:       imgturk.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
  */

class ImgTurk extends WP_Widget {

    protected $widget_slug = 'imgturk';

    /*--------------------------------------------------*/
    /* Constructor
    /*--------------------------------------------------*/

    /**
     * Specifies the classname and description, instantiates the widget,
     * loads localization files, and includes necessary stylesheets and JavaScript.
     */
    public function __construct() {

        // load plugin text domain
        add_action( 'init', array( $this, 'widget_textdomain' ) );

        $title = 'ImgTurk';

        parent::__construct(
            $this->get_widget_slug(),
            __( 'ImgTurk', $this->get_widget_slug() ),
            array(
                'classname'  => $this->get_widget_slug().'-class',
                'description' => __( 'Instagram sidebar widget.', $this->get_widget_slug() )
            )
        );

        // Register admin styles and scripts
        add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

        // Register site styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

        // Refreshing the widget's cached output with each new post
        add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
        add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

    } // end constructor


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

    /*--------------------------------------------------*/
    /* Widget API Functions
    /*--------------------------------------------------*/

    /**
     * Outputs the content of the widget.
     *
     * @param array args  The array of form elements
     * @param array instance The current instance of the widget
     */
    public function widget( $args, $instance ) {

        // Check if there is a cached output
        $cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

        if ( !is_array( $cache ) )
            $cache = array();

        if ( ! isset ( $args['widget_id'] ) )
            $args['widget_id'] = $this->id;

        if ( isset ( $cache[ $args['widget_id'] ] ) )
            return print $cache[ $args['widget_id'] ];

        // go on with your widget logic, put everything into a string and …

        extract( $args, EXTR_SKIP );

        $imgturk_content_type = $instance['content_type'];
        $imgturk_content_id = $instance['content_id'];

        $widget_string = $before_widget;

        if (!$imgturk_content_type || !$imgturk_content_id) {
            include( plugin_dir_path( __FILE__ ) . 'views/error.php' );
        } else {
            ob_start();
            include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
            $widget_string .= ob_get_clean();
            $widget_string .= $after_widget;

            $cache[ $args['widget_id'] ] = $widget_string;

            wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );
        }

        print $widget_string;

    } // end widget


    public function flush_widget_cache()
    {
        wp_cache_delete( $this->get_widget_slug(), 'widget' );
    }
    /**
     * Processes the widget's options to be saved.
     *
     * @param array new_instance The new instance of values to be generated via the update.
     * @param array old_instance The previous instance of values before the update.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $content_type = strtolower($new_instance['content_type']);
        $content_id = sanitize_text_field(strtolower($new_instance['content_id']));

        if ($content_type != 'user' && $content_type != 'tag') {
            $content_type = 'user';
        }

        if ($content_type == 'user') {
            $content_id = preg_replace('/[^a-zA-Z0-9_\.]/', '', $content_id);
        } else {
            $content_id = preg_replace('/(*UTF8)[^\w\p{L}]/', '', $content_id);
        }

        $instance['content_type'] = $content_type;
        $instance['content_id'] = $content_id;

        if (empty($content_id)) {

            add_settings_error($this->get_field_name( 'content_id' ), 'required',
                'This field is required', 'error');

            return false;
        } else {
            return $instance;
        }
    } // end widget

    /**
     * Generates the administration form for the widget.
     *
     * @param array instance The array of keys and values for the widget.
     */
    public function form( $instance ) {
        $instance = wp_parse_args(
            (array) $instance
        );

        $content_type = $instance['content_type'];
        $content_id = $instance['content_id'];

        if (!$content_type || $content_type === '') {
            $content_type = 'user';
        }

        $title = null;

        if ($content_type == 'tag') {
            $title = '#' . $content_id;
        } else if ($content_type == 'user') {
            $title = '@' . $content_id;
        }

        // Display the admin form
        include( plugin_dir_path(__FILE__) . 'views/admin.php' );

    } // end form

    /*--------------------------------------------------*/
    /* Public Functions
    /*--------------------------------------------------*/

    /**
     * Loads the Widget's text domain for localization and translation.
     */
    public function widget_textdomain() {
        load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );

    } // end widget_textdomain

    /**
     * Fired when the plugin is activated.
     *
     * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
     */
    public static function activate( $network_wide ) {
        // TODO define activation functionality here
    } // end activate

    /**
     * Fired when the plugin is deactivated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
     */
    public static function deactivate( $network_wide ) {
        // TODO define deactivation functionality here
    } // end deactivate

    /**
     * Registers and enqueues admin-specific styles.
     */
    public function register_admin_styles() {

        wp_enqueue_style( $this->get_widget_slug().'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ) );

    } // end register_admin_styles

    /**
     * Registers and enqueues admin-specific JavaScript.
     */
    public function register_admin_scripts() {

        //wp_enqueue_script( $this->get_widget_slug().'-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery') );

    } // end register_admin_scripts

    /**
     * Registers and enqueues widget-specific styles.
     */
    public function register_widget_styles() {

        wp_enqueue_style( $this->get_widget_slug().'-widget-styles', plugins_url( 'css/widget.css', __FILE__ ) );

    } // end register_widget_styles

    /**
     * Registers and enqueues widget-specific scripts.
     */
    public function register_widget_scripts() {

        wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/widget.js', __FILE__ ), array('jquery') );

    } // end register_widget_scripts

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("ImgTurk");' ) );

// Hooks fired when the Widget is activated and deactivated
register_activation_hook( __FILE__, array( 'ImgTurk', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ImgTurk', 'deactivate' ) );
