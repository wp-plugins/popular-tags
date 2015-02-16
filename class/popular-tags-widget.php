<?php
/**
 * Adds Popular_Tags_Widget widget.
 */
class Popular_Tags_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Popular_Tags_Widget', // Base ID
			__( 'Popular Tags by Visit', 'text_domain' ), // Name
			array( 'description' => __( 'Show popular tags by user visit', 'text_domain' ), ) // Args
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
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		if ( false === ( $tag_ids = get_transient( 'ec_popular_tag_transient' ) ) ) {
		    $popular_for = $instance['popular_for'];
			$tag_count = $instance['tag_count'];
			
			global $wpdb;

			$table_name = $wpdb->prefix . 'tags_visit';

			$time = strtotime( $popular_for );
			$date = date('Y-m-d H:i:s', $time);

			$sql = $wpdb->prepare( 
				"
	            SELECT tag_id, count(*) as count 
	            FROM $table_name
	            WHERE visit > %s
	            GROUP BY tag_id
	            ORDER BY count DESC
				LIMIT %d
				",
		        $date,
		        $tag_count
	        );

	        $tag_ids = $wpdb->get_results( $sql );

	        set_transient( 'ec_popular_tag_transient', $tag_ids, HOUR_IN_SECONDS );
		}

	    if ( count( $tag_ids ) > 0 ) {
	    	echo '<ul class="menu">';
	    	foreach ($tag_ids as $key => $tag) {
	    		$term = get_term( $tag->tag_id, 'post_tag' );
	    		echo '<li><a title="'.$term->name.'" href="'.get_term_link( $term, 'post_tag' ).'">'.$term->name.'</a></li>';
	    	}
	    	echo '</ul>';
	    }

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Popular Tags', 'text_domain' );
		$popular_for = ! empty( $instance['popular_for'] ) ? $instance['popular_for'] : __( '-1 week', 'text_domain' );
		$tag_count = ! empty( $instance['tag_count'] ) ? $instance['tag_count'] : __( '10', 'text_domain' );

		$options = array(
			'-1 week' => '1 Week',
			'-1 month' => '1 Month',
			'-2 months' => '2 Months',
			'-6 months' => '6 Months',
			'-1 year' => '1 Year'
		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'popular_for' ); ?>"><?php _e( 'Popular For:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'popular_for' ); ?>" name="<?php echo $this->get_field_name( 'popular_for' ); ?>">
				<?php foreach ($options as $key => $option) { 
					$selected = ( $key == esc_attr( $popular_for ) ) ? 'selected="selected"' : '';
					?>
					<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $option; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'tag_count' ); ?>"><?php _e( 'Number of Tags to be displayed:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'tag_count' ); ?>" name="<?php echo $this->get_field_name( 'tag_count' ); ?>" type="number" value="<?php echo esc_attr( $tag_count ); ?>">
		</p>
		<?php 
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
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['popular_for'] = ( ! empty( $new_instance['popular_for'] ) ) ? strip_tags( $new_instance['popular_for'] ) : '';
		$instance['tag_count'] = ( ! empty( $new_instance['tag_count'] ) ) ? strip_tags( $new_instance['tag_count'] ) : '';

		return $instance;
	}

} // class Popular_Tags_Widget