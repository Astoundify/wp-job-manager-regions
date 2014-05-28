<?php

class Astoundify_Job_Manager_Regions_Template extends Astoundify_Job_Manager_Regions {

	public function __construct() {
		add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );

		add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'job_manager_job_filters_search_jobs_end' ) );
		add_filter( 'the_job_location', array( $this, 'the_job_location' ), 10, 2 );
	}

	/**
	 * Add the field to the submission form.
	 *
	 * @since 1.0.0
	 */
	public function submit_job_form_fields( $fields ) {
		$fields[ 'job' ][ 'job_region' ] = array(
			'label'       => __( 'Job Region', 'job_manager' ),
			'type'        => 'job-region',
			'required'    => true,
			'priority'    => '2.5',
			'default'     => -1
		);

		return $fields;
	}

	public function job_manager_job_filters_search_jobs_end( $atts ) {
		wp_dropdown_categories( array(
			'show_option_all' => __( 'All Regions', 'wp-job-manager-locations' ),
			'hierarchical' => true,
			'taxonomy' => 'job_listing_region',
			'name' => 'search_region'
		) );
	}

	/**
	 * On a singular job page, append the region to the location.
	 *
	 * @since 1.0.0
	 */
	public function the_job_location( $job_location, $post ) {
		if ( ! is_singular( 'job_listing' ) )
			return $job_location;

		$terms = wp_get_post_terms( $post->ID, 'job_listing_region' );

		if ( is_wp_error( $terms ) || empty( $terms ) )
			return $job_location;

		$location = $terms[0];
		$locname  = $location->name;

		$job_location = sprintf( '%s &mdash; <a href="%s">%s</a>', $job_location, get_term_link( $location, 'job_listing_region' ), $locname );

		return apply_filters( 'wp_job_manager_locations_job_location', $job_location, $location );
	}

}