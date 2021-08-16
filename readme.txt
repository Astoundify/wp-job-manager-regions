=== Regions for WP Job Manager ===
Author URI: http://astoundify.com
Plugin URI: https://astoundify.com/products/wp-job-manager-regions/
Donate link: https://www.paypal.me/astoundify
Contributors: Astoundify
Tags: job, job listing, job region
Requires at least: 4.7.0
Tested up to: 5.8.0
Stable Tag: 1.18.0
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

= 1.18.0: Aug 16, 2021 =

* Update: Compatibility check with latest WordPress v5.8.0.
* Update: Compatibility check with Latest WP Job Manager v1.35.2.
* Fix: Console error issue.

= 1.17.7: Oct 27, 2020 =

* Fix: Minify JS file.
* New: Regions settings implementation for resumes.
* Update: Compatibility check with latest WordPress v5.5.1.
* Update: Compatibility check with Latest WP Job Manager v1.34.3.
* Update: Compatibility check with the latest PHP v7.4.10.

= 1.17.4: February 14, 2019 =

* Fix: Improve type checks for the Select2 library.
* Fix: Make the taxonomy available to REST Api, and now it's available in the new editor.

= 1.17.3: January 30, 2019 =

* Fix: Output region instead of candidate location if setting is enabled.
* Fix: Further WP Job Manager compatibility.

= 1.17.1/2: January 26, 2019 =

* Fix: Ensure select2 arguments are always defined.

= 1.17.0: January 24, 2019 =

* New: WP Job Manager 1.32.0 support.

= 1.16.0: January 14, 2019 =

* New: WP Job Manager 1.32.0 support.

= 1.15.1: May 16, 2018 =

* Fix: Revert use `job_manager_dropdown_categories()` function instead of `wp_dropdown_categories()`.

= 1.15.0: May 12, 2018 =

* New: Use `job_manager_dropdown_categories()` function instead of `wp_dropdown_categories()`.
* New: Add `job_manager_locations_get_terms` and `job_manager_locations_get_term_list_separator` filters for modifying output.

= 1.14.0: July 11, 2017 =

* New: Listify 2.0+ support.

= 1.13.0: April 12, 2017 =

* New: Update README.
* Fix: Update plugin strings.

= 1.12.1: February 1, 2017 =

* Fix: Tested up to: WordPress 4.7.2
* Fix: Move .pot translation file to /languages directory

= 1.12.0: January 10, 2017 =

* New: Tested up to: WordPress 4.7
* Fix: String updates.
* Fix: Only adjust placeholder on our current field.

= 1.11.0: September 1, 2016 =

* New: Tested up to: WordPress 4.6
* New: add in option to disable filtering location
* New: add in new taxonomy for resumes
* Fix: Remove trailing slash and account for https
* Fix: Update settings strings

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
