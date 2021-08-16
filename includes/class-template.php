<?php

class Astoundify_Job_Manager_Regions_Template extends Astoundify_Job_Manager_Regions {

	public function __construct() {
		add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );
		add_filter( 'submit_resume_form_fields', array( $this, 'submit_resume_form_fields' ) );

		if ( get_option( 'job_manager_enable_regions_filter' ) ) {
			add_filter( 'the_job_location', array( $this, 'the_job_location' ), 10, 2 );
		}
		if ( get_option( 'resume_manager_enable_regions_filter' ) ) {
			add_filter( 'the_candidate_location', array( $this, 'the_candidate_location' ), 10, 2 );
		}

		add_filter( 'submit_job_form_fields_get_job_data', array( $this, 'submit_job_form_fields_get_job_data' ), 10, 2 );
		add_filter( 'submit_resume_form_fields_get_resume_data', array( $this, 'submit_resume_form_fields_get_resume_data' ), 10, 2 );
		
		add_filter( 'job_manager_term_select_field_wp_dropdown_categories_args', array( $this, 'job_manager_term_select_field_wp_dropdown_categories_args' ), 10, 3 );

		add_action( 'wp', array( $this, 'sort' ) );
	}

	public function sort() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		if ( get_option( 'job_manager_regions_filter' ) || is_tax( 'job_listing_region' ) ) {
			add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'job_manager_job_filters_search_jobs_end' ) );
		} else {
			add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'tax_archive_field' ) );
			add_filter( 'body_class', array( $this, 'body_class' ) );
		}
		if ( get_option( 'resume_manager_regions_filter' ) || is_tax( 'resume_region' ) ) {
			add_action( 'resume_manager_resume_filters_search_resumes_end', array( $this, 'resume_manager_resume_filters_search_resumes_end' ) );
		} else {
			add_action( 'resume_manager_resume_filters_search_resumes_end', array( $this, 'tax_archive_field' ) );
			add_filter( 'body_class', array( $this, 'body_class' ) );
		}
	}

	/**
	 * Frontend scripts.
	 */
	public function wp_enqueue_scripts() {
		$deps = array( 'jquery' );
		if(function_exists('WPJM')){
			$wpjm = WPJM();
			if ( method_exists( $wpjm, 'register_select2_assets' ) ) {
				$wpjm::register_select2_assets();

				wp_enqueue_script( 'select2' );
				wp_enqueue_style( 'select2' );
			} else {
				wp_enqueue_script( 'chosen' );
				wp_enqueue_style( 'chosen' );
			}

			wp_enqueue_script( 'job-regions', wp_job_manager_regions()->plugin_url . 'assets/js/main.min.js', array( 'jquery' ), 20190128, true );
		}
	}

	public function submit_resume_form_fields_get_resume_data( $fields, $job ) {
		$field = isset( $fields[ 'resume_fields' ][ 'resume_region' ] ) ? $fields[ 'resume_fields' ][ 'resume_region' ] : false;

		if ( $field ) {
			$fields[ 'resume_fields' ][ 'resume_region' ][ 'value' ] = wp_get_object_terms( $job->ID, $field['taxonomy'], array( 'fields' => 'ids' ) );
		}

		return $fields;
	}

	public function submit_job_form_fields_get_job_data( $fields, $job ) {
		$field = isset( $fields[ 'job' ][ 'job_region' ] ) ? $fields[ 'job' ][ 'job_region' ] : false;

		if ( $field ) {
			$fields[ 'job' ][ 'job_region' ][ 'value' ] = wp_get_object_terms( $job->ID, $field['taxonomy'], array( 'fields' => 'ids' ) );
		}

		return $fields;
	}

	public function submit_resume_form_fields( $fields ) {
		$fields[ 'resume_fields' ][ 'resume_region' ] = array(
			'label'       => __( 'Region', 'wp-job-manager-locations' ),
			'type'        => 'term-select',
			'taxonomy'    => 'resume_region',
			'required'    => true,
			'priority'    => '2.5',
			'default'     => -1
		);

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
			'multiple' => false,
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
	 * Add the field to the filters
	 */
	public function resume_manager_resume_filters_search_resumes_end( $atts ) {
		if ( ( ! isset( $atts[ 'selected_region' ] ) || '' == $atts[ 'selected_region' ] ) && isset( $_GET[ 'search_region' ] ) ) {
			$atts[ 'selected_region' ] = absint( $_GET[ 'search_region' ] );
		}

		wp_dropdown_categories( apply_filters( 'resume_manager_regions_dropdown_args', array(
			'show_option_all' => __( 'All Regions', 'wp-job-manager-locations' ),
			'hierarchical' => true,
			'orderby' => 'name',
			'taxonomy' => 'resume_region',
			'name' => 'search_region',
			'class' => 'search_region',
			'hide_empty' => 0,
			'selected' => isset( $atts[ 'selected_region' ] ) ? $atts[ 'selected_region' ] : ''
		) ) );
	}

	public function job_manager_term_select_field_wp_dropdown_categories_args( $args, $key, $field ) {
		if ( 'job_region' !== $key ) {
			return $args;
		}

		$args['show_option_none'] = __( 'Select Region', 'wp-job-manager-locations' );
		$args['option_none_value'] = '';

		return $args;
	}
	public function the_job_location( $job_location, $post ) {
		if ( is_singular( 'job_listing' ) ) {
			return strip_tags( get_the_term_list( $post->ID, 'job_listing_region', '', ', ', '' ) );
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
		return implode( ', ', $names );
	}
	
	/**
	 * Replace location output with the region.
	 *
	 * @since 1.15.0
	 */
	public function the_candidate_location( $job_location, $post ) {
		if ( is_singular( 'resume' ) ) {
			return strip_tags( get_the_term_list( $post->ID, 'resume_region', '', ', ', '' ) );
		} else {
			$terms = wp_get_object_terms( $post->ID, 'resume_region', array( 'orderby' => 'term_order', 'order' => 'desc') );

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
	public function the_listing_location( $job_location, $post ) {

		$terms  = wp_get_object_terms(
			$post->ID, 
			'job_listing' === $post->post_type ? 'job_listing_region' : 'resume_region',
			apply_filters( 'job_manager_locations_get_terms', array(
				'orderby' => 'term_order',
				'order'   => 'desc'
			) )
		);

		$_terms = array();

		if ( empty( $terms ) ) {
			return;
		}

		if ( is_singular( array( 'job_listing', 'resume' ) ) ) {
			foreach ( $terms as $term ) {
				$_terms[] = '<a href=" ' . esc_url( get_term_link( $term ) ) . '">' . $term->name . '</a>';
			}

		} else {
			foreach ( $terms as $term ) {
				$_terms[] = $term->name;
			}
		}

		$separator = apply_filters( 'job_manager_locations_get_term_list_separator', ', ' );

		return implode( $separator, $_terms );
	}
}
