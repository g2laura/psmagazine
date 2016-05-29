<?php
/*
Plugin Name: Author Recommended Posts
Plugin URI: http://
Description: A simple WordPress plugin that allows the author to pick recommended reading of posts, on a per post basis
Version: 1.0.3
Author: digital-telepathy
Author URI: http://www.dtelepathy.com
License: GPL3

Copyright 2012 digital-telepathy  (email : support@digital-telepathy.com)

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

// Include constants file
require_once( dirname( __FILE__ ) . '/lib/constants.php' );

class AuthorRecommendedPosts {
    static $html_newline = "\n";
    var $namespace = "author_recommended_posts";
    var $version = "1.0.3";
    
    // Default plugin options
    var $defaults = array(
        'author_recommended_posts_title' => "Author Recommended Posts",
        'author_recommended_posts_show_title' => true,
        'author_recommended_posts_show_featured_image' => false,
        'author_recommended_posts_format_is_horizontal' => true,
        'author_recommended_posts_post_types' => array( 'post' ),
        'author_recommended_posts_output_after_content' => true,
        'author_recommended_posts_auto_output' => array( 'post' )
    );
    
    /**
     * Instantiation construction
     * 
     * @uses add_action()
     * @uses AuthorRecommendedPosts::wp_register_scripts()
     * @uses AuthorRecommendedPosts::wp_register_styles()
     */
    function __construct() {
        // Name of the option_value to store plugin options in
        $this->option_name = '_' . $this->namespace . '--options';
        
        // Set and Translate the friendly name
        $this->friendly_name = __( "Author Recommended Posts", $this->namespace );
		
        // Load all library files used by this plugin
        $libs = glob( AUTHOR_RECOMMENDED_POSTS_DIRNAME . '/lib/*.php' );
        foreach( $libs as $lib ) {
            include_once( $lib );
        }
        
        /**
         * Make this plugin available for translation.
         * Translations can be added to the /languages/ directory.
         */
        load_theme_textdomain( $this->namespace, AUTHOR_RECOMMENDED_POSTS_DIRNAME . '/languages' );

		// Add all action, filter and shortcode hooks
		$this->_add_hooks();
    }
    
    /**
     * Add in various hooks
     * 
     * Place all add_action, add_filter, add_shortcode hook-ins here
     */
    private function _add_hooks() {
        // Options page for configuration
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        
        // Route requests for form processing
        add_action( 'init', array( &$this, 'route' ) );
        
        // Add the meta boxes
        add_action( 'add_meta_boxes', array( &$this, 'add_recommended_meta_box' ) );
        
        // Save post meta to the post
        add_action( 'save_post', array( &$this, 'saving_recommended_posts_ids' ), 10, 2 );
        
        // Filter the content of the post and output at the end of the content
        add_filter( 'the_content', array( &$this, 'recommended_posts_output' ) );
        
        // Add a settings link next to the "Deactivate" link on the plugin listing page
        add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
        
        // Register all JavaScripts for this plugin
        add_action( 'init', array( &$this, 'wp_register_scripts' ), 1 );
        
        // Register all Stylesheets for this plugin
        add_action( 'init', array( &$this, 'wp_register_styles' ), 1 );
        
        // Enqueue all Public Stylesheets for this plugin
        add_action( 'wp_head', array( &$this, 'enqueue_custom_styles' ), 1 );
        
        // Ajax handler for searching/filter posts
        add_action( 'wp_ajax_author_recommended_posts_search', array( &$this, 'author_recommended_posts_search') );
        
        add_shortcode( 'AuthorRecommendedPosts', array( &$this, 'shortcode') );
    }
    
    /**
     * Process update page form submissions
     * 
     * @uses AuthorRecommendedPosts::sanitize()
     * @uses wp_redirect()
     * @uses wp_verify_nonce()
     */
    private function _admin_options_update() {
        // Verify submission for processing using wp_nonce
        if( wp_verify_nonce( $_REQUEST['_wpnonce'], "{$this->namespace}-update-options" ) ) {
            $data = array();
            /**
             * Loop through each POSTed value and sanitize it to protect against malicious code. Please
             * note that rich text (or full HTML fields) should not be processed by this function and 
             * dealt with directly.
             */
            foreach( $_POST['data'] as $key => $val ) {
                $data[$key] = $this->_sanitize( $val );
            }
            
            /**
             * Place your options processing and storage code here
             */
            
            // Checking to see if Output Options are empty, if they are we are setting an empty array so it does not pull in defaults
            // Defaults come in when the option is not set, so basically this sets the option to nothing 
            if( empty( $data["author_recommended_posts_auto_output"] ) ){
                $data["author_recommended_posts_auto_output"] = array();
            }
            
            // Update the options value with the data submitted
            update_option( $this->option_name, $data );
            
            // Redirect back to the options page with the message flag to show the saved message
            wp_safe_redirect( $_REQUEST['_wp_http_referer'] . '&message=1' );
            exit;
        }
    }
    
    /**
     * Sanitize data
     * 
     * @param mixed $str The data to be sanitized
     * 
     * @uses wp_kses()
     * 
     * @return mixed The sanitized version of the data
     */
    private function _sanitize( $str ) {
        if ( !function_exists( 'wp_kses' ) ) {
            require_once( ABSPATH . 'wp-includes/kses.php' );
        }
        global $allowedposttags;
        global $allowedprotocols;
        
        if ( is_string( $str ) ) {
            $str = wp_kses( $str, $allowedposttags, $allowedprotocols );
        } elseif( is_array( $str ) ) {
            $arr = array();
            foreach( (array) $str as $key => $val ) {
                $arr[$key] = $this->_sanitize( $val );
            }
            $str = $arr;
        }
        
        return $str;
    }

    /**
     * Hook into register_activation_hook action
     * 
     * Put code here that needs to happen when your plugin is first activated (database
     * creation, permalink additions, etc.)
     */
    static function activate() {
        // Do activation actions
    }
    
    /**
     * Add Recommended Meta Box
     * 
     * runs the add_meta_box() method to create the Meta Box in the post
     */
    function add_recommended_meta_box() {
        // set post_types that this meta box shows up on.
        $author_recommended_posts_post_types = $this->get_option( "{$this->namespace}_post_types" );
        
        foreach( $author_recommended_posts_post_types as $author_recommended_posts_post_type ) {
            // adds to posts $post_type
            add_meta_box( 
                $this->namespace . '-recommended_meta_box',
                __( 'Author Recommended Posts', $this->namespace ),
                array( &$this, 'recommended_meta_box' ),
                $author_recommended_posts_post_type,
                'side',
                'high'
            );
        }
            
    }
    
    /**
     * Recommended Meta Box
     * 
     * Adds the meta box to post types that allows the author to set
     * which posts they want to pull into the Recommended Author Reading section
     */
    function recommended_meta_box( $object, $box ) {
        
        $author_recommended_posts = get_post_meta( $object->ID, $this->namespace, true );
        $author_recommended_posts_post_types = $this->get_option( "{$this->namespace}_post_types" );
        $author_recommended_posts_search_results = $this->author_recommended_posts_search();
        $author_recommended_posts_options_url = admin_url() . '/options-general.php?page=' . $this->namespace;
        
        include( AUTHOR_RECOMMENDED_POSTS_DIRNAME . '/views/_recommended-meta-box.php' );
    }
    
    function author_recommended_posts_search(){
        global $post;
        $post_id = $post->ID;
        $html = '';
        
        // set post_types that get filtered in the search box.
        $author_recommended_posts_post_types = $this->get_option( "{$this->namespace}_post_types" );
        
        // set default query options
        $options = array(
            'post_type' =>  $author_recommended_posts_post_types,
            'posts_per_page' => 10,
            'paged' => 0,
            'order' => 'DESC',
            'post_status' => array('publish'),
            'suppress_filters' => false,
            'post__not_in' => array($post_id),
            's' => ''
        );
        
        // check if ajax
        $ajax = isset( $_POST['action'] ) ? true : false;
        
        // if ajax merge $_POST
        if( $ajax ) {
            $options = array_merge($options, $_POST);
        }
        
        // search
        if( $options['s'] ) {
            // set temp title to search query
            $options['like_title'] = $options['s'];
            // filter query by title
            add_filter( 'posts_where', array($this, 'posts_where'), 10, 2 );
        }
        
        // unset search so results are accurate and not muddled 
        unset( $options['s'] );
        
        $searchable_posts = get_posts( $options );
        
        if( $searchable_posts ) {
            foreach( $searchable_posts as $searchable_post ) {
                // right aligned info
                $title = '<span class="recommended-posts-post-type">';
                $title .= $searchable_post->post_type;
                $title .= '</span>';
                $title .= '<span class="recommended-posts-title">';
                $title .= apply_filters( 'the_title', $searchable_post->post_title, $searchable_post->ID );
                $title .= '</span>';
                
                $html .= '<li><a href="' . get_permalink($searchable_post->ID) . '" data-post_id="' . $searchable_post->ID . '">' . $title .  '</a></li>' . "\n";
            }
        }
        
        // if ajax, die and echo $html otherwise just return
        if( $ajax ) {
            die( $html );
        } else {
            return $html;
        }
    }
    
    function posts_where( $where, &$wp_query ) {
        global $wpdb;
        
        if ( $title = $wp_query->get('like_title') ) {
            $where .= " AND " . $wpdb->posts . ".post_title LIKE '%" . esc_sql( like_escape( $title ) ) . "%'";
        }
        
        return $where;
    }
    
    function saving_recommended_posts_ids( $post_id, $post ) {
        
        if( isset( $_REQUEST['_post_ids_nonce'] ) && !empty( $_REQUEST['_post_ids_nonce'] ) ){
            
            // Verfiy the nonce before proceeding
            if( !wp_verify_nonce( $_REQUEST['_post_ids_nonce'], "{$this->namespace}_post_ids_nonce" ) ) {
                return $post_id;
            }
            
            // Get the post type object.
            $post_type = get_post_type_object( $post->post_type );
            
            // Check if the current user has permissions to edit the post.
            if( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
                return $post_id;
            }
            
            // Get the posted data and sanitize
            $new_meta_value = ( isset( $_POST['author-recommended-posts'] ) ? $this->_sanitize( $_POST['author-recommended-posts'] ) : '' );
            
            // Get the meta key
            $meta_key = $this->namespace;
            
            // Get the meta value of the custom field key
            $meta_value = get_post_meta( $post_id, $meta_key, true );
            
            // If the new meta value was added and there was no previous value, add it.
            if ( $new_meta_value && ( '' == $meta_value ) ) {
                add_post_meta( $post_id, $meta_key, $new_meta_value, true );
            
            // If the new meta value does not match the old value, update it.
            } elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
                update_post_meta( $post_id, $meta_key, $new_meta_value );
            
            // If there is no new meta value but an old value exists, delete it.
            } elseif ( ( '' == $new_meta_value ) && $meta_value ) {
                delete_post_meta( $post_id, $meta_key, $meta_value );
            }
            
        }
    }
    
    /**
     * Runs the shortcode in the content filter if single view
     * 
     * @param string $content The content of the post
     * 
     * @uses do_shortcode()
     * 
     * @return string The content with Author Recommended Posts appended if single and if exists
     */
    function recommended_posts_output( $content ){
        global $post;    
        $author_recommended_posts_auto_output = $this->get_option( "{$this->namespace}_auto_output" );

        if( is_singular() ) {
            if ( in_array( $post->post_type, $author_recommended_posts_auto_output ) ) {
                $html = do_shortcode('[AuthorRecommendedPosts post_id='. $post->ID .']');
                $content = $content . $html;
            }
        }
        
        return $content;
    }
    
    /**
     * Process the SlideDeck shortcode
     *
     * @param object $atts Attributes of the shortcode
     *
     * @uses shortcode_atts()
     * @uses slidedeck_process_template()
     *
     * @return object The processed shortcode
     */
    function shortcode( $atts ) {
        global $post;
        $namespace = $this->namespace;
        
        if ( isset( $atts['post_id'] ) && !empty( $atts['post_id'] ) ){
            $shortcode_post_id = $atts['post_id']; 
        }else{
            $shortcode_post_id = $post->ID;
        }
        
        $recommended_ids = get_post_meta( $shortcode_post_id, $namespace, true );
        
        $html = '';

        if( $recommended_ids ){
            
            $html_title = $this->get_option( "{$namespace}_title" );
            $show_title = $this->get_option( "{$namespace}_show_title" );
            $show_featured_image = $this->get_option( "{$namespace}_show_featured_image" );
            $format_horizontal = $this->get_option( "{$namespace}_format_is_horizontal" );
            $author_recommended_posts_post_types = $this->get_option( "{$namespace}_post_types" );
            
            ob_start( );
            include( AUTHOR_RECOMMENDED_POSTS_DIRNAME . '/views/_author-recommended-posts-list.php' );
            $html .= ob_get_contents( );
            ob_end_clean( );
        
        }
        
        return $html;
    }
	
    /**
     * Define the admin menu options for this plugin
     * 
     * @uses add_action()
     * @uses add_options_page()
     */
    function admin_menu() {
        $page_hook = add_options_page( $this->friendly_name, $this->friendly_name, 'administrator', $this->namespace, array( &$this, 'admin_options_page' ) );
                
        // Add print scripts and styles action based off the option page hook
        add_action( 'admin_print_scripts-' . $page_hook, array( &$this, 'admin_print_scripts' ) );
        add_action( 'admin_print_styles-' . $page_hook, array( &$this, 'admin_print_styles' ) );
        
        add_action( 'admin_print_scripts-post.php', array( &$this, 'admin_print_scripts' ) );
        add_action( 'admin_print_styles-post.php', array( &$this, 'admin_print_styles' ) );
        
        add_action( 'admin_print_scripts-post-new.php', array( &$this, 'admin_print_scripts' ) );
        add_action( 'admin_print_styles-post-new.php', array( &$this, 'admin_print_styles' ) );
    }
    
    
    /**
     * The admin section options page rendering method
     * 
     * @uses current_user_can()
     * @uses wp_die()
     */
    function admin_options_page() {
        if( !current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page' );
        }
        
        $page_title = $this->friendly_name . ' Options';
        $namespace = $this->namespace;
        
        // Look Up Data and Build Array of Posts
        $registered_post_types = get_post_types( array( 'exclude_from_search' => false ), 'objects' );
        
        // Rebuild the array into something more usable
        $author_recommended_posts_post_types = array();
        
        foreach( $registered_post_types as $key => $val ){
            $author_recommended_posts_post_types[] = array(
                'slug' => $key,
                'name' => $val->labels->name
            );
        }
        
        include( AUTHOR_RECOMMENDED_POSTS_DIRNAME . "/views/options.php" );
    }
    
    /**
     * Load JavaScript for the admin options page
     * 
     * @uses wp_enqueue_script()
     */
    function admin_print_scripts() {
        global $post;
        
        // =====================================================================
        // check to see if the post type is included in the options set by user
        // ===================================================================== 
        
        wp_enqueue_script( "{$this->namespace}-admin" );
    }
    
    /**
     * Load Stylesheet for the admin options page
     * 
     * @uses wp_enqueue_style()
     */
    function admin_print_styles() {
        wp_enqueue_style( "{$this->namespace}-admin" );
    }
    
    /**
     * Hook into register_deactivation_hook action
     * 
     * Put code here that needs to happen when your plugin is deactivated
     */
    static function deactivate() {
        // Do deactivation actions
    }
    
    /**
     * Retrieve the stored plugin option or the default if no user specified value is defined
     * 
     * @param string $option_name The name of the TrialAccount option you wish to retrieve
     * 
     * @uses get_option()
     * 
     * @return mixed Returns the option value or false(boolean) if the option is not found
     */
    function get_option( $option_name ) {
        // Load option values if they haven't been loaded already
        if( !isset( $this->options ) || empty( $this->options ) ) {
            $this->options = get_option( $this->option_name, $this->defaults );
        }
        
        if( isset( $this->options[$option_name] ) ) {
            return $this->options[$option_name];    // Return user's specified option value
        } elseif( isset( $this->defaults[$option_name] ) ) {
            return $this->defaults[$option_name];   // Return default option value
        }
        return false;
    }
    
    /**
     * Initialization function to hook into the WordPress init action
     * 
     * Instantiates the class on a global variable and sets the class, actions
     * etc. up for use.
     */
    static function instance() {
        global $AuthorRecommendedPosts;
        
        // Only instantiate the Class if it hasn't been already
        if( !isset( $AuthorRecommendedPosts ) ) $AuthorRecommendedPosts = new AuthorRecommendedPosts();
    }
	
	/**
	 * Hook into plugin_action_links filter
	 * 
	 * Adds a "Settings" link next to the "Deactivate" link in the plugin listing page
	 * when the plugin is active.
	 * 
	 * @param object $links An array of the links to show, this will be the modified variable
	 * @param string $file The name of the file being processed in the filter
	 */
	function plugin_action_links( $links, $file ) {
		if( $file == plugin_basename( AUTHOR_RECOMMENDED_POSTS_DIRNAME . '/' . basename( __FILE__ ) ) ) {
            $old_links = $links;
            $new_links = array(
                "settings" => '<a href="options-general.php?page=' . $this->namespace . '">' . __( 'Settings' ) . '</a>'
            );
            $links = array_merge( $new_links, $old_links );
		}
		
		return $links;
	}
    
    /**
     * Route the user based off of environment conditions
     * 
     * This function will handling routing of form submissions to the appropriate
     * form processor.
     * 
     * @uses AuthorRecommendedPosts::_admin_options_update()
     */
    function route() {
        $uri = $_SERVER['REQUEST_URI'];
        $protocol = isset( $_SERVER['HTTPS'] ) ? 'https' : 'http';
        $hostname = $_SERVER['HTTP_HOST'];
        $url = "{$protocol}://{$hostname}{$uri}";
        $is_post = (bool) ( strtoupper( $_SERVER['REQUEST_METHOD'] ) == "POST" );
        
        // Check if a nonce was passed in the request
        if( isset( $_REQUEST['_wpnonce'] ) ) {
            $nonce = $_REQUEST['_wpnonce'];
            
            // Handle POST requests
            if( $is_post ) {
                if( wp_verify_nonce( $nonce, "{$this->namespace}-update-options" ) ) {
                    $this->_admin_options_update();
                }
            } 
            // Handle GET requests
            else {
                
            }
        }
    }
    
    /**
     * Register scripts used by this plugin for enqueuing elsewhere
     * 
     * @uses wp_register_script()
     */
    function wp_register_scripts() {
        // Admin JavaScript
        wp_register_script( "{$this->namespace}-admin", AUTHOR_RECOMMENDED_POSTS_URLPATH . "/js/admin.js", array( 'jquery' ), $this->version, true );
    }
    
    /**
     * Register styles used by this plugin for enqueuing elsewhere
     * 
     * @uses wp_register_style()
     */
    function wp_register_styles() {
        // Admin Stylesheet
        wp_register_style( "{$this->namespace}-admin", AUTHOR_RECOMMENDED_POSTS_URLPATH . "/css/admin.css", array(), $this->version, 'screen' );
        
        // Public Stylesheet
        wp_register_style( "{$this->namespace}-public", AUTHOR_RECOMMENDED_POSTS_URLPATH . "/css/public.css", array(), $this->version, 'screen' );
    }
    
    /**
     * Enqueue public styles used by this plugin
     * 
     * @uses wp_enqueue_style()
     */
    function enqueue_custom_styles(){
        wp_enqueue_style( "{$this->namespace}-public" );
    }
}
if( !isset( $AuthorRecommendedPosts ) ) {
	AuthorRecommendedPosts::instance();
}

register_activation_hook( __FILE__, array( 'AuthorRecommendedPosts', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'AuthorRecommendedPosts', 'deactivate' ) );
