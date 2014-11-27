<?php
/*
Alcyone slider widgets
1. Slider widget

*/

class alcyoneslider_widget extends WP_Widget {

	function alcyoneslider_widget() {
			$widget_ops = array('classname' => 'alcyoneslider_widget', 'description' => __( 'Alcyone slider') );
			$this->WP_Widget('alcyoneslider_widget', __('Alcyone slider'), $widget_ops);
	}
	
	
	function widget( $args, $instance ) {
			extract( $args );			
			$sliderID = $instance['slider_ID'];	
			
			echo $before_widget;			
			echo $before_title.$instance['title'].$after_title; 			
			echo do_shortcode('[alcyone_slider id="'.$sliderID.'"]');									
			echo $after_widget;	
		}
		
	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['slider_ID'] = strip_tags($new_instance['slider_ID']);						
			return $instance;
	}
	
	function form( $instance ) {
			//Defaults
				$instance = wp_parse_args( (array) $instance, array( 
						'title' => '', 
						'postlink_title' => 'More',						
						'postlink' => false ));
				$title = esc_attr( $instance['title'] );
				$slider_ID = esc_attr( $instance['slider_ID'] );						
	
	?>  
				<p class="<?php echo $this->get_field_id('title'); ?>" ><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('slider_ID'); ?>">Slider:</label>
					<select id="<?php echo $this->get_field_id('slider_ID'); ?>" name="<?php echo $this->get_field_name('slider_ID'); ?>" >
						<option value="">
						<?php echo esc_attr( __( 'Select slider' ) ); ?></option> 
						 <?php 
						  $sliders = get_posts("post_type=alcyoneslider&posts_per_page=-1"); 						  
						  foreach ( $sliders as $slider ) {
								$custom = get_post_custom($slider->ID);
								$banner_height = $custom['height'][0];	
								$banner_width = $custom['width'][0];
							$option = '<option value="'.$slider->ID.'" ';
								if ($slider_ID == $slider->ID){
									$option .= ' selected="selected" ';
								}
							$option .= '">';
							$option .= $slider->post_title." - ".$banner_width."px/".$banner_height."px";
							$option .= '</option>';
							echo $option;
						  }
						 ?>
					</select>	
					<br/>To edit sliders click <a href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=alcyoneslider'; ?>">here</a>					
			</p>
		<?php		
	}

}
		
function alcyoneslider_widgets_init() {
	register_widget('alcyoneslider_widget');
}
add_action('widgets_init', 'alcyoneslider_widgets_init');
?>