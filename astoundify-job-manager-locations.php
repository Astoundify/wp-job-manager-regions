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
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
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
		$this->file         = __FILE__;
		
		$this->basename     = apply_filters( 'ajmr_plugin_basenname', plugin_basename( $this->file ) );
		$this->plugin_dir   = apply_filters( 'ajmr_plugin_dir_path',  plugin_dir_path( $this->file ) );
		$this->plugin_url   = apply_filters( 'ajmr_plugin_dir_url',   plugin_dir_url ( $this->file ) );

		$this->lang_dir     = apply_filters( 'ajmr_lang_dir',     trailingslashit( $this->plugin_dir . 'languages' ) );

		$this->domain       = 'ajmr'; 
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
		add_action( 'job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );

		add_filter( 'the_job_location', array( $this, 'the_job_location' ), 10, 2 );

		$this->load_textdomain();
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
	            'has_archive'           => true,
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

	function update_job_data( $job_id, $values ) {
		$region = $values[ 'job' ][ 'job_region' ];
		$term   = get_term_by( 'slug', $region, 'job_listing_region' );

		wp_set_post_terms( $job_id, array( $term->term_id ), 'job_listing_region', false );
	}

	function the_job_location( $job_location, $post ) {
		if ( ! is_singular( 'job_listing' ) )
			return $job_location;

		$terms = wp_get_post_terms( $post->ID, 'job_listing_region' );

		if ( is_wp_error( $terms ) )
			return $job_location;

		$location = $terms[0];
		$locname  = $location->name;

		$job_location = sprintf( '%s &mdash; <a href="%s">%s</a>', $job_location, get_term_link( $location, 'job_listing_region' ), $locname );

		return apply_filters( 'ajmr_job_location', $job_location, $location );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @since 
	 */
	public function load_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/' . $this->domain . '/' . $mofile;

		// Look in global /wp-content/languages/ajmr folder
		if ( file_exists( $mofile_global ) ) {
			return load_textdomain( $this->domain, $mofile_global );

		// Look in local /wp-content/plugins/ajmr/languages/ folder
		} elseif ( file_exists( $mofile_local ) ) {
			return load_textdomain( $this->domain, $mofile_local );
		}

		return false;
	}
}

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