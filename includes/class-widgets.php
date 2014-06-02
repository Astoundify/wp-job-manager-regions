<?php
/**
 * Widgets
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Astoundify_Job_Manager_Regions_Widgets extends Astoundify_Job_Manager_Regions {

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

	public function widgets_init() {
		register_widget( 'Astoundify_Job_Manager_Regions_Widget' );
	}

}