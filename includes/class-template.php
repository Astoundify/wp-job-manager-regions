<?php

class Astoundify_Job_Manager_Regions_Template extends Astoundify_Job_Manager_Regions {

	public function __construct() {
		// Template loader
		add_filter( 'job_manager_locate_template', array( $this, 'locate_template' ), 10, 3 );

		add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );
		add_filter( 'the_job_location', array( $this, 'the_job_location' ), 10, 2 );

		if ( get_option( 'job_manager_regions_filter' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'job_manager_job_filters_search_jobs_end' ) );
		}
	}

	public function locate_template( $template, $template_name, $template_path ) {
		global $job_manager;

		if ( ! file_exists( $template ) ) {
			$default_path = wp_job_manager_regions()->plugin_dir . '/templates/';

			$template = $default_path . $template_name;
		}

		return $template;
	}

	public function wp_enqueue_scripts() {
		wp_enqueue_script( 'job-regions', wp_job_manager_regions()->plugin_url . '/assets/js/main.js', array( 'jquery' ), 20140525, true );
	}

	/**
	 * Add the field to the submission form.
	 *
	 * @since 1.0.0
	 */
	public function submit_job_form_fields( $fields ) {
		$fields[ 'job' ][ 'job_region' ] = array(
			'label'       => __( 'Job Region', 'wp-job-manager-locations' ),
			'type'        => 'job-region',
			'required'    => true,
			'priority'    => '2.5',
			'default'     => -1
		);

		return $fields;
	}

	public function job_manager_job_filters_search_jobs_end( $atts ) {
		if ('' == $atts[ 'selected_region' ] && $_GET[ 'search_region' ] ) {
			$atts[ 'selected_region' ] = absint( $_GET[ 'search_region' ] );
		}

		wp_dropdown_categories( array(
			'show_option_all' => __( 'All Regions', 'wp-job-manager-locations' ),
			'hierarchical' => true,
			'taxonomy' => 'job_listing_region',
			'name' => 'search_region',
			'class' => 'search_region',
			'hide_empty' => false,
			'selected' => isset( $atts[ 'selected_region' ] ) ? $atts[ 'selected_region' ] : ''
		) );
	}

	/**
	 * Replace location output with the region.
	 *
	 * @since 1.0.0
	 */
	public function the_job_location( $job_location, $post ) {
		$terms = wp_get_post_terms( $post->ID, 'job_listing_region' );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return $job_location;
		}

		$location = $terms[0];

		return $location->name;
	}

}