<?php
/**
 * Plugin Name: WP Job Manager - Predefined Regions
 * Plugin URI:  https://github.com/astoundify/wp-job-manager-regions/
 * Description: Create predefined regions/locations that job submissions can associate themselves with.
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     1.4.0
 * Text Domain: wp-job-manager-locations
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
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Set some smart defaults to class variables. Allow some of them to be
	 * filtered to allow for early overriding.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function setup_globals() {
		$this->file         = __FILE__;

		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url ( $this->file );

		$this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );

		$this->domain       = 'wp-job-manager-locations';
	}

	private function includes() {
		$files = array(
			'includes/class-taxonomy.php',
			'includes/class-template.php',
			'includes/class-widgets.php'
		);

		foreach ( $files as $file ) {
			include_once( $this->plugin_dir . '/' . $file;
		}
	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function setup_actions() {
		add_filter( 'job_manager_locate_template', array( $this, 'locate_template' ), 10, 3 );

		add_action( 'job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );
		add_filter( 'submit_job_form_fields_get_job_data', array( $this, 'form_fields_get_job_data' ), 10, 2 );

		$this->load_textdomain();
	}

	public function locate_template( $template, $template_name, $template_path ) {
		global $job_manager;

		if ( ! file_exists( $template ) ) {
			$default_path = $this->plugin_dir . '/templates/';

			$template = $default_path . $template_name;
		}

		return $template;
	}

	/**
	 * Get the current value for the job region. We can't rely
	 * on basic meta value getting, instead we need to find the term.
	 *
	 * @since 1.0.0
	 */
	function form_fields_get_job_data( $fields, $job ) {
		$fields[ 'job' ][ 'job_region' ][ 'value' ] = current( wp_get_object_terms( $job->ID, 'job_listing_region', array( 'fields' => 'slugs' ) ) );

		return $fields;
	}

	/**
	 * When the form is submitted, update the data.
	 *
	 * @since 1.0.0
	 */
	function update_job_data( $job_id, $values ) {
		$region = isset ( $values[ 'job' ][ 'job_region' ] ) ? $values[ 'job' ][ 'job_region' ] : null;

		if ( ! $region )
			return;

		$term = get_term_by( 'slug', $region, 'job_listing_region' );

		wp_set_post_terms( $job_id, array( $term->term_id ), 'job_listing_region', false );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @since 1.0.0
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
 * @since 1.0.0
 */
function wp_job_manager_locations() {
	return Astoundify_Job_Manager_Regions::instance();
}

wp_job_manager_locations();