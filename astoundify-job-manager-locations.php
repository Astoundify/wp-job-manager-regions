<?php
/**
 * Plugin Name: WP Job Manager Predefined Locations
 * Plugin URI:  https://github.com/astoundify
 * Description: Set predefined locations for WP Job Manager
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     0.1
 * Text Domain: ajml
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Crowd Funding Class
 *
 * @since Appthemer CrowdFunding 0.1-alpha
 */
final class Astoundify_Job_Manager_Locations {

	/**
	 * @var crowdfunding The one true AT_CrowdFunding
	 */
	private static $instance;

	/**
	 * Main Crowd Funding Instance
	 *
	 * Ensures that only one instance of Crowd Funding exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since Appthemer CrowdFunding 0.1-alpha
	 *
	 * @return The one true Crowd Funding
	 */
	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new Astoundify_Job_Manager_Locations;
			self::$instance->setup_globals();
			self::$instance->includes();
			self::$instance->setup_actions();
		}

		return self::$instance;
	}

	/** Private Methods *******************************************************/

	/**
	 * Set some smart defaults to class variables. Allow some of them to be
	 * filtered to allow for early overriding.
	 *
	 * @since Appthemer CrowdFunding 0.1-alpha
	 *
	 * @return void
	 */
	private function setup_globals() {
		/** Versions **********************************************************/

		$this->version    = '0.1';
		$this->db_version = '1';

		/** Paths *************************************************************/

		$this->file         = __FILE__;
		$this->basename     = apply_filters( 'ajml_plugin_basenname', plugin_basename( $this->file ) );
		$this->plugin_dir   = apply_filters( 'ajml_plugin_dir_path',  plugin_dir_path( $this->file ) );
		$this->plugin_url   = apply_filters( 'ajml_plugin_dir_url',   plugin_dir_url ( $this->file ) );

		// Includes
		$this->includes_dir = apply_filters( 'ajml_includes_dir', trailingslashit( $this->plugin_dir . 'includes'  ) );
		$this->includes_url = apply_filters( 'ajml_includes_url', trailingslashit( $this->plugin_url . 'includes'  ) );

		$this->template_dir = apply_filters( 'ajml_templates_dir', trailingslashit( $this->plugin_dir . 'templates'  ) );

		// Languages
		$this->lang_dir     = apply_filters( 'ajml_lang_dir',     trailingslashit( $this->plugin_dir . 'languages' ) );

		/** Misc **************************************************************/

		$this->domain       = 'ajml'; 
	}

	/**
	 * Include required files.
	 *
	 * @since Appthemer CrowdFunding 0.1-alpha
	 *
	 * @return void
	 */
	private function includes() {
		
	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @since Appthemer CrowdFunding 0.1-alpha
	 *
	 * @return void
	 */
	private function setup_actions() {
		add_action( 'init', array( $this, 'register_post_taxonomy' ) );
		add_filter( 'wp_job_manager_locate_template', array( $this, 'locate_template' ), 10, 3 );
		add_filter( 'submit_job_form_fields', array( $this, 'form_fields' ) );
		add_action( 'wp_job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );

		add_filter( 'the_job_location', array( $this, 'the_job_location' ), 10, 2 );

		$this->load_textdomain();

		if ( ! is_admin() )
			return;

		add_filter( 'wp_job_manager_job_listing_data_fields', array( $this, 'data_fields' ) );
	}

	/**
	 * register_post_types function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_taxonomy() {
		if ( ! post_type_exists( 'job_listing' ) )
			return;

		$admin_capability = 'manage_job_listings';
		
		$singular  = __( 'Job Location', 'ajml' );
		$plural    = __( 'Job Locations', 'ajml' );

		if ( current_theme_supports( 'job-manager-templates' ) ) {
			$rewrite     = array(
				'slug'         => _x( 'job-location', 'Job location slug - resave permalinks after changing this', 'ajml' ),
				'with_front'   => false,
				'hierarchical' => false
			);
		} else {
			$rewrite = false;
		}

		register_taxonomy( 'job_listing_location',
	        array( 'job_listing' ),
	        array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> $plural,
	            'labels' => array(
                    'name' 				=> $plural,
                    'singular_name' 	=> $singular,
                    'search_items' 		=> sprintf( __( 'Search %s', 'ajml' ), $plural ),
                    'all_items' 		=> sprintf( __( 'All %s', 'ajml' ), $plural ),
                    'parent_item' 		=> sprintf( __( 'Parent %s', 'ajml' ), $singular ),
                    'parent_item_colon' => sprintf( __( 'Parent %s:', 'ajml' ), $singular ),
                    'edit_item' 		=> sprintf( __( 'Edit %s', 'ajml' ), $singular ),
                    'update_item' 		=> sprintf( __( 'Update %s', 'ajml' ), $singular ),
                    'add_new_item' 		=> sprintf( __( 'Add New %s', 'ajml' ), $singular ),
                    'new_item_name' 	=> sprintf( __( 'New %s Name', 'ajml' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
	            'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),
	            'rewrite' 				=> $rewrite,
	        )
	    );
	}

	function locate_template( $template, $template_name, $template_path ) {
		if ( ! file_exists( $template ) )
			$template = trailingslashit( $this->template_dir ) . $template_name;

		return $template;
	}

	function form_fields( $fields ) {
		$fields[ 'job' ][ 'job_location' ][ 'type' ] = 'ajml-select';
		$fields[ 'job' ][ 'job_location' ][ 'options' ] = ajml_get_locations_simple();

		return $fields;
	}

	function update_job_data( $values, $job_id ) {
		wp_set_object_terms( $job_id, array( $values['job']['job_location'] ), 'job_listing_location', false );
	}

	function data_fields( $fields ) {
		die( 'wat' );
		unset( $fields[ '_job_location' ] );

		return $fields;
	}

	function the_job_location( $job_location, $post ) {
		$terms    = wp_get_post_terms( $post->ID, 'job_listing_location' );

		if ( empty( $terms ) )
			return $job_location;

		$location = $terms[0];

		return $location->name;
	}

	/**
	 * Loads the plugin language files
	 *
	 * @since Appthemer CrowdFunding 0.1-alpha
	 */
	public function load_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/' . $this->domain . '/' . $mofile;

		// Look in global /wp-content/languages/atcf folder
		if ( file_exists( $mofile_global ) ) {
			return load_textdomain( $this->domain, $mofile_global );

		// Look in local /wp-content/plugins/appthemer-crowdfunding/languages/ folder
		} elseif ( file_exists( $mofile_local ) ) {
			return load_textdomain( $this->domain, $mofile_local );
		}

		return false;
	}
}

/**
 * The main function responsible for returning the one true Crowd Funding Instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $crowdfunding = crowdfunding(); ?>
 *
 * @since Appthemer CrowdFunding 0.1-alpha
 *
 * @return The one true Crowd Funding Instance
 */
function ajml() {
	return Astoundify_Job_Manager_Locations::instance();
}

ajml();

function ajml_get_locations() {
	$locations = get_terms( 'job_listing_location', apply_filters( 'ajml_get_locations_args', array( 'hide_empty' => 0 ) ) );

	return $locations;
}

function ajml_get_locations_simple() {
	$locations = ajml_get_locations();
	$simple    = array();

	foreach ( $locations as $location ) {
		$simple[ $location->slug ] = $location->name;
	}

	return apply_filters( 'ajml_get_locations_simple', $simple );
}