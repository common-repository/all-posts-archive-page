<?php
   /*
   Plugin Name: Binge Reading Archive Page Template
   Plugin URI: http://narrowbridgemedia.com/
   Description: A plugin to create an "all posts since this site started by month" page. Optimized for sites using the Genesis Framework.
   Version: 0.2
   Author: Eric Rosenberg - Narrow Bridge Media
   Author URI: http://narrowbridgemedia.com
   License:     GPL2
   License URI: https://www.gnu.org/licenses/gpl-2.0.html
   Customized with code from http://www.joemaraparecio.com/customizing-genesis-archive-template-display-posts-month/
   */

/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}

/**
     * Activation Hook - Confirm site is using Genesis
     *
     * @since 1.0.0
     */
    function activation_hook() {

        if ( 'genesis' != basename( TEMPLATEPATH ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( sprintf( __( 'Sorry, this plugin can only activate if you have installed <a href="%s">Genesis</a>', 'all-posts-archive-page' ), 'http://personalprofitability.com/genesis' ) );
        }
    }

class nbm_PageTemplater {

    /**
         * A Unique Identifier
         */
     protected $plugin_slug;

        /**
         * A reference to an instance of this class.
         */
        private static $instance;

        /**
         * The array of templates that this plugin tracks.
         */
        protected $templates;

        /**
         * Returns an instance of this class. 
         */
        public static function get_instance() {

                if( null == self::$instance ) {
                        self::$instance = new nbm_PageTemplater();
                } 
                return self::$instance;
        } 

        /**
         * Initializes the plugin by setting filters and administration functions.
         */
        private function __construct() {

                $this->templates = array();

                // Add a filter to the attributes metabox to inject template into the cache.
                add_filter(
          'page_attributes_dropdown_pages_args',
           array( $this, 'register_project_templates' ) 
        );

                // Add a filter to the save post to inject out template into the page cache
                add_filter(
          'wp_insert_post_data', 
          array( $this, 'register_project_templates' ) 
        );

                // Add a filter to the template include to determine if the page has our 
        // template assigned and return it's path
                add_filter(
          'template_include', 
          array( $this, 'view_project_template') 
        );
                // Add your templates to this array.
                $this->templates = array(
                        'binge-reading.php'     => 'Binge Reading Archive by Month',
                );
        } 


        /**
         * Adds our template to the pages cache in order to tell WordPress the template file exists.
         */

        public function register_project_templates( $atts ) {

                // Create the key used for the themes cache
                $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

                // Retrieve the cache list. 
                // If it doesn't exist, or it's empty prepare an array
                $templates = wp_get_theme()->get_page_templates();
                if ( empty( $templates ) ) {
                        $templates = array();
                } 

                // New cache, therefore remove the old one
                wp_cache_delete( $cache_key , 'themes');

                // Now add our template to the list of templates by merging our templates
                // with the existing templates array from the cache.
                $templates = array_merge( $templates, $this->templates );

                // Add the modified cache to allow WordPress to pick it up for listing
                // available templates
                wp_cache_add( $cache_key, $templates, 'themes', 1800 );

                return $atts;
        } 

        /**
         * Checks if the template is assigned to the page
         */
        public function view_project_template( $template ) {

                global $post;

                if (!isset($this->templates[get_post_meta( 
          $post->ID, '_wp_page_template', true 
        )] ) ) {
          
                        return $template;
                } 

                $file = plugin_dir_path(__FILE__). get_post_meta( 
          $post->ID, '_wp_page_template', true 
        );
        
                // Just to be safe, we check if the file exist first
                if( file_exists( $file ) ) {
                        return $file;
                } 
        else { echo $file; }

                return $template;

        } 
} 

add_action( 'plugins_loaded', array( 'nbm_PageTemplater', 'get_instance' ) );


/** Create WordPress Administration Menus and Pages */

/** Step 2 (from text above). */
add_action( 'admin_menu', 'nbm_plugin_menu' );

/** Step 1. */
function nbm_plugin_menu() {
    add_options_page( 'Binge Reading Archive Page Options', 'Binge Reading Archive Page', 'manage_options', 'all-posts-archive-page', 'all_posts_archive_page_options' );
}

/** Step 3. */
function all_posts_archive_page_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page. BURN!' ) );
    }
    echo '<div class="wrap">';
    echo '<h1>Binge Reading Archive Page Template</h1>';
    echo '<p>Thank you for installing the All Posts Archive Page plugin by Narrow Bridge Media. In the future, this page will contain options to customize your all posts archive page or add multiple versions. For now, it is used for the happy dance.</p>';
    echo '<ul>';
    echo '<li>Official Plugin Homepage: <a href="https://narrowbridgemedia.com/plugins/binge-reading-page-template/">All Posts Archive Page by Narrow Bridge Media</a></li>';
    echo '<li>Submit a Bug or Feature Request: <a href="https://wordpress.org/support/plugin/all-posts-archive-page">WordPress.org Forums</a></li>';
    echo '<li>Follow the Developer on Twitter: <a href="https://twitter.com/EricProfits">@EricProfits</a></li>';
    echo '</ul>';
    echo '<iframe src="//giphy.com/embed/26ufoAcj4cdJoeKzu?html5=true" width="480" height="480" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>';
    echo '</div>';
}