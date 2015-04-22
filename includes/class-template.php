<?php

class Astoundify_Job_Manager_Regions_Template extends Astoundify_Job_Manager_Regions {

	public function __construct() {
		add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );
		add_filter( 'the_job_location', array( $this, 'the_job_location' ), 10, 2 );
		add_filter( 'submit_job_form_fields_get_job_data', array( $this, 'submit_job_form_fields_get_job_data' ), 10, 2 );

		add_action( 'wp', array( $this, 'sort' ) );
	}

	public function sort() {
		if ( get_option( 'job_manager_regions_filter' ) || is_tax( 'job_listing_region' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'job_manager_job_filters_search_jobs_end' ) );
		} else {
			add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'tax_archive_field' ) );
			add_filter( 'body_class', array( $this, 'body_class' ) );
		}
	}

	/**
	 * Frontend scripts.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_script( 'job-regions', wp_job_manager_regions()->plugin_url . '/assets/js/main.js', array( 'jquery', 'chosen' ), 20140525, true );
	}

	public function submit_job_form_fields_get_job_data( $fields, $job ) {
		$field = isset( $fields[ 'job' ][ 'job_region' ] ) ? $fields[ 'job' ][ 'job_region' ] : false;

		if ( $field ) {
			$fields[ 'job' ][ 'job_region' ][ 'value' ] = wp_get_object_terms( $job->ID, $field['taxonomy'], array( 'fields' => 'ids' ) );
		}

		return $fields;
	}

	/**
	 * Add the field to the submission form.
	 */
	public function submit_job_form_fields( $fields ) {
		$fields[ 'job' ][ 'job_region' ] = array(
			'label'       => __( 'Job Region', 'wp-job-manager-locations' ),
			'type'        => 'term-select',
			'taxonomy'    => 'job_listing_region',
			'required'    => true,
			'priority'    => '2.5',
			'default'     => -1
		);

		return $fields;
	}

	/**
	 * Add the field to the filters
	 */
	public function job_manager_job_filters_search_jobs_end( $atts ) {
		if ( ( ! isset( $atts[ 'selected_region' ] ) || '' == $atts[ 'selected_region' ] ) && isset( $_GET[ 'search_region' ] ) ) {
			$atts[ 'selected_region' ] = absint( $_GET[ 'search_region' ] );
		}

		wp_dropdown_categories( apply_filters( 'job_manager_regions_dropdown_args', array(
			'show_option_all' => __( 'All Regions', 'wp-job-manager-locations' ),
			'hierarchical' => true,
			'orderby' => 'name',
			'taxonomy' => 'job_listing_region',
			'name' => 'search_region',
			'class' => 'search_region',
			'hide_empty' => 0,
			'selected' => isset( $atts[ 'selected_region' ] ) ? $atts[ 'selected_region' ] : ''
		) ) );
	}

	/**
	 * If we are not using regions on the filter set a hidden field so the AJAX
	 * call still only looks in that area.
	 */
	public function tax_archive_field( $atts ) {
		if ( ( ! isset( $atts[ 'selected_region' ] ) || '' == $atts[ 'selected_region' ] ) && isset( $_GET[ 'search_region' ] ) ) {
			$atts[ 'selected_region' ] = absint( $_GET[ 'search_region' ] );
		}

		echo '<input type="hidden" name="search_region" class="search_region" value="' . $atts[
		'selected_region' ]. '" />';
	}

	/**
	 * If we are not using regions on the filter set a body class so themes can hide the text
	 * input field so they don't have false thoughts about searching.
	 */
	public function body_class( $classes ) {
		if ( is_tax( 'job_listing_region' ) ) {
			$classes[] = 'wp-job-manager-regions-no-filter';
		}

		return $classes;
	}

	/**
	 * Replace location output with the region.
	 *
	 * @since 1.0.0
	 */
	public function the_job_location( $job_location, $post ) {
		if ( is_singular( 'job_listing' ) ) {
			return get_the_term_list( $post->ID, 'job_listing_region', '', ', ', '' );
		} else {
			$terms = wp_get_object_terms( $post->ID, 'job_listing_region', array( 'orderby' => 'term_order', 'order' => 'desc') );

			if ( empty( $terms ) ) {
				return;
			}

			$names = array();

			foreach ( $terms as $term ) {
				$names[] = $term->name;
			}

			return implode( ', ', $names );
		}
	}
}
