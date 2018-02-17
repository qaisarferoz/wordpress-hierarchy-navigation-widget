<?php
/**
 * Handles all Widget class extensions
 *
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );


if(!class_exists('WPHN_Page_Widget'))
{
	class WPHN_Page_Widget extends WP_Widget
	{

		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			$description = __('Shows page ancestory beneath the current top level page as a list of links.', 'wordpress_hierarchy_navigation');

			parent::__construct(
				'wphn_page_widget', // Base ID
				__( 'WPHN Page Hierarchy', 'text_domain' ), // Name
				array( 'classname' => 'wphn-page-widget widget_wphn_page_widget', 'description' => $description ) // Args
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		function widget($args, $instance) {

			extract( $args );

			$hierarchy = new WPHN_Hierarchy($instance);

			if (!$hierarchy) {
				return;
			}

			$children = $hierarchy->get_posts();

			// If there are no pages in this section we are done
			if( !$children ) {
				return false;
			}

			echo $before_widget;

			if ( $instance['top_page_as_title'] ) {
				// Get the hierarchy parent
				$parent = $hierarchy->get_parent();
				$title = apply_filters( 'widget_title', $parent->post_title );
			}else{
				$title = apply_filters( 'widget_title', $instance['title'] );
			}

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			echo $children;

			echo $after_widget;
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		function update($new_instance, $old_instance) {
			$instance = array();
			$instance['title'] = trim( strip_tags( $new_instance['title'] ) ); //sets widget title
			$instance['show_all'] = isset( $new_instance['show_all'] ) ? true : false; //shows all childern
			$instance['exclude'] = str_replace( " ", "", strip_tags($new_instance['exclude']) ); //remove spaces from list
			$instance['top_page_as_title'] = isset( $new_instance['top_page_as_title'] ) ? true : false; // hide nav title option
			$instance['show_top_page'] = isset( $new_instance['show_top_page'] ) ? true : false; // hide nav title option
			$instance['show_top_level'] = isset( $new_instance['show_top_level'] ) ? true : false; // hide nav title option
			$instance['featured'] = trim( strip_tags( $new_instance['featured'] ) ); //sets featured image size
			$instance['sort_by'] = ( in_array( $new_instance['sort_by'], array( 'post_title', 'menu_order', 'ID' ) ) ) ? $new_instance['sort_by'] : 'menu_order';

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		function form($instance) {
			//Defaults
			$instance = wp_parse_args(
				(array) $instance,
				array(
						'title' => '',
						'sort_by' => 'menu_order',
						'exclude' => '',
						'top_page_as_title' => false,
						'show_top_page' => false,
						'show_top_level' => false,
						'show_all' => false,
						'featured' => 'none',
					)
				);
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" size="7" class="widefat" /><br />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('sort_by'); ?>"><?php _e('Sort pages by:'); ?></label>
				<select name="<?php echo $this->get_field_name('sort_by'); ?>" id="<?php echo $this->get_field_id('sort_by'); ?>" class="widefat">
					<option value="menu_order"<?php selected( $instance['sort_by'], 'menu_order' ); ?>><?php _e('Menu Order'); ?></option>
					<option value="post_title"<?php selected( $instance['sort_by'], 'post_title' ); ?>><?php _e('Title'); ?></option>
					<option value="date_published"<?php selected( $instance['sort_by'], 'date_published' ); ?>><?php _e('Date Published'); ?></option>
					<option value="ID"<?php selected( $instance['sort_by'], 'ID' ); ?>><?php _e( 'ID' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude:'); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" value="<?php echo esc_attr($instance['exclude']); ?>" size="7" class="widefat" /><br />
				<small>Page IDs, separated by commas.</small>
			</p>
			<p>
				<input class="checkbox" type="checkbox" <?php checked($instance['top_page_as_title']); ?> id="<?php echo $this->get_field_id('top_page_as_title'); ?>" name="<?php echo $this->get_field_name('top_page_as_title'); ?>" />
				<label for="<?php echo $this->get_field_id('top_page_as_title'); ?>"><?php _e('Use top page as title'); ?></label><br />
				<input class="checkbox" type="checkbox" <?php checked($instance['show_top_page']); ?> id="<?php echo $this->get_field_id('show_top_page'); ?>" name="<?php echo $this->get_field_name('show_top_page'); ?>" />
				<label for="<?php echo $this->get_field_id('show_top_page'); ?>"><?php _e('Show top level page'); ?></label><br />
				<input class="checkbox" type="checkbox" <?php checked($instance['show_top_level']); ?> id="<?php echo $this->get_field_id('show_top_level'); ?>" name="<?php echo $this->get_field_name('show_top_level'); ?>" />
				<label for="<?php echo $this->get_field_id('show_top_level'); ?>"><?php _e('Show all top level pages'); ?></label><br />
				<input class="checkbox" type="checkbox" <?php checked($instance['show_all']); ?> id="<?php echo $this->get_field_id('show_all'); ?>" name="<?php echo $this->get_field_name('show_all'); ?>" />
				<label for="<?php echo $this->get_field_id('show_all'); ?>"><?php _e('Show all pages in menu'); ?></label><br />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('featured'); ?>"><?php _e('Featured Image:'); ?></label>
				<select name="<?php echo $this->get_field_name('featured'); ?>" id="<?php echo $this->get_field_id('featured'); ?>" class="widefat">
					<option value="none"<?php selected( $instance['featured'], 'none' ); ?>><?php _e('none'); ?></option>
					<?php
					$img_sizes = get_intermediate_image_sizes();
					foreach( $img_sizes as $img_size ):
					?>
					<option value="<?php echo $img_size; ?>"<?php selected( $instance['featured'], $img_size ); ?>><?php echo $img_size; ?></option>
				<?php endforeach; ?>
				</select>
			</p>
		<?php
		}

		/**
		* Additional Widget Functions
		*/

	} // End WPHN_Page_Widget class
} // End if class exists

?>
