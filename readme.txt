=== Plugin Name ===
Contributors: Olaf Baumann
Donate link: http://blog.zeitgrund.de/category/plugins/last-viewed-posts/
Tags: posts, post, page, pages viewed, recent, widget, sidebar, visitor, history
Requires at least: 2.1
Tested up to: 2.3.1
Stable tag: 0.7

This plugin displays the posts (and pages - if you want) that have been recently viewed by the visitor (single view). 
Data is saved in cookie for each visitor.

== Description ==

This plugin displays the posts (and pages) that have been recently viewed by the visitor (single view).
The posts are saved in in a cookie and by default the last 10 posts that have been visited are displayed.
Note that this is NOT a global listing of recently viewed posts by all users! Nothing is stored in the database.
Every vistor has his own unique listing of the single posts he has viewed.

If cookies are not accepted or no single post has been clicked, no output will be displayed.
The plugin comes with a widget and a template tag.

== Installation ==

Viewed posts are always tracked as long as the plugin is active and the visitor has enabled the cookies.

Download and unzip the plugin file. Upload last_viewed_posts.php to your /wp-content/plugins/ directory.
Go to the admin backend and activate the plugin.

To display the list, you can use the widget that comes with the plugin or use the following code and place it anywhere you want to, but outside the loop, e.g. sidebar.php :

<?php if (function_exists('zg_recently_viewed')): if (isset($_COOKIE["WP-LastViewedPosts"])) { ?>
<h2>Last viewed posts</h2>
<?php zg_recently_viewed(); ?>
<?php } endif; ?>

The single template tag is shown below, but I recommend to use the code shown above.
It prevents you from displaying a blank list if the visitor has cookies disabled or did not view any single post yet.
<?php zg_recently_viewed(); ?>

By default the cookie expires after 360 days, the number of entries that are displayed is 10 and pages are recognized.<br />
To change these values, edit the last_viewed_posts.php between the equals sign and the semicolon:<br /><br />
$zg_cookie_expire = 360; // After how many days should the cookie expire? Default is 360.<br />
$zg_number_of_posts = 10; // How many posts should be displayed in the list? Default is 10.<br />
$zg_recognize_pages = true; // Should pages to be recognized and listed? Default is true.

== Changelog ==

0.7: 	- Pages can now be recognized (optional).
	- Custom Loop is not longer used. Now we make a database query to get the post title.


