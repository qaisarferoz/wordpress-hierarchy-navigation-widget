<?php
/**
 * Handles Core Plugin Functions
 *
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );


if ( !class_exists( 'WPHN_Hierarchy' ) ) {
	class WPHN_Hierarchy {

		private $_post = null;
		private $_ancestors = null;
		private $_args = [];

		/**
		 * Construct the plugin object
		 */
		public function __construct($args)
		{
			global $post;
			$current_post = $this->has_hierarchy($post);

			if ( !$post ) {
				return false;
			}

			$this->set_post($current_post);
			$this->set_ancestors($current_post);
			$this->set_args($args);

		} // END public function __construct

		/**
		 * Sets the post object
		 */
		private function set_post($post) {
			$this->_post = $post;
		}

		/**
		 * Sets the post ancestor array
		 */
		private function set_ancestors($post) {
			$this->_ancestors = isset($post->ancestors) ? $post->ancestors : get_post_ancestors($post);
		}

		/**
		 * Sets the args array
		 */
		private function set_args($args) {

			$keys = array(
				'title',
				'sort_by',
				'exclude',
				'top_page_as_title',
				'show_top_page',
				'show_top_level',
				'show_all',
				'featured',

			);

			foreach ( $keys as $key ) {
				if ( !isset($this->_args[$key]) )
					$this->_args[$key] = '';
			}

			if ( is_array($args) ) {
				$this->_args = $this->parse_args(wp_parse_args($args, $this->_args));
			}
		}

		/**
		 * Parses the args array
		 */
		private function parse_args($args) {

			if ( empty( $args ) || !is_array($args) ) {
				return;
			}

			$new_args = [];

			// Hierarchy title
			$new_args['title'] = trim( strip_tags( $args['title'] ) );

			// Show all childern
			$new_args['show_all'] = !empty( $args['show_all'] ) ? true : false;

			// List of Excluded Posts
			$new_args['exclude'] = !empty( $args['exclude'] ) ? explode(',', str_replace( " ", "", strip_tags($args['exclude']) ) ) : array();

			// Use to Top Ancestor as Title
			$new_args['top_page_as_title'] = !empty( $args['top_page_as_title'] ) ? true : false;

			// Include the Top Ancestor as a Link
			$new_args['show_top_page'] = !empty( $args['show_top_page'] ) ? true : false;

			// Include the Top Ancestor Siblings
			$new_args['show_top_level'] = !empty( $args['show_top_level'] ) ? true : false;

			// Include a Featured Image with designated size
			$new_args['featured'] = trim( strip_tags( $args['featured'] ) );

			// Change the Sort Order of the Posts
			$new_args['sort_by'] = !empty( $args['sort_by'] ) ? $args['sort_by'] : 'menu_order';

			return $new_args;

		}

		/**
		 * Check if current post is an excluded id
		 */
		private function has_hierarchy($post) {

			// Doesn't apply to search or 404 page
			if ( is_search() || is_404() ) {
				return false;
			}

			// Treat the non-heirarchical posts page as the current page if applicable
			if ( !is_post_type_hierarchical( $post->post_type ) ) {
				if ( !is_page() && get_option( 'page_for_posts' ) ){
					return get_page( get_option( 'page_for_posts' ) );
				}else {
					return false;
				}
			}

			return $post;

		}

		/**
		 * Check if current post is an excluded id
		 */
		private function is_excluded() {
			// If on excluded page or child or excluded page
			if ( in_array( $this->_post->ID, $this->_args['exclude'] ) || !empty(array_intersect($this->_args['exclude'], $this->_ancestors)) ) {
				return true;
			}

			return false;
		}

		/**
		 * Returns the top parent of the post
		 */
		private function get_the_parent() {
			return !empty($this->_ancestors) ? get_page(end($this->_ancestors)) : $this->_post;
		}

		/**
		 * Returns the depth array of the hierarchy
		 */
		private function get_depth() {

			if( !$this->_args['show_all'] ) {
				// Prevents improper grandchildren from showing
				return count($this->_ancestors) + 1;
			}

			return 0;
		}

		/**
		 * Returns the exlcuded array of the hierarchy
		 */
		private function get_excluded() {

			if( !$this->_args['show_all'] && !empty($this->_ancestors) ) {
				return $this->filter_direct_hierarchy($this->_ancestors, $this->_args['exclude']);
			}

			return $this->_args['exclude'];
		}

		/**
		 * Returns the top parent of the post
		 */
		private function get_children($parent) {
			$depth = $this->get_depth();
			$excluded = $this->get_excluded();

			// Get the list of pages, including only those in our page list
      $args = array(
        'title_li' => '',
        'echo' => 0,
        'depth' => $depth,
        'child_of' => $this->_args['show_top_level'] ? $parent->parent_id : $parent->ID,
        'sort_column' => $this->_args['sort_by'],
        'exclude' => implode(',', $excluded),
        'walker' => $this->_args['featured'] !== 'none' ? new WPHN_Walker_Page() : '',
        'walker_arg' => $this->_args['featured']
      );

    	return wp_list_pages($args);
		}

		/**
		 * Excludes all posts outside the current direct hierarchy
		 */
		private function filter_direct_hierarchy($ancestors = array(), $excluded = array()) {

      $excluded_hierarchy = array();
			$current_hierarchy = $ancestors;
			array_push($current_hierarchy, $this->_post->ID);

			// Exclude pages not in direct hierarchy
			foreach ($current_hierarchy as $hierarchy_id)
			{
				$children = get_pages(array(
					'child_of' => $hierarchy_id,
					'exclude' => $current_hierarchy,
					'post_type' => $this->_post->post_type
				) );

				foreach ($children as $child) {
					$excluded_children = get_pages(array(
						'child_of' => $child->ID,
						'post_type' => $this->_post->post_type
					));
					foreach ($excluded_children as $excluded_page) {
						$excluded_hierarchy[] = $excluded_page->ID;
					}
				}
			}

			return array_merge($excluded, $excluded_hierarchy);
		}

		/**
		 * Outputs the posts as HTML
		 */
		private function output_post_string() {

			if( $this->is_excluded() ) {
				return;
			}

			$parent = $this->get_parent();
			$children = $this->get_children($parent);

			$output = '';

			$output .= "<ul>";

      if ( $this->_args['show_top_page'] && !$this->_args['show_top_level'] ) {
        $parent_class = ( $this->_post->ID === $parent->ID ) ? "current_page_item" : "current_page_ancestor";

        if ( $this->_post->post_parent === $parent->ID ) {
          $parent_class .= " current_page_parent";
        }

        $parent_title = '<a href="' . get_page_link($parent->ID) . '">' . apply_filters( 'the_title',  $parent->post_title ). '</a>';

        $output .= '<li class="' . $parent_class . '">';

        $output .= apply_filters( 'wphn_top_page_title', $parent_title );

        $output .= '<ul>';

        $output .= apply_filters( 'wphn_page_list', $children );

        $output .= '</ul></li>';

      }else{

        $output .= apply_filters( 'wphn_page_list', $children );

      }

      $output .= "</ul>";

			return $output;
		}

		public function get_parent() {
			return $this->get_the_parent();
		}

		public function get_posts() {
			return $this->output_post_string();
		}

		public function the_posts() {
			echo $this->output_post_string();
		}

	} // End Class WPHN_Hierarchy

} // End if class_exists

?>
