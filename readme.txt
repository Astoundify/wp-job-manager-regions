=== WP Job Manager - Predefined Regions ===

Author URI: http://astoundify.com
Plugin URI: http://astoundify.com
Donate link: https://www.paypal.me/astoundify
Contributors: Astoundify
Tags: job, job listing, job region
Requires at least: 4.4 
Tested up to: 4.4.*
Stable Tag: 1.10.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add predefined regions to WP Job Manager submission form.

== Description ==

Adds a "Job Region" taxonomy so the site administrator can control a set of predefined regions listings can be assigned to.

**Note:** Listings are not filtered by regions. They are simply used as an organization tool.

= Where can I use this? =

Astoundify has released two themes that are fully integrated with the WP Job Manager plugin. Check out ["Jobify"](http://themeforest.net/item/jobify-job-board-wordpress-theme/5247604?ref=Astoundify) and our WordPress Directory theme ["Listify"](http://themeforest.net/item/listify-wordpress-directory-theme/9602611?ref=Astoundify)

== Installation ==

1. Install and Activate
2. Go to "Job Listings > Job Regions" and add regions.

== Frequently Asked Questions ==

== Changelog ==

= 1.10.0: February 5, 2016 =

* New: Use `search_contains` to search less strictly.

= 1.9.1: October 30, 2015 =

* Fix: Verify the region exists for alerts.

= 1.9.0: September 1, 2015 =

* Fix: WP Job Manager - Alerts support
* Fix: Reset
* Fix: uninstall.php
* Fix: RSS Feeds

= 1.8.1: August 31, 2015 =

* Fix: Taxonomy registration priority to be used with widgets.

= 1.8.0: August 12, 2015 =

* Fix: Listify 1.0.6 compatibility.

= 1.7.3: April 1, 2015 =

* Fix: Make sure the regions dropdown can always replace the location input.
* Fix: Compatibility with WP Job Manager - Alerts.

= 1.7.2: March 17, 2015 =

* Fix: Properly place the standard <select> dropdown for mobile devices.

= 1.7.1: March 15, 2015 =

* New: Add Chosen dropdown support.
* New: Add WP Job Manager Alerts support.
* New: Add Danish tranlsation.
* Tweak: Use $_REQUEST to add support for 1.21.0 of WP Job Manager.
* Fix: Remove extra whitespace.

= 1.7.0: January 8, 2015 =

* New: Add es_ES translation.
* Fix: Make sure translations can always properly be loaded.
* Fix: Always use the dropdown when on a region archive to help with sorting.

= 1.6.1: December 19, 2014 =

Fix: Outputting extra links in the job list.

= 1.6.0: December 17, 2014 =

* New: Add a class to the theme's body tag so the location field can be hidden on term archive pages when filters are off.
* New: Add a filter to allow dropdown arguments to be modified.
* Fix: General code cleanup.

= 1.5.2: November 25, 2014 =

* Tweak: Turn off region dropdown for new installs.

= 1.5.1: October 13, 2014 =

* Fix: Properly unset location input to avoid polluted query.

= 1.5.0: September 9, 2014 =

* New: Regions can now be used to filter listings instead of the standard location text field.

= 1.4.0: May 22, 2014 =

* New: Use a custom template so the select box can have hierarchy.

= 1.3.1: January 20, 2014 =

* Fix: Avoid priority conflict with existing fields.

= 1.3: August 13, 2013 =

* Fix: Update wp-job-manager-locations.php

= 1.2: July 28, 2013 =

* Fix: Make sure the taxononmy is properly added.
* Fix: Don't error if the plugin is activated, but no term is added via the backend

= 1.1: July 27, 2013 =

* New: Simple regions list output.

= 1.0: July 26, 2013 =

* First official release!
