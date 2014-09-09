(function($) {
	'use strict';

	var jobRegions = {
		cache: {
			$document: $(document),
			$window: $(window),
			$search_jobs: $( '.search_jobs, .job_search_form' )
		},

		init: function() {
			this.cacheElements();
			this.bindEvents();
		},

		cacheElements: function() {
			this.cache.$location = this.cache.$search_jobs.find( '.search_location' );
			this.cache.$regions = this.cache.$search_jobs.find( '.search_region' ).first();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'ready', function() {
				self.addRegions();
				self.updateResults();
				self.resetResults();
			});
		},

		addRegions: function() {
			this.cache.$location.html( '' );
			this.cache.$regions.detach().appendTo( this.cache.$location );
		},

		updateResults: function() {
			this.cache.$regions.change(function() {
				var target = jQuery(this).closest( 'div.job_listings' );

				target.trigger( 'update_results', [ 1, false ] );
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