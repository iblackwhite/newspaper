<?php

class GN_Get_Notified_Widget extends WP_Widget {
	
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		parent::__construct(
	 		'GN_Get_Notified_Widget', // Base ID
			__( 'Get Notified Widget', 'get-notified' ), // Name
			array( 'description' => __( 'Get Notified Widget', 'get-notified' ) ) // Args
		);
		
	}
	
	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	
	public function widget( $args, $instance ) {
		
		extract( $args );//variables below extracted from $args
		
		$title = $instance['title'];
		$button = $instance['button'];
		
		$GN_FB_Connect = new GN_FB_Connect();
		$content = $GN_FB_Connect->display_fb_connect( array( 'button' => $button  ) );
		
		if( $content ){
			
			echo $before_widget;
			if ( !empty($title) ) 
				echo $before_title . apply_filters( 'widget_title', $title ) . $after_title;

			echo $content;
			
			echo $after_widget;
			
		}
		
		
	}
	
	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		
		$title 		= isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '' ;
		$button 	= isset( $instance[ 'button' ] ) ? $instance[ 'button' ] : '' ;
		
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'get-notified' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'button' ); ?>"><?php _e( 'Image URL:', 'get-notified' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'button' ); ?>" name="<?php echo $this->get_field_name( 'button' ); ?>" type="text" value="<?php echo esc_attr( $button ); ?>" />
		</p>
		
		<p><small><?php _e( '*If "Image URL" is not provided then default button will be used.', 'get-notified' ); ?></small></p>
		
		<?php
	}
	
	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['button'] = strip_tags( $new_instance['button'] );
		return $instance;
	}
	
}

?>