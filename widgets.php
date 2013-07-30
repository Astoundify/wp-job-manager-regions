<?php
/**
 * Widgets
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( class_exists( 'Jobify_Widget' ) ) :
/**
 * Simple list
 */
class Astoundify_Job_Manager_Regions_Widget extends Jobify_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'ajmr_widget_regions';
		$this->widget_description = __( 'Display a list of job regions.', 'ajmr' );
		$this->widget_id          = 'ajmr_widget_regions';
		$this->widget_name        = __( 'Job Regions', 'ajmr' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'Job Regions',
				'label' => __( 'Title:', 'ajmr' )
			)
		);
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) )
			return;

		ob_start();

		extract( $args );

		echo $before_widget;
		
		if ( $instance[ 'title' ] ) echo $before_title . $instance[ 'title' ] . $after_title;

		wp_list_categories( array(
			'title_li'   => '',
			'taxonomy'   => 'job_listing_region',
			'hide_empty' => 0
		) );

		echo $after_widget;

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}
endif;