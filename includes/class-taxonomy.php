<?php

class Astoundify_Job_Manager_Regions_Taxonomy extends Astoundify_Job_Manager_Regions {

	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomy' ), 0 );
	}

	/**
	 * Create the `job_listing_region` taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register_taxonomy() {
		$admin_capability = 'manage_job_listings';

		$job_singular  = __( 'Job Region', 'wp-job-manager-locations' );
		$job_plural    = __( 'Job Regions', 'wp-job-manager-locations' );

		if ( current_theme_supports( 'job-manager-templates' ) ) {
			$job_rewrite     = array(
				'slug'         => _x( 'job-region', 'Job region slug - resave permalinks after changing this', 'wp-job-manager-locations' ),
				'with_front'   => false,
				'hierarchical' => false
			);
			$resume_rewrite     = array(
				'slug'         => _x( 'resume-region', 'Resume region slug - resave permalinks after changing this', 'wp-job-manager-locations' ),
				'with_front'   => false,
				'hierarchical' => false
			);
		} else {
			$job_rewrite = false;
			$resume_rewrite = false;
		}

		register_taxonomy( 'job_listing_region',
			array( 'job_listing' ),
			array(
				'hierarchical' 			=> true,
				'update_count_callback' => '_update_post_term_count',
				'label' 				=> $job_plural,
				'labels' => array(
					'name' 				=> $job_plural,
					'singular_name' 	=> $job_singular,
					'search_items' 		=> sprintf( __( 'Search %s', 'wp-job-manager-locations' ), $job_plural ),
					'all_items' 		=> sprintf( __( 'All %s', 'wp-job-manager-locations' ), $job_plural ),
					'parent_item' 		=> sprintf( __( 'Parent %s', 'wp-job-manager-locations' ), $job_singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'wp-job-manager-locations' ), $job_singular ),
					'edit_item' 		=> sprintf( __( 'Edit %s', 'wp-job-manager-locations' ), $job_singular ),
					'update_item' 		=> sprintf( __( 'Update %s', 'wp-job-manager-locations' ), $job_singular ),
					'add_new_item' 		=> sprintf( __( 'Add New %s', 'wp-job-manager-locations' ), $job_singular ),
					'new_item_name' 	=> sprintf( __( 'New %s Name', 'wp-job-manager-locations' ),  $job_singular )
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
				'rewrite' 				=> $job_rewrite,
			)
		);

		$resume_singular  = __( 'Resume Region', 'wp-job-manager-locations' );
		$resume_plural    = __( 'Resume Regions', 'wp-job-manager-locations' );

		register_taxonomy( 'resume_region',
			array( 'resume' ),
			array(
				'hierarchical' 			=> true,
				'update_count_callback' => '_update_post_term_count',
				'label' 				=> $resume_plural,
				'labels' => array(
					'name' 				=> $resume_plural,
					'singular_name' 	=> $resume_singular,
					'search_items' 		=> sprintf( __( 'Search %s', 'wp-job-manager-locations' ), $resume_plural ),
					'all_items' 		=> sprintf( __( 'All %s', 'wp-job-manager-locations' ), $resume_plural ),
					'parent_item' 		=> sprintf( __( 'Parent %s', 'wp-job-manager-locations' ), $resume_singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'wp-job-manager-locations' ), $resume_singular ),
					'edit_item' 		=> sprintf( __( 'Edit %s', 'wp-job-manager-locations' ), $resume_singular ),
					'update_item' 		=> sprintf( __( 'Update %s', 'wp-job-manager-locations' ), $resume_singular ),
					'add_new_item' 		=> sprintf( __( 'Add New %s', 'wp-job-manager-locations' ), $resume_singular ),
					'new_item_name' 	=> sprintf( __( 'New %s Name', 'wp-job-manager-locations' ),  $resume_singular )
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
				'rewrite' 				=> $resume_rewrite,
			)
		);
	}

}
