<?php
/**
 * Handles Plugin Shortcodes
 *
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );


if ( !class_exists( 'WPHN_Shortcodes' ) ) {
	class WPHN_Shortcodes {

		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register shortcodes
			add_shortcode( 'wphn_page_menu', array(&$this, 'page_menu_shortcode_callback' ));
		} // END public function __construct


		//Returns Track List, styles amd scripts
		public static function page_menu_shortcode_callback( $atts ) {

			$a = shortcode_atts( array(
				'title' => '',
				'sort_by' => 'menu_order',
				'exclude' => '',
				'top_page_as_title' => false,
				'show_top_page' => false,
				'show_top_level' => false,
				'show_all' => false,
				'featured' => 'none',
				'before_shortcode'=>'<nav>',
				'after_shortcode'=>'</nav>',
				'before_title'=>'<h2 class="shortcode-title">',
				'after_title'=>'</h2>',
				), $atts );

				$hierarchy = new WPHN_Heirarchy($a);

				if (!$hierarchy) {
					return;
				}

				$children = $hierarchy->get_posts();

				// If there are no pages in this section we are done
				if( !$children ) {
					return false;
				}

				$output = '';

				$output .= $a['before_shortcode'];

				if ( $a['top_page_as_title'] ) {
					// Get the hierarchy parent
					$parent = $hierarchy->get_parent();
					$title = apply_filters( 'widget_title', $parent->post_title );
				}else{
					$title = apply_filters( 'widget_title', $a['title'] );
				}

				if ( $title ) {
					$output .= $a['before_title'] . $title . $a['after_title'];
				}

				$output .= $children;

				$output .= $a['after_shortcode'];

			return $output;
		}

	} // End Class WPHN_Shortcodes

} // End is class exists

?>
