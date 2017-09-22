<?php
/**
 * Widgets
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}

/**
 * Widgets
 *
 * @since 1.0.0
 */
class Astoundify_Job_Manager_Regions_Widgets extends Astoundify_Job_Manager_Regions {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! class_exists( 'Jobify_Widget' ) ) {
			return;
		}

		$widgets = array(
			'class-widget-region-list.php'
		);

		foreach ( $widgets as $widget ) {
			include_once( $this->plugin_dir . '/' . $widget );
		}

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	/**
	 * Widgets Init
	 *
	 * @since 1.0.0
	 */
	public function widgets_init() {
		register_widget( 'Astoundify_Job_Manager_Regions_Widget' );
	}

}
