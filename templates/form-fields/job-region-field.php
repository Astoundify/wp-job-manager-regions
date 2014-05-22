<?php
	wp_dropdown_categories( array(
		'taxonomy' => 'job_listing_region',
		'name' => esc_attr( isset( $field['name'] ) ? $field['name'] : $key ),
		'id' => esc_attr( $key ),
		'hierarchical' => true,
		'selected' => isset( $field[ 'value' ] ) ? $field[ 'value' ] : $field[ 'default' ],
		'show_option_none' => _x( '&mdash;', 'show option none', 'wp-job-manager-locations' ),
		'hide_empty' => false
	) );
?>