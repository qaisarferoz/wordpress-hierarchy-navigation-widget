<?php
/**
 * Handles all Walker class extensions
 *
 */

 // Don't load directly
 if ( !defined( 'ABSPATH' ) ) die( '-1' );


 if(!class_exists('WPHN_Walker_Page'))
 {
	//Extend Walker Class to add featured image display function
	class WPHN_Walker_Page extends Walker_Page {

		function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0) {
      if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
  			$t = "\t";
  			$n = "\n";
  		} else {
  			$t = '';
  			$n = '';
  		}
  		if ( $depth ) {
  			$indent = str_repeat( $t, $depth );
  		} else {
  			$indent = '';
  		}

			$css_class = array( 'page_item', 'page-item-' . $page->ID );

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				$css_class[] = 'page_item_has_children';
			}

			if ( ! empty( $current_page ) ) {
				$_current_page = get_post( $current_page );
				if ( $_current_page && in_array( $page->ID, $_current_page->ancestors ) ) {
					$css_class[] = 'current_page_ancestor';
				}
				if ( $page->ID == $current_page ) {
					$css_class[] = 'current_page_item';
				} elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
					$css_class[] = 'current_page_parent';
				}
			} elseif ( $page->ID == get_option('page_for_posts') ) {
				$css_class[] = 'current_page_parent';
			}

			$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

			if ( '' === $page->post_title ) {
				/* translators: %d: ID of a post */
				$page->post_title = sprintf( __( '#%d (no title)' ), $page->ID );
			}

			$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
			$args['link_after'] = empty( $args['link_after'] ) ? '' : $args['link_after'];

			$output .= $indent . sprintf(
				'<li class="%s"><a href="%s">%s%s%s</a>',
				$css_classes,
				get_permalink( $page->ID ),
				$args['link_before'],
				get_the_post_thumbnail($page->ID, $args['walker_arg']),
				/** This filter is documented in wp-includes/post-template.php */
				apply_filters( 'the_title', $page->post_title, $page->ID ),
				$args['link_after']
			);

			if ( ! empty( $args['show_date'] ) ) {
				if ( 'modified' == $args['show_date'] ) {
					$time = $page->post_modified;
				} else {
					$time = $page->post_date;
				}

				$date_format = empty( $args['date_format'] ) ? '' : $args['date_format'];
				$output .= " " . mysql2date( $date_format, $time );
			}

		}

	} // End WPHN_Walker_Page Class
} // End If Class exists

?>
