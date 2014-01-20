<?php
/**
 * Plugin Name: WP Job Manager - Predefined Regions
 * Plugin URI:  https://github.com/astoundify/wp-job-manager-locations
 * Description: Create predefined regions that job submissions can associate themselves with.
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     1.3.1
 * Text Domain: ajmr
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Astoundify_Job_Manager_Regions {

	/**
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Make sure only one instance is only running.
	 */
	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Start things up.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Set some smart defaults to class variables. Allow some of them to be
	 * filtered to allow for early overriding.
	 *
	 * @since 1.0
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
	 * @since 1.0
	 *
	 * @return void
	 */
	private function setup_actions() {
		add_action( 'init', array( $this, 'register_post_taxonomy' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'form_fields' ) );
		add_action( 'job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );
		add_filter( 'submit_job_form_fields_get_job_data', array( $this, 'form_fields_get_job_data' ), 10, 2 );

		add_filter( 'the_job_location', array( $this, 'the_job_location' ), 10, 2 );

		$this->load_textdomain();
	}

	/**
	 * Create the `job_listing_region` taxonomy.
	 *
	 * @since 1.0
	 */
	public function register_post_taxonomy() {
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

	/**
	 * Add the field to the submission form.
	 *
	 * @since 1.0
	 */
	function form_fields( $fields ) {
		$fields[ 'job' ][ 'job_region' ] = array(
			'label'       => __( 'Job Region', 'job_manager' ),
			'type'        => 'select',
			'options'     => ajmr_get_regions_simple(),
			'required'    => true,
			'priority'    => '2.5'
		);

		return $fields;
	}

	/**
	 * Get the current value for the job region. We can't rely
	 * on basic meta value getting, instead we need to find the term.
	 *
	 * @since 1.0
	 */
	function form_fields_get_job_data( $fields, $job ) {
		$fields[ 'job' ][ 'job_region' ][ 'value' ] = current( wp_get_object_terms( $job->ID, 'job_listing_region', array( 'fields' => 'slugs' ) ) );

		return $fields;
	}

	/**
	 * When the form is submitted, update the data.
	 *
	 * @since 1.0
	 */
	function update_job_data( $job_id, $values ) {
		$region = isset ( $values[ 'job' ][ 'job_region' ] ) ? $values[ 'job' ][ 'job_region' ] : null;

		if ( ! $region )
			return;

		$term   = get_term_by( 'slug', $region, 'job_listing_region' );

		wp_set_post_terms( $job_id, array( $term->term_id ), 'job_listing_region', false );
	}

	/**
	 * On a singular job page, append the region to the location.
	 *
	 * @since 1.0
	 */
	function the_job_location( $job_location, $post ) {
		if ( ! is_singular( 'job_listing' ) )
			return $job_location;

		$terms = wp_get_post_terms( $post->ID, 'job_listing_region' );

		if ( is_wp_error( $terms ) || empty( $terms ) )
			return $job_location;

		$location = $terms[0];
		$locname  = $location->name;

		$job_location = sprintf( '%s &mdash; <a href="%s">%s</a>', $job_location, get_term_link( $location, 'job_listing_region' ), $locname );

		return apply_filters( 'ajmr_job_location', $job_location, $location );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @since 1.0
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

/**
 * Start things up.
 *
 * Use this function instead of a global.
 *
 * $ajmr = ajmr();
 *
 * @since 1.0
 */
function ajmr() {
	return Astoundify_Job_Manager_Regions::instance();
}

ajmr();

/**
 * Get regions (terms) helper.
 *
 * @since 1.0
 */
function ajmr_get_regions() {
	$locations = get_terms( 'job_listing_region', apply_filters( 'ajmr_get_region_args', array( 'hide_empty' => 0 ) ) );

	return $locations;
}

/**
 * Create a key => value pair of term ID and term name.
 *
 * @since 1.0
 */
function ajmr_get_regions_simple() {
	$locations = ajmr_get_regions();
	$simple    = array();

	foreach ( $locations as $location ) {
		$simple[ $location->slug ] = $location->name;
	}

	return apply_filters( 'ajmr_get_regions_simple', $simple );
}

/**
 * Custom widgets
 *
 * @since 1.1
 */
function ajmr_widgets_init() {
	if ( ! class_exists( 'Jobify_Widget' ) )
		return;

	$ajmr = ajmr();

	include_once( $ajmr->plugin_dir . '/widgets.php' );

	register_widget( 'Astoundify_Job_Manager_Regions_Widget' );
}
add_action( 'after_setup_theme', 'ajmr_widgets_init', 11 );
