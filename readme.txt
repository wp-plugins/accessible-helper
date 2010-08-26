=== Plugin Name ===
Contributors: kevee
Tags: accessibility
Requires at least: 3.0.0
Tested up to: 3.0.0
Stable tag: 0.3

Gives feedback to authors about the accessibility problems of their posts and pages.

== Description ==

The Accessible Helper plugin helps content authors understand what accessibility problems exist on a page, and what they can do to fix those errors. It keeps track in metdata of posts how many errors exist for that post, and can show a list of all errors, or a highlighted version of the post with errors outlined and prefixed with help icons.

== Installation ==

The plugin requires the open-source QUAIL (QUAIL Accessibility Information Library) before it can be enabled. To download the library go to http://quail-lib.org and download the latest release.

1. Upload the `accessible_helper` directory to the `/wp-content/plugins/` directory
2. Unpack the QUAIL library, and rename the directory to `quail`, instead of `quail-lib-x.x.x`
3. Upload the `quail` directory to `/wp-content/plugins/accessible_helper/`
3. Go to the Plugins page and enable the plugin

== Frequently Asked Questions ==

= How are the guidelines created? ==

The guidelines are inherited from the QUAIL library. You can view all the tests that QUAIL provides, along with their alignment to each guideline at http://quail-lib.org/tests. 

= What are the severity levels all about? =

Tests are broken into three levels -- called severity levels -- that define how accurate a test is, and therefore, how severe errors the test finds are. They are really a level of how sure we are that there is a problem on a page. All accessibility errors should be fixed, but some things are not possible to test through automated processes alone (like if an "alt" text of an image is correct.)

1. Severe errors - Errors where there is a 100% certainty that the problem exists.
2. Moderate errors - There is probably an issue here, but someone should take a look first.
3. Suggestions - This area of the document has a likelihood that there could be an error, but only a human can review this.