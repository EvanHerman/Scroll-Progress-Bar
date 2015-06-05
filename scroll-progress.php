<?php
/**
 * @package Scroll Progress Bar
 */
/*
Plugin Name: Scroll Progress Bar
Plugin URI: http://www.evan-herman.com
Description: Bar at the top of your posts and pages to indicate the location on the page.
Version: 0.1
Author: eherman24
Author URI: https://www.evan-herman.com
License: GPLv2 or later
Text Domain: scroll-progress
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*
*	Helper funciton to get our options with default values
*/
function spb_get_options() {
	// get and store default options array
	return $scroll_progress_options = get_option( 'scroll_progress_options' , array(
		'progress_bar_colorpicker' => '#bada55',
		'progress_bar_display_on_post_types' => array( 'post' , 'page' , 'home_page' ),
		'progress_bar_exclude_from' => '',
		'progress_bar_top_offset' => '0',
		'smooth_scroll_active' => 'true',
		'smooth_scroll_scroll_time' => '.5',
		'smooth_scroll_pixel_offset' => '300',
	) );
}

/*
*	Print our the progress bar
*	On the frontend of the site
*/
function spb_append_progress_indicator() {
	// get the global $post variable
	global $post;
	
	// retreive + store our options
	$scroll_progress_options = spb_get_options();
	
	// load our smooth scroll scripts
	spb_load_smooth_scroll( $scroll_progress_options );	
	
	// if our the user hasn't set any post types, don't do anything 
	if( !isset( $scroll_progress_options['progress_bar_display_on_post_types'] ) ) {
		return false;
	}
	
	// check if were on the correct page to display our progress bar
	$display_on_post_types = $scroll_progress_options['progress_bar_display_on_post_types'];
	
	if( !empty( $display_on_post_types ) ) {
		// check if the user has setup any posts to exclude the progress bar from
		if( isset( $scroll_progress_options['progress_bar_exclude_from'] ) && $scroll_progress_options['progress_bar_exclude_from'] != '' ) {
			// get our option of what post ID's to exclude the progress bar from
			$exclude_from_post_id = explode( ',' , $scroll_progress_options['progress_bar_exclude_from'] );
			// if the current post ID is in the array of excluded posts, 
			// do not load, render or display the progress bar
			if( in_array( $post->ID , $exclude_from_post_id ) ) {
				return false;
			}
		}
		// loop over to check current screen vs post type to display on
		foreach( $display_on_post_types as $post_type ) {
			if( is_singular( $post_type ) ) {
				// render the scroll bar progress indicator
				spb_render_scroll_progress_bar( $scroll_progress_options );
			}
		}
		// if the user has opted to display on 'pages',
		// that should also include archive pages
		if( in_array( 'page' , $display_on_post_types ) ) {
			if( is_archive() ) {
				// render the scroll bar progress indicator
				spb_render_scroll_progress_bar( $scroll_progress_options );
			}
		}
		// if the user has opted to display on the front page,
		// lets confirm were there or else abort
		if( in_array( 'home_page' , $display_on_post_types ) ) {
			if( is_front_page() ) {
				// render the scroll bar progress indicator
				spb_render_scroll_progress_bar( $scroll_progress_options );
			}
		}
	}
}
add_action( 'wp_head' , 'spb_append_progress_indicator' , 0 );

/*
*	Render our scroll bar progress indicator
*	@since 0.1
*/
function spb_render_scroll_progress_bar( $scroll_progress_options ) {
	// enqueue our scripts/styles (Progress Bar)
	add_action( 'wp_enqueue_scripts' , 'spb_enqueue_scripts_and_styles' );		
	// render the progress bar at the top of our page
	?>
	<!-- override default color via settings page -->
	<style type="text/css">
	#progressBar.flat::-webkit-progress-value {
		background-color: <?php echo $scroll_progress_options['progress_bar_colorpicker']; ?> !important;
	}
	<?php 
		$offset_top = $scroll_progress_options['progress_bar_top_offset'];
		if( is_user_logged_in() ) {
			$offset_top += 32;
		} 
	?>
		#progressBar.flat {
			margin-top: <?php echo $offset_top; ?>px !important;
		}
	</style>
	<progress value="0" id="progressBar" class="flat" title="<?php _e( 'Page location progress bar' , 'scroll-progress' ); ?>">
		<div class="progress-container">
			<span class="progress-bar"></span>
		</div>
	</progress>
	<?php
}	

/*
*	Load the necessary smooth scroll scripts
*	@since 0.1
*/
function spb_load_smooth_scroll( $scroll_progress_options ) {
	if( $scroll_progress_options['smooth_scroll_active'] == 'true' ) {
		// enqueue our scripts/styles (Smooth Scroll)
		add_action( 'wp_enqueue_scripts' , 'spb_smooth_scroll_scripts_and_styles' );
	}
}

/* 
*	Enqueue Scripts/Styles
*	- for our progress bar
*/
function spb_enqueue_scripts_and_styles() {
	// enqueue progress bar styles
	wp_register_style( 'scroll-progress-styles' , plugin_dir_url( __FILE__ ) . 'lib/css/progress-bar-style.min.css' );
	wp_enqueue_style( 'scroll-progress-styles' );
	// enqueue progres bar scripts
	wp_register_script( 'scroll-progress-bar' , plugin_dir_url( __FILE__ ) . 'lib/js/progress-bar.min.js' , array( 'jquery' ) , 'all' );
	wp_enqueue_script( 'scroll-progress-bar' );
}


/* 
*	Enqueue Scripts/Styles
*	- for smooth scrolling (if enabled)
*/
function spb_smooth_scroll_scripts_and_styles() {
	$scroll_progress_options = spb_get_options();
	// enqueue our smooth scroll script from CDN
	wp_register_script( 'smooth-scroll' , plugin_dir_url( __FILE__ ) . 'lib/js/TweenMax.min.js' , array( 'jquery' ) , 'all' );
	wp_enqueue_script( 'smooth-scroll' );
	wp_register_script( 'scroll-to' , plugin_dir_url( __FILE__ ) . 'lib/js/ScrollToPlugin.min.js' , array( 'jquery' ) , 'all' );
	wp_enqueue_script( 'scroll-to' );
	// enqueue our smooth scroll script which handles the actual scrolling
	wp_register_script( 'smooth-scroll-handling' , plugin_dir_url( __FILE__ ) . 'lib/js/smooth-scroll-handle.min.js' , array( 'jquery' , 'smooth-scroll' , 'scroll-to' ) , 'all' );
	// localize data, pass to our scroll handling script
	$localized_array = array(
		'scrollTime' => $scroll_progress_options['smooth_scroll_scroll_time'],
		'scrollDistance' => $scroll_progress_options['smooth_scroll_pixel_offset']
	);	
	wp_localize_script( 'smooth-scroll-handling' , 'smooth_scroll' , $localized_array );
	wp_enqueue_script( 'smooth-scroll-handling' );
}


// Include our RGBA color picker custom field type
require_once( plugin_dir_path( __FILE__ ) . '/lib/custom-field-types/rgba-color-picker.php' );
// Include our Scroll Pgoress CMB2 Options Page
require_once( plugin_dir_path( __FILE__ ) . '/lib/scroll-progress-options.php' );


/*
*	Enjoy this free plugin :)
*	Coded by Evan Herman in Philadelphia, PA - https://www.Evan-Herman.com
*/