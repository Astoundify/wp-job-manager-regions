'use strict';
module.exports = function(grunt) {

	grunt.initConfig({
		pkg : grunt.file.readJSON( 'package.json' ),
		
		uglify : {
			task1: {
				src: 'assets/js/main.js',
				dest: 'assets/js/main.min.js'
			}			
		},
		
		makepot: {
			reviews: {
				options: {
					type: 'wp-plugin'
				}
			}
		},
		checktextdomain: {
			standard: {
				options:{
					force: true,
					text_domain: 'wp-job-manager-locations',
					create_report_file: false,
					correct_domain: true,
					keywords: [
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d', 
						'esc_attr_e:1,2d', 
						'esc_attr_x:1,2c,3d', 
						'_ex:1,2c,3d',
						'_n:1,2,4d', 
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				files: [{
					src: ['**/*.php','!node_modules/**'],
					expand: true,
				}],
			},
		},
	});

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );

	grunt.registerTask( 'pot', [ 'makepot' ] );
};