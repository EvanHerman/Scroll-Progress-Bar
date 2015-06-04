<?php
/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
 
 // include the bootstrap file
if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

// initialize the class file
class Scroll_Progress_Admin {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'scroll_progress_options';

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'scroll_progress_option_metabox';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	
	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'Scroll Progress', 'scroll-progress' );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'spb_init' ) );
		add_action( 'admin_menu', array( $this, 'spb_add_options_page' ) );
		add_action( 'cmb2_init', array( $this, 'spb_add_scroll_progress_options_metaboxes' ) );
		add_action( 'admin_footer_text' , array( $this, 'spb_alter_admin_footer' ) );
	}


	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function spb_init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function spb_add_options_page() {
		$this->options_page = add_submenu_page( 'options-general.php', $this->title, $this->title, 'manage_options', $this->key, array( $this, 'spb_admin_page_display' ) );
		// Include CMB CSS in the head to avoid FOUT
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function spb_admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key, array( 'cmb_styles' => false ) ); ?>
		</div>
		<?php
	}

	/**
	 *	Alter the Scroll Progress admin footer with a custom notice
	 *	@since 0.1
	 */
	function spb_alter_admin_footer() {
		// store current screen (we'll use it to grab the base)
		$screen = get_current_screen();
		// confirm that were on the 'Scroll Progress' options page, and alter the admin footer text
		if( $GLOBALS['pagenow'] == 'options-general.php' && $screen->base == 'settings_page_scroll_progress_options' ) {
			$evan_url = 'https://www.Evan-Herman.com';
			$review_repo_url = 'http://www.wordpress.org';
			$text = sprintf( wp_kses( __( '<strong>Scroll Progress</strong> proudly coded by <a href="%s" target="_blank">EH Dev Shop</a> in Philadelpha, PA.', 'scroll-progress' ), array(  'a' => array( 'href' => array() , 'target' => array() ), 'strong' => array() ) ), esc_url( $evan_url ) );
			$text .= '<br />' . sprintf( wp_kses( __( 'If you enjoy this plugin, please consider leaving us a nice <a href="%s" target="_blank">review</a>.', 'my-text-domain' ), array(  'a' => array( 'href' => array() , 'target' => array() ) ) ), esc_url( $review_repo_url ) );
			echo $text;
		}
	}
	
	
	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function spb_add_scroll_progress_options_metaboxes() {
		
		// Setup our Progress Bar Settings CMB2 Metabox
		$cmb = new_cmb2_box( array(
			'id'      => $this->metabox_id,
			'hookup'  => false,
			'show_on' => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		// Setup our Progress Bar Field Title
		$cmb->add_field( array(
			'name'    => __( 'Progress Bar Settings', 'scroll-progress' ),
			'desc'    => __( 'Adjust settings for the scroll progress bar..', 'scroll-progress' ), 
			'type'    => 'title',
		) );
		
		// Setup our Progress Bar Field - Progress Bar Color colorpicker
		$cmb->add_field( array(
			'name'    => __( 'Progress Bar Color', 'scroll-progress' ),
			'desc'    => __( 'Set the color of the progress bar.', 'scroll-progress' ),
			'id'      => 'progress_bar_colorpicker',
			'type'    => 'rgba_colorpicker',
			'default' => '#bada55',
		) );
		
		// Setup our Progress Bar Field - Progress Bar Post Types
		$cmb->add_field( array(
			'name'    => __( 'Display On', 'scroll-progress' ),
			'desc'    => '',
			'id'      => 'progress_bar_display_on_post_types',
			'type'    => 'post_type_multicheck',
		) );
		
		
		// Setup our Progress Bar Field - Progress Bar Post Types
		$cmb->add_field( array(
			'name'    => __( 'Display On', 'scroll-progress' ),
			'desc'    => __( 'Enter a comma separated list of page IDs to exclude the progress bar indicator from.' , 'scroll-progress' ),
			'id'      => 'progress_bar_exclude_from',
			'type'    => 'text',
		) );
			
		// Setup our Smooth Scroll Field - Smooth Scroll Title
		$cmb->add_field( array(
			'name'    => __( 'Smooth Scroll Settings', 'scroll-progress' ),
			'desc'    => __( 'Enqueue the necessary smooth scroll scripts across your entire site.', 'scroll-progress' ),
			'id'      => 'smooth_scroll_title',
			'type'    => 'title',
		) );
		
		// Setup our Smooth Scroll Field - Smooth Scroll Toggle
		$cmb->add_field( array(
			'name'    => __( 'Smooth Scroll', 'scroll-progress' ),
			'desc'    => __( 'Toggle on/off smooth scrolling on your site.', 'scroll-progress' ),
			'id'      => 'smooth_scroll_active',
			'type'    => 'select',
			'default' => 'true',
			'options' => array(
				'true' => 'Active',
				'false' => 'Inactive',
			),
		) );
		
		// Setup our Smooth Scroll Field - Smooth Scroll Length (time)
		$cmb->add_field( array(
			'name'    => __( 'Smooth Scroll', 'scroll-progress' ),
			'desc'    => __( 'How long should scrolling take (s)? (default: .5)', 'scroll-progress' ),
			'id'      => 'smooth_scroll_scroll_time',
			'type'    => 'text_small',
			'default' => '.5',
		) );
		
		// Setup our Smooth Scroll Field - Smooth Scroll Distance (distance)
		$cmb->add_field( array(
			'name'    => __( 'Smooth Scroll', 'scroll-progress' ),
			'desc'    => __( 'How many pixels should the page scroll (px). (default: 300)', 'scroll-progress' ),
			'id'      => 'smooth_scroll_pixel_offset',
			'type'    => 'text_small',
			'default' => '300',
		) );
			

	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

	/**
	*	Helper function to return an array of all registered post types
	*	which will be used to target pages using the scroll bar
	*	@since v0.1
	*/
	public static function scroll_progress_get_registered_post_types() {
		// store an array of registered post types
		$registered_post_types = get_post_types();
		// remove the un-needed post_types from our array
		unset( $registered_post_types['attachment'], $registered_post_types['revision'], $registered_post_types['nav_menu_item'] );
		return $registered_post_types;
	}
	
}

/**
 * Helper function to get/return the scroll_progress_Admin object
 * @since  0.1.0
 * @return scroll_progress_Admin object
 */
function scroll_progress_admin() {
	static $object = null;
	if ( is_null( $object ) ) {
		$object = new scroll_progress_Admin();
		$object->hooks();
	}

	return $object;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function scroll_progress_get_option( $key = '' ) {
	return cmb2_get_option( scroll_progress_admin()->key, $key );
}


/* 
 *	Custom Field Types
 *	- post_type_multicheck
 *	@since 0.1
*/
function cmb2_render_callback_for_post_type_multicheck( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	?>
	<!-- multicheck inline styles -->
	<style type="text/css">
		ul.cmb2-checkbox-list {
			display: inline-block;
			max-width: 800px;
		}
			ul.cmb2-checkbox-list li {
				float: left;
				margin-right: 15px;
				min-width: 175px;
			}
	</style>
	<?php
	// to check if were on a cpt page use is_singular( 'post_type' )
	// https://codex.wordpress.org/Function_Reference/is_singular#Custom_Post_Types
	// get our options
	$scroll_progress_options = spb_get_options();
	// grab our registered post types
	$registered_post_types = Scroll_Progress_Admin::scroll_progress_get_registered_post_types();
	// create a UL
	echo '<ul class="cmb2-checkbox-list cmb2-list">';
		// check if homepage is in our array of post types to display on
		if( !empty( $scroll_progress_options['progress_bar_display_on_post_types'] ) ) {
			$home_check = in_array( 'home_page' , $scroll_progress_options['progress_bar_display_on_post_types'] ) ? 'checked' : '';
		} else {
			$home_check = '';
		}
		// lets provide a field for our home page
		echo '<li><label>' . $field_type_object->input( array( 'type' => 'checkbox' , 'class' => 'cmb2-option' , 'id' => 'home' , 'name' => 'progress_bar_display_on_post_types[]' , 'value' => 'home_page' , $home_check => $home_check ) ) . 'Home Page' . '</label></li>';
		// loop over and display a checkbox foreach
		foreach( $registered_post_types as $post_type ) {
			// setup our checked attribute
			if( !empty( $scroll_progress_options['progress_bar_display_on_post_types'] ) ) {
				$checked = in_array( $post_type , $scroll_progress_options['progress_bar_display_on_post_types'] ) ? 'checked' : '';
			} else {
				$checked = '';
			}
			 echo '<li><label>' . $field_type_object->input( array( 'type' => 'checkbox' , 'class' => 'cmb2-option' , 'id' => $post_type , 'name' => 'progress_bar_display_on_post_types[]' , 'value' => $post_type , $checked => $checked ) ) . $post_type . '</label></li>';
		}
	echo '</ul>';
	// display a description
	echo '<p class="cmb2-metabox-description">' . __( 'Select which post type the scroll progress bar should be displayed on.' , 'scroll-progress' ) . '</p>';
}
add_action( 'cmb2_render_post_type_multicheck', 'cmb2_render_callback_for_post_type_multicheck', 10, 5 );


// Get it started
scroll_progress_admin();