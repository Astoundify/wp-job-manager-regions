<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

$options = array(
    'job_manager_regions_filter'
);

foreach ( $options as $option ) {
    delete_option( $option );
}
