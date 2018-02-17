<?php
/**
* Plugin Name: WordPress Hierarchy Navigation
* Plugin URI: http://www.akaflyingfox.com
* Description: Adds a <strong>shortcode</strong> and <strong>widget</strong> for heirarchical based page navigation. The <strong>title of the widget is the top level page</strong> within the current page hierarchy. Shows all page siblings (except on the top level page), all parents and grandparents (and higher), the siblings of all parents and grandparents (up to top level page), and any immediate children of the current page. Can also be called by a function inside template files. May <strong>exclude any pages or sections</strong>. Uses standard WordPress navigation classes for easy styling.
* Version: 1.1
* Author: Joe Tercero
* Author URI: http://www.akaflyingfox.com
*
* Plugin: Copyright 2017 AKAFlyingFox
*
*  This program is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  You should have received a copy of the GNU General Public License
*  along with this program; if not, write to the Free Software
*  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined('WPHN_THEME_DIR') )
		define('WPHN_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());
if ( !defined('WPHN_PLUGIN_DIR') )
	define('WPHN_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
if ( !defined('WPHN_PLUGIN_URL') )
	define('WPHN_PLUGIN_URL', plugin_dir_url( __FILE__ ));
if ( !defined('WPHN_BASE_FILE') )
		define('WPHN_BASE_FILE', basename( __FILE__ ));
if ( ! defined( 'WPHN_BASE_DIR' ) )
		define( 'WPHN_BASE_DIR', dirname( __FILE__ ) );
if ( !defined('WPHN_VERSION_KEY') )
		define('WPHN_VERSION_KEY', 'wordpress_hierarchy_navigation_version');
if ( !defined('WPHN_VERSION_NUM') )
		define('WPHN_VERSION_NUM', '1.1');

if(!class_exists('WordPress_Hierarchy_Nav'))
{
	class WordPress_Hierarchy_Nav
	{

	/**
	* Construct the plugin object
	*/
	public function __construct()
	{
		// Initialize Plugin Functions

		// Get Shortcodes Class
		require_once(sprintf("%s/core/shortcodes.class.php", WPHN_BASE_DIR));
		$WPHN_Shortcodes = new WPHN_Shortcodes();

		// Hierarchy Class
		require_once(sprintf("%s/core/hierarchy.class.php", WPHN_BASE_DIR));

		// Walker Class
		require_once(sprintf("%s/core/walkers.class.php", WPHN_BASE_DIR));

		// Widgets Class
		require_once(sprintf("%s/core/widgets.class.php", WPHN_BASE_DIR));

	} // END public function __construct

	/**
	* Activate the plugin
	*/
	public static function activate()
	{
		if( get_option(WPHN_VERSION_KEY) !== WPHN_VERSION_NUM ) {

			update_option( WPHN_VERSION_KEY, WPHN_VERSION_NUM );

		}

	} // END public static function activate

	/**
	* Deactivate the plugin
	*/
	public static function deactivate()
	{
		// Do nothing
	} // END public static function deactivate

	} // END class WordPress_Hierarchy_Nav
} // END if(!class_exists('WordPress_Hierarchy_Nav'))

if(class_exists('WordPress_Hierarchy_Nav'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WordPress_Hierarchy_Nav', 'activate'));
	register_deactivation_hook(__FILE__, array('WordPress_Hierarchy_Nav', 'deactivate'));

	// instantiate the plugin class
	$WordPress_Hierarchy_Nav = new WordPress_Hierarchy_Nav();

	if(isset($WordPress_Hierarchy_Nav))
	{
		/**
		 * Register Widgets
		*/
		function register_wphn_widgets() {

			register_widget( 'WPHN_Page_Widget' );

		}
		add_action( 'widgets_init', 'register_wphn_widgets' );

		/**
		 * Plugin Functions
		*/

		/**
		 * Display hierarchy navigation
		 *
		 * @param array|string $args Optional. Override default arguments.
		 * @return string HTML content, if not displaying.
		 */
		function the_wphn_page_hierarchy_widget( $args = array() ) {

			$instance = wp_parse_args($args, array(
				'title' => '',
				'sort_by' => 'menu_order',
				'exclude' => '',
				'top_page_as_title' => false,
				'show_top_page' => false,
				'show_top_level' => false,
				'show_all' => false,
				'featured' => 'none',
				'before_widget' => '<nav>',
				'after_widget' => '</nav>',
				'before_title' => '<h2 class="widget-title">',
				'after_title' => '</h2>',
			)); // defaults

			the_widget(
				'WPHN_Page_Widget',
				$instance,
				array(
					'before_widget' => $instance['before_widget'],
					'after_widget' => $instance['after_widget'],
					'before_title' => $instance['before_title'],
					'after_title' => $instance['after_title'],
				)
				);

		} // End the_wphn_page_hierarchy

	} // End is plugin class set

}


?>
