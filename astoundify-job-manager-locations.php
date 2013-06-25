<?php
/**
 * Plugin Name: WP Job Manager Predefined Regions
 * Plugin URI:  https://github.com/astoundify
 * Description: Create predefined regions that job submissions can associate themselves with.
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     0.1
 * Text Domain: ajmr
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Crowd Funding Class
 *
 * @since 
 */
final class Astoundify_Job_Manager_Regions {

	/**
	 * @var 
	 */
	private static $instance;

	/**
	 * 
	 *
	 * @since 
	 *
	 * @return 
	 */
	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new Astoundify_Job_Manager_Regions;
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
		$this->basename     = apply_filters( 'ajmr_plugin_basenname', plugin_basename( $this->file ) );
		$this->plugin_dir   = apply_filters( 'ajmr_plugin_dir_path',  plugin_dir_path( $this->file ) );
		$this->plugin_url   = apply_filters( 'ajmr_plugin_dir_url',   plugin_dir_url ( $this->file ) );

		// Includes
		$this->includes_dir = apply_filters( 'ajmr_includes_dir', trailingslashit( $this->plugin_dir . 'includes'  ) );
		$this->includes_url = apply_filters( 'ajmr_includes_url', trailingslashit( $this->plugin_url . 'includes'  ) );

		$this->template_dir = apply_filters( 'ajmr_templates_dir', trailingslashit( $this->plugin_dir . 'templates'  ) );

		// Languages
		$this->lang_dir     = apply_filters( 'ajmr_lang_dir',     trailingslashit( $this->plugin_dir . 'languages' ) );

		/** Misc **************************************************************/

		$this->domain       = 'ajmr'; 
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
		add_filter( 'submit_job_form_fields', array( $this, 'form_fields' ) );
		add_action( 'wp_job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );

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
		
		$singular  = __( 'Job Region', 'ajmr' );
		$plural    = __( 'Job Regions', 'ajmr' );

		if ( current_theme_supports( 'job-manager-templates' ) ) {
			$rewrite     = array(
				'slug'         => _x( 'job-region', 'Job region slug - resave permalinks after changing this', 'ajmr' ),
				'with_front'   => false,
				'hierarchical' => false
			);
		} else {
			$rewrite = false;
		}

		register_taxonomy( 'job_listing_region',
	        array( 'job_listing' ),
	        array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> $plural,
	            'labels' => array(
                    'name' 				=> $plural,
                    'singular_name' 	=> $singular,
                    'search_items' 		=> sprintf( __( 'Search %s', 'ajmr' ), $plural ),
                    'all_items' 		=> sprintf( __( 'All %s', 'ajmr' ), $plural ),
                    'parent_item' 		=> sprintf( __( 'Parent %s', 'ajmr' ), $singular ),
                    'parent_item_colon' => sprintf( __( 'Parent %s:', 'ajmr' ), $singular ),
                    'edit_item' 		=> sprintf( __( 'Edit %s', 'ajmr' ), $singular ),
                    'update_item' 		=> sprintf( __( 'Update %s', 'ajmr' ), $singular ),
                    'add_new_item' 		=> sprintf( __( 'Add New %s', 'ajmr' ), $singular ),
                    'new_item_name' 	=> sprintf( __( 'New %s Name', 'ajmr' ),  $singular )
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

	function form_fields( $fields ) {
		$fields[ 'job' ][ 'job_region' ] = array(
			'label'       => __( 'Job Region', 'job_manager' ),
			'type'        => 'select',
			'options'     => ajmr_get_regions_simple(),
			'required'    => true,
			'priority'    => 3
		);

		return $fields;
	}

	function update_job_data( $values, $job_id ) {
		wp_set_object_terms( $job_id, array( $values[ 'job' ][ 'job_region' ] ), 'job_listing_region', false );
	}

	function data_fields( $fields ) {
		return $fields;
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
 * 
 */
function ajmr() {
	return Astoundify_Job_Manager_Regions::instance();
}

ajmr();

function ajmr_get_regions() {
	$locations = get_terms( 'job_listing_region', apply_filters( 'ajmr_get_region_args', array( 'hide_empty' => 0 ) ) );

	return $locations;
}

function ajmr_get_regions_simple() {
	$locations = ajmr_get_regions();
	$simple    = array();

	foreach ( $locations as $location ) {
		$simple[ $location->slug ] = $location->name;
	}

	return apply_filters( 'ajmr_get_regions_simple', $simple );
}