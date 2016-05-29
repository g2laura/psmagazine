=== Author Recommended Posts ===
Contributors: dtelepathy, oriontimbers, dtlabs, kynatro, jamie3d, bkenyon, moonspired
Donate link: http://www.dtelepathy.com/labs
Tags: related posts, author posts, recommended, reading, author recommended posts, recommended author posts, recommended posts, reading, specific posts, plugin, list of posts, recommended articles
Requires at least: 3.3
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Pick specific posts to promote and recommend to your audience.

== Description ==

= Simple Author Recommended Posts Plugin =
Author Recommended Posts lets you easily search and choose specific posts, pages and custom post types you want to associate to a post or page. You can add, reorder, remove selected posts for display via the simple drag and drop interface.

= Features = 
* Simple clean styles for basic layout/structure
* Show/hide output title & featured images
* Display your recommended posts vertically or side-by-side
* Filter which content is searchable and which post types to automatically output on

**Requirements:** WordPress 3.3+, PHP5 and higher

This plugin is free to use and is not actively supported by the author, but will be monitored for serious bugs that may need correcting.

== Installation ==

The plugin is simple to install:

1. Upload `author-recommended-posts` to the `/wp-content/plugins/` directory
2. Activate the plugin through the Plugins menu in WordPress
3. Visit the options in, Settings > Author Recommended Posts
4. Go to a post, page or your custom post type and choose your recommended posts

== Screenshots ==

1. The search meta box.
2. Horizontal output after the post, with thumbnails.
3. Vertical output after the post, with thumbnails.
4. Vertical output after the post, without thumbnails.

== Frequently Asked Questions ==

= Can I output using a template tag? =
Of course! Just include `<?php echo do_shortcode( '[AuthorRecommendedPosts post_id="XXX"]' ); ?>` where *XXX* is the ID of the post, or you can leave it out if you include it in the loop. i.e. `<?php echo do_shortcode( '[AuthorRecommendedPosts]' ); ?>`

= I want to make it fit the style of my site better =
We left the styles pretty generic so it is simple enough to update to fit your theme style. Of course some CSS writing will be required.

= Is there a widget? =
At this time no, we want to add this in though. So keep your eyes peeled.

= Why did you make this? =
We did not really find any plugins out there that does just this. And we wanted something quick, simple, and easy to use.

== Changelog ==
= 1.0.3 =
* Changed plugin URL constant to properly respect https scheme

= 1.0.2 =
* Added 2 new actions author_recommended_posts_before_related and author_recommended_posts_after_related that can be hooked into for outputting custom content before or after each related entry

= 1.0.1 =
* Fixed posts search order to Desc
* Updateing namespacing in admin.js

= 1.0 =
* Initial release

== Upgrade Notice ==
= 1.0.3 =
Added https fix

= 1.0.2 =
Minor additions

= 1.0.1 =
Minor bug fixes 

= 1.0 =
Initial release