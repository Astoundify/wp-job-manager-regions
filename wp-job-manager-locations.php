<?php
/**
 * Plugin Name: Regions for WP Job Manager
 * Plugin URI:  https://wordpress.org/plugins/wp-job-manager-locations/
 * Description: Create predefined regions/locations that job submissions can associate themselves with.
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     1.14.0
 * Text Domain: wp-job-manager-locations
 * Domain Path: /languages
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
        $this->file         = __FILE__;
        $this->basename     = plugin_basename( $this->file );
        $this->plugin_dir   = plugin_dir_path( $this->file );
        $this->plugin_url   = set_url_scheme( plugin_dir_url ( $this->file ), is_ssl() ? 'https' : 'http' );
        $this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );
        $this->domain       = 'wp-job-manager-locations';

        $files = array(
            'includes/class-taxonomy.php',
            'includes/class-template.php',
            'includes/class-widgets.php'
        );

        foreach ( $files as $file ) {
            include_once( $this->plugin_dir . '/' . $file );
        }

        $this->taxonomy = new Astoundify_Job_Manager_Regions_Taxonomy;
        $this->template = new Astoundify_Job_Manager_Regions_Template;

        $this->setup_actions();
    }

    /**
     * Setup the default hooks and actions
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function setup_actions() {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        /* Job Manager */
        add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );

        add_filter( 'job_manager_output_jobs_defaults', array( $this, 'job_manager_output_jobs_defaults' ) );
        add_filter( 'job_manager_get_listings', array( $this, 'job_manager_get_listings' ), 10, 2 );
        add_filter( 'job_manager_get_listings_args', array( $this, 'job_manager_get_listings_args' ) );

        add_filter( 'job_feed_args', array( $this, 'job_feed_args' ) );

        /* Resumes */
        add_filter( 'resume_manager_settings', array( $this, 'resume_manager_settings' ) );

        add_filter( 'resume_manager_output_resumes_defaults', array( $this, 'resume_manager_output_resumes_defaults' ) );
        add_filter( 'resume_manager_get_resumes', array( $this, 'resume_manager_get_resumes' ), 10, 2 );
        add_filter( 'resume_manager_get_resumes_args', array( $this, 'job_manager_get_listings_args' ) );
    }

    /**
     * Add settings fields to select the appropriate form for each listing type.
     *
     * @since WP Job Manager - Predefiend Regions 1.4.1
     *
     * @return void
     */
    public function job_manager_settings( $settings ) {
        $settings[ 'job_listings' ][1][] = array(
            'name'     => 'job_manager_enable_regions_filter',
            'std'      => '1',
            'label'    => __( 'Filter Location Display', 'wp-job-manager-locations' ),
            'cb_label' => __( 'Display Region', 'wp-job-manager-locations' ),
            'desc'     => __( 'Replace the entered address with the selected region on output.', 'wp-job-manager-locations' ),
            'type'     => 'checkbox'
        );
        $settings[ 'job_listings' ][1][] = array(
            'name'     => 'job_manager_regions_filter',
            'std'      => '0',
            'label'    => __( 'Search by Region', 'wp-job-manager-locations' ),
            'cb_label' => __( 'Search by Region', 'wp-job-manager-locations' ),
            'desc'     => __( 'Use a dropdown of defined regions instead of a text input. Disables radius search.', 'wp-job-manager-locations' ),
            'type'     => 'checkbox'
        );

        return $settings;
    }

    public function resume_manager_settings( $settings ) {
        $settings[ 'resume_listings' ][1][] = array(
            'name'     => 'resume_manager_enable_regions_filter',
            'std'      => '1',
            'label'    => __( 'Filter Location Display', 'wp-job-manager-locations' ),
            'cb_label' => __( 'Display Region', 'wp-job-manager-locations' ),
            'desc'     => __( 'Replace the entered address with the selected region on output.', 'wp-job-manager-locations' ),
            'type'     => 'checkbox'
        );
        $settings[ 'resume_listings' ][1][] = array(
            'name'     => 'resume_manager_regions_filter',
            'std'      => '0',
            'label'    => __( 'Search by Region', 'wp-job-manager-locations' ),
            'cb_label' => __( 'Search by Region', 'wp-job-manager-locations' ),
            'desc'     => __( 'Use a dropdown of defined regions instead of a text input. Disables radius search.', 'wp-job-manager-locations' ),
            'type'     => 'checkbox'
        );

        return $settings;
    }

    /**
     * Modify the default shortcode attributes for displaying listings.
     *
     * If we are on a listing region term archive set the selected_region so
     * we can preselect the dropdown value. This is needed when filtering by region.
     */
    public function job_manager_output_jobs_defaults( $defaults ) {
        $defaults[ 'selected_region' ] = '';

        if ( is_tax( 'job_listing_region' ) ) {
            $type = get_queried_object();

            if ( ! $type ) {
                return $defaults;
            }

            $defaults[ 'show_categories' ] = true;
            $defaults[ 'selected_region' ] = $type->term_id;
        }

        return $defaults;
    }

    public function resume_manager_output_resumes_defaults( $defaults ) {
        $defaults[ 'selected_region' ] = '';

        if ( is_tax( 'resume_region' ) ) {
            $type = get_queried_object();

            if ( ! $type ) {
                return $defaults;
            }

            $defaults[ 'show_categories' ] = true;
            $defaults[ 'selected_region' ] = $type->term_id;
        }

        return $defaults;
    }

    public function job_manager_get_listings( $query_args, $args ) {
        $params = array();

        if ( isset( $_REQUEST[ 'form_data' ] ) ) {

            parse_str( $_REQUEST[ 'form_data' ], $params );

            if ( isset( $params[ 'search_region' ] ) && 0 != $params[ 'search_region' ] ) {
                $region = $params[ 'search_region' ];

                if ( is_int( $region ) ) {
                    $region = array( $region );
                }

                $query_args[ 'tax_query' ][] = array(
                    'taxonomy' => 'job_listing_region',
                    'field'    => 'id',
                    'terms'    => $region,
                    'operator' => 'IN'
                );

                add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
                add_filter( 'job_manager_get_listings_custom_filter_text', array( $this, 'custom_filter_text' ) );
                add_filter( 'job_manager_get_listings_custom_filter_rss_args', array( $this, 'custom_filter_rss' ) );
            }

        } elseif ( isset( $_GET[ 'selected_region' ] ) ) {

            $region = $_GET[ 'selected_region' ];

            if ( is_int( $region ) ) {
                $region = array( $region );
            }

            $query_args[ 'tax_query' ][] = array(
                'taxonomy' => 'job_listing_region',
                'field'    => 'id',
                'terms'    => $region,
                'operator' => 'IN'
            );

        } elseif( isset( $args['search_region'] ) ) { // WPJM Alerts support
            $region = $args[ 'search_region' ];

			if ( is_array( $region ) && empty( $region ) ) {
				return $query_args;
			}

            $query_args[ 'tax_query' ][] = array(
                'taxonomy' => 'job_listing_region',
                'field'    => 'id',
                'terms'    => $region,
                'operator' => 'IN'
            );

        }

        return $query_args;
    }

    public function resume_manager_get_resumes( $query_args, $args ) {
        $params = array();

        if ( isset( $_REQUEST[ 'form_data' ] ) ) {

            parse_str( $_REQUEST[ 'form_data' ], $params );

            if ( isset( $params[ 'search_region' ] ) && 0 != $params[ 'search_region' ] ) {
                $region = $params[ 'search_region' ];

                if ( is_int( $region ) ) {
                    $region = array( $region );
                }

                $query_args[ 'tax_query' ][] = array(
                    'taxonomy' => 'resume_region',
                    'field'    => 'id',
                    'terms'    => $region,
                    'operator' => 'IN'
                );

                add_filter( 'resume_manager_get_resumes_custom_filter', '__return_true' );
                add_filter( 'resume_manager_get_resumes_custom_filter_text', array( $this, 'resume_custom_filter_text' ) );
            }

        } elseif ( isset( $_GET[ 'selected_region' ] ) ) {

            $region = $_GET[ 'selected_region' ];

            if ( is_int( $region ) ) {
                $region = array( $region );
            }

            $query_args[ 'tax_query' ][] = array(
                'taxonomy' => 'resume_region',
                'field'    => 'id',
                'terms'    => $region,
                'operator' => 'IN'
            );

        } elseif( isset( $args['search_region'] ) ) { // WPJM Alerts support
            $region = $args[ 'search_region' ];

            if ( is_array( $region ) && empty( $region ) ) {
                return $query_args;
            }

            $query_args[ 'tax_query' ][] = array(
                'taxonomy' => 'resume_region',
                'field'    => 'id',
                'terms'    => $region,
                'operator' => 'IN'
            );

        }

        return $query_args;
    }

    /**
     * Filter the AJAX request to set the search location to null if a region
     * is being passed as well.
     */
    public function job_manager_get_listings_args( $args ) {
        $params = array();

        if ( isset( $_REQUEST[ 'form_data' ] ) ) {

            parse_str( $_REQUEST[ 'form_data' ], $params );

            if ( isset( $params[ 'search_region' ] ) && 0 != $params[ 'search_region' ] ) {
                $args[ 'search_location' ] = null;
            }

        }

        return $args;
    }

    /**
     * Filter the AJAX to update the "showing" text.
     */
    public function custom_filter_text( $text ) {
        $params = array();

        parse_str( $_REQUEST[ 'form_data' ], $params );

        $term = get_term( $params[ 'search_region' ], 'job_listing_region' );

        $text .= sprintf( ' ' .  __( 'in %s', 'wp-job-manager-locations' ) . ' ', $term->name );

        return $text;
    }

    public function resume_custom_filter_text( $text ) {
        $params = array();

        parse_str( $_REQUEST[ 'form_data' ], $params );

        $term = get_term( $params[ 'search_region' ], 'resume_region' );

        $text .= sprintf( ' ' .  __( 'in %s', 'wp-job-manager-locations' ) . ' ', $term->name );

        return $text;
    }

    /**
     * Filter the AJAX request to update the RSS feed URL.
     */
    public function custom_filter_rss( $args ) {
        $params = array();

        parse_str( $_REQUEST[ 'form_data' ], $params );

        $args[ 'job_region' ] = $params[ 'search_region' ];

        return $args;
    }

    public function job_feed_args( $query_args ) {
        $region = isset( $_GET[ 'job_region' ] ) ? $_GET[ 'job_region' ] : false;

        if ( ! $region ) {
            return $query_args;
        }

        $region = esc_attr( $region );

        if ( is_int( $region ) ) {
            $region = array( absint( $region ) );
        }

        $query_args[ 'tax_query' ][] = array(
            'taxonomy' => 'job_listing_region',
            'field'    => 'id',
            'terms'    => $region,
            'operator' => 'IN'
        );

        return $query_args;
    }

    /**
     * Loads the plugin language files
     */
    public function load_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-locations' );
        load_textdomain( 'wp-job-manager-locations', WP_LANG_DIR . "/wp-job-manager-locations/wp-job-manager-locations-$locale.mo" );
        load_plugin_textdomain( 'wp-job-manager-locations', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
function wp_job_manager_regions() {
    return Astoundify_Job_Manager_Regions::instance();
}

wp_job_manager_regions();
