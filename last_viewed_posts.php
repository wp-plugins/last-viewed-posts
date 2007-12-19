<?php
/*
Plugin Name: Last Viewed Posts
Plugin URI: http://blog.zeitgrund.de/category/plugins/last-viewed-posts/
Description: Show a list of posts (and pages) the visitor had recently viewed. It's cookie based. Every visitor has his own listing. This is not a global output for all users! 
Author: Olaf Baumann
Version: 0.6
Author URI: http://zeitgrund.de
*/

/* Copyright 2007 Olaf Baumann  (http://zeitgrund.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	
	
Use:
For the ouput use the sidebar widget OR place following code just anywhere (outside the loop) into your theme (e.g. sidebar.php).
Note that the output will not appear if there's no cookie set (because cookies are disabled or the user didn't view any single post).
-------------------------------------------

<?php if (function_exists('zg_recently_viewed')):  if (isset($_COOKIE["WP-LastViewedPosts"])) { ?>
 <h2>Last viewed posts</h2>
 <?php zg_recently_viewed(); ?>
<?php }  endif; ?>

------------------------------------------- */

/* Here are some parameters you may want to change: */
$zg_cookie_expire = 360; // After how many days should the cookie expire? Default is 360.
$zg_number_of_posts = 10; // How many posts should be displayed in the list? Default is 10. 

/* Do not edit after this line! */


// Main function is called every time a page is being called
function zg_lw_setcookie() {
	if (is_single()) {		
		global $wp_query; 
		$zg_post_ID = $wp_query->post->ID; // Read post-ID. Is it also working for pages?
		if (! isset($_COOKIE["WP-LastViewedPosts"])) {
			$zg_cookiearray = array($zg_post_ID); // If there's no cookie set, set up a new array
		} else {
			$zg_cookiearray = unserialize(preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", stripslashes($_COOKIE["WP-LastViewedPosts"]))); // Read serialized array from cooke and unserialize it
			if (! is_array($zg_cookiearray)) {
				$zg_cookiearray = array($zg_post_ID); // If array is fucked up...just build a new one.
			}
		}
	   	if (in_array($zg_post_ID, $zg_cookiearray)) { // If the item is already included in the array then remove it
		$zg_key = array_search($zg_post_ID, $zg_cookiearray);  
		array_splice($zg_cookiearray, $zg_key, 1);		
		}
		array_unshift($zg_cookiearray, $zg_post_ID); // Add new entry as first item in array
		global $zg_number_of_posts;
		while (count($zg_cookiearray) > $zg_number_of_posts) { // Limit array to xx (zg_number_of_posts) entries. Otherwise cut off last entry until the right count has been reached
			array_pop($zg_cookiearray);
		}
		global $zg_cookie_expire;
		$zg_blog_url_array = parse_url(get_bloginfo('url')); // Get URL of blog
		$zg_blog_url = $zg_blog_url_array['host']; // Get domain
		$zg_blog_url = str_replace('www.', '', $zg_blog_url);
		$zg_blog_url_dot = '.';
		$zg_blog_url_dot .= $zg_blog_url;
		$zg_path_url = $zg_blog_url_array['path']; // Get path
		$zg_path_url_slash = '/';
		$zg_path_url .= $zg_path_url_slash;
		setcookie("WP-LastViewedPosts", serialize($zg_cookiearray), (time()+($zg_cookie_expire*86400)), $zg_path_url, $zg_blog_url_dot, 0);
	}
}

function zg_recently_viewed() { // Output
	echo '<ul class="viewed_posts">';
	if (isset($_COOKIE["WP-LastViewedPosts"])) {
		//echo "Cookie was set.<br/>";  // For bugfixing - uncomment to see if cookie was set
		//echo $_COOKIE["WP-LastViewedPosts"]; // For bugfixing (cookie content)
		$zg_post_IDs = unserialize(preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", stripslashes($_COOKIE["WP-LastViewedPosts"]))); // Read serialized array from cooke and unserialize it
		foreach ($zg_post_IDs as $value) { // Do output as long there are posts
			$zg_viewed_posts_query = new WP_Query('p=' . $value . ''); // Define new loop with post ID
			while ($zg_viewed_posts_query->have_posts()) : $zg_viewed_posts_query->the_post();
				if ($value == get_the_ID()) { // Make sure that we just show what we want to show (to prevent crazy output that might occure)
					echo "<li><a href=\"". get_permalink() . "\" title=\"". get_the_title() . "\">". get_the_title() . "</a></li>\n"; // Output link and title
				}
			endwhile;
		}		
	} else {
		//echo "No cookie found.";  // For bugfixing - uncomment to see if cookie was not set
	}
	echo '</ul>';
}

function zg_lwp_widget($args) {
	extract($args);
	$options = get_option('zg_lwp_widget');
	$title = htmlspecialchars(stripcslashes($options['title']), ENT_QUOTES);
	$title = empty($options['title']) ? 'Last viewed posts' : $options['title'];
	if (isset($_COOKIE["WP-LastViewedPosts"])) {
		echo $before_widget . $before_title . $title . $after_title;
		zg_recently_viewed();
		echo $after_widget; 
	}
}

function zg_lwp_widget_control() {
	$options = $newoptions = get_option('zg_lwp_widget');	
	if ( $_POST['lwp-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['lwp-title']));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('zg_lwp_widget', $options);
	}
	$title = attribute_escape( $options['title'] );
	?>
	<p><label for="lwp-title">
	<?php _e('Title:') ?> <input type="text" style="width:250px" id="lwp-title" name="lwp-title" value="<?php echo $title ?>" /></label>
	</p>
	<input type="hidden" name="lwp-submit" id="lwp-submit" value="1" />
	<?php
}

function zg_lwp_init() {
  	if ( !function_exists('register_sidebar_widget') )
  		return;
	register_sidebar_widget('Last Viewed Posts','zg_lwp_widget');	
  	register_widget_control('Last Viewed Posts','zg_lwp_widget_control', 250, 100);	
}

add_action('get_header','zg_lw_setcookie');
add_action('widgets_init', 'zg_lwp_init');
?>
