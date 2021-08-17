(function($) {
	'use strict';

	var jobRegions = {
		cache: {
			$document: $(document),
			$window: $(window),
		},

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'ready', function() {
				self.$forms = $( '.search_jobs' );

				if ( typeof job_manager_select2_args === "undefined" ) {
					this.select2_args = {};
				} else {
					this.select2_args = job_manager_select2_args;
				}

				this.select2_args['allowClear']              = true;
				this.select2_args['minimumResultsForSearch'] = 10;

				self.addSubmission();
				self.addRegions();
				self.updateResults();
				self.resetResults();
			});
		},

		addSubmission: function() {
			if ( $.isFunction($.fn.chosen) ) {
				$( '#job_region, #resume_region' ).chosen( {
					search_contains: true,
				} );
			} else if( $.isFunction($.fn.select2) ) {
				$( '#job_region, #resume_region' ).select2( this.select2_args );
			}
		},

		addRegions: function() {
			this.$forms.each(function(i, el) {
				var wrapper = false;
				var $regions = $(el).find( 'select.search_region' );

				// Grab Listify's wrapper element.
				if ( $regions.parent().hasClass( 'select' ) ) {
					wrapper = true;
					$regions = $regions.parent();
				}

				if ( ! $regions.length ) {
					return;
				}

				var location = $(el).find( '.search_location' );
				location.html( '' );
				location.removeClass( 'search_location' ).addClass( 'search_region' );

				$regions.detach().appendTo(location);

				var args = {
						search_contains: true
				};

				if ( $.isFunction($.fn.chosen) ) {
					if ( ! wrapper ) {
						$regions.chosen( args );
					} else {
						$regions.children( 'select' ).chosen( args );
					}
				} else {
					if ( ! wrapper ) {
						$regions.select2( this.select2_args );
					} else {
						$regions.children( 'select' ).select2( this.select2_args );
					}
				}
			});
		},

		updateResults: function() {
			this.$forms.each(function(i, el) {
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
				self.$forms.each(function(i, el) {
					var $regions = $(el).find( 'select.search_region' );
					$regions.val(0).trigger( 'change' ).trigger( 'chosen:updated' );
				});
			});
		}
	};

	jobRegions.init();
	
	var resumeRegions = {
		cache: {
			$document: $(document),
			$window: $(window),
		},

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'ready', function() {
				self.$forms = $( '.search_resumes' );

				self.addSubmission();
				self.addRegions();
				self.updateResults();
				self.resetResults();
			});
		},

		addSubmission: function() {		
			var filter_id = $( '#job_region, #resume_region' );
			if(filter_id.length){		 
				filter_id.chosen({
					search_contains: true
				});			
			}		
		},

		addRegions: function() {
			this.$forms.each(function(i, el) {
				var wrapper = false;
				var $regions = $(el).find( 'select.search_region' );

				// Grab Listify's wrapper element.
				if ( $regions.parent().hasClass( 'select' ) ) {
					wrapper = true;
					$regions = $regions.parent();
				}

				if ( ! $regions.length ) {
					return;
				}

				var location = $(el).find( '.search_location' );
				location.html( '' );
				location.removeClass( 'search_location' ).addClass( 'search_region' );

				$regions.detach().appendTo(location);

				var args = {
						search_contains: true
				};

				if ( ! wrapper ) {
					$regions.chosen( args );
				} else {
					$regions.children( 'select' ).chosen( args );
				}
			});
		},

		updateResults: function() {
			this.$forms.each(function(i, el) {
				var region = $(this).find( '.search_region' );

				region.on( 'change', function() {
					var target = $(this).closest( 'div.resumes' );

					target.trigger( 'update_results', [ 1, false ] );
				});
			});
		},

		resetResults: function() {
			var self = this;

			$( '.resumes' ).on( 'reset', function() {
				self.$forms.each(function(i, el) {
					var $regions = $(el).find( 'select.search_region' );
					$regions.val(0).trigger( 'change' ).trigger( 'chosen:updated' );
				});
			});
		}
	};

	resumeRegions.init();

})(jQuery);
