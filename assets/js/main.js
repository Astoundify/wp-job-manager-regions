(function($) {
	'use strict';

	var jobRegions = {
		cache: {
			$document: $(document),
			$window: $(window),
			$search_jobs: $( '.search_jobs, .job_search_form' )
		},

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'ready', function() {
				self.addSubmission();
				self.addRegions();
				self.updateResults();
				self.resetResults();
			});
		},

		addSubmission: function() {
			$( '#job_region' ).chosen();
		},

		addRegions: function() {
			this.cache.$search_jobs.each(function(i, el) {
				$(this).find( '.search_region' ).chosen();
			});

			this.cache.$search_jobs.each(function(i, el) {
				var location = $(this).find( '.search_location' );
				location.html( '' );
				location.removeClass( 'search_location' ).addClass( 'search_region' );

				var $chosen = $( this ).find( '#search_region_chosen' );
				var $std = $(this).find( '#search_region' );

				if ( $chosen.length ) {
					$chosen.detach().appendTo(location);
				} else {
					$std.detach().appendTo(location);
				}
			});
		},

		updateResults: function() {
			this.cache.$search_jobs.each(function(i, el) {
				var region = $(this).find( '.search_region' );

				region.on( 'change', function() {
					var target = $(this).closest( 'div.job_listings' );

					target.trigger( 'update_results', [ 1, false ] );
				});
			});
		},

		resetResults: function() {
			var self = this;

			$( '.job_listings' ).on( 'reset', function() {
				self.cache.$regions.val(0).trigger( 'change' );
			});
		}
	};

	jobRegions.init();

})(jQuery);
