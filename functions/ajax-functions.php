<?php
/* Ajax Functions *************************************************************************************/


/* Create New Slider ***********************************************************************/
add_action('wp_ajax_addNewSlider', 'addNewSlider');
add_action('wp_ajax_nopriv_addNewSlider', 'addNewSlider');

function addNewSlider() {

	$height = $_POST['height'];	
	$width = $_POST['width'];	
	// Create post object
	$my_post = array(
	  'post_title'    => 'New Slider',			  
	  'post_status'   => 'publish',
	  'post_type'   => 'alcyoneslider'
	);
	// Insert the post into the database
	$new_post_id = wp_insert_post( $my_post , $wp_error);
	$slider_transition = 2;	
	$slider_duration = 4;	
	$slider_transition_type = "fade";	
	
	update_post_meta($new_post_id, 'height', $height);	
	update_post_meta($new_post_id, 'width', $width);	
	update_post_meta($new_post_id, 'slider_transition', $slider_transition);	
	update_post_meta($new_post_id, 'slider_duration', $slider_duration);	
	update_post_meta($new_post_id, 'slider_transition_type', $slider_transition_type);	
	echo $new_post_id;

die();
}
/*************************************************************************************/
/* Update slider /serialize/ elements ****************************************************/
add_action('wp_ajax_update_alcyone_slider', ('alcyone_slider_update'));		
add_action('wp_ajax_nopriv_update_alcyone_slider', 'alcyone_slider_update');	

function alcyone_slider_update() {
		
	$active_list_url = $_POST['active'];
	$inactive_list_url = $_POST['inactive'];
	$post_id= $_POST['post_id'];

	if (!is_array($active_list_url) && !empty($active_list_url) ) { echo "error empty array"; die(); }
	if (!is_array($inactive_list_url) && !empty($inactive_list_url) ) { echo "error empty array"; die(); }

	update_post_meta($post_id, 'available_headers', $inactive_list_url);
	update_post_meta($post_id, 'active_headers', $active_list_url);		
	$attributes['id']=$post_id;
	alcyone_slider_draw($attributes);
	
	die();
}
/*************************************************************************************/
// function for AJAX to set settings *************
add_action('wp_ajax_deleteSlide', 'deleteSlide');
add_action('wp_ajax_nopriv_deleteSlide', 'deleteSlide');
function deleteSlide() {

	$slide_id = $_POST['slide_id'];	
	$sliderID = $_POST['post_id'];
	$slides = get_post_meta( $sliderID, 'slides' ,true );
	$custom = get_post_custom($sliderID);
	$captions = get_post_meta($sliderID, $slide_id.'_captions');
	$captions = (int)$captions[0];
	
	delete_post_meta($sliderID, $slide_id );	
	update_post_meta($sliderID, 'slides', $slides-1 );		

	$i=0;
	$loop = true;
	$loop_i = 1;
	while ($loop) {
	//for ($i=0;$i<10;$i++){
		$i++;  
		if ($custom[$slide_id.'_caption_'.$i][0]) {			
			$caption_box_id = $slide_id."_caption_".$i;
			delete_post_meta($sliderID, $caption_box_id.'_custom_text');
			delete_post_meta($sliderID, $caption_box_id.'_pos_x');
			delete_post_meta($sliderID, $caption_box_id.'_pos_y');
			delete_post_meta($sliderID, $caption_box_id.'_element_width');
			delete_post_meta($sliderID, $caption_box_id.'_element_height');
			delete_post_meta($sliderID, $caption_box_id.'_font_color');
			delete_post_meta($sliderID, $caption_box_id.'_bg_color');
			delete_post_meta($sliderID, $caption_box_id.'_opacity');
			delete_post_meta($sliderID, $caption_box_id );	
			update_post_meta($sliderID, $slide_id.'_captions', $captions-1 );
			//$slide_id = 'alcyone_slide_'.$i;
			$loop_i++;			
		}	
		if ($loop_i > $captions) {
			delete_post_meta($sliderID, $slide_id.'_captions');
			break;
		}
		
	}
	//delete_post_meta($sliderID, $slide_id.'_captions' );	

die();
}
/*************************************************************************************/
// function for AJAX to set settings *************
add_action('wp_ajax_updateSliderTitle', 'updateSliderTitle');
add_action('wp_ajax_nopriv_updateSliderTitle', 'updateSliderTitle');
function updateSliderTitle() {

	$my_post = array();
	$my_post['ID'] = $_POST['post_id'];	
	$my_post['post_title'] = $_POST['title'];
	
	wp_update_post($my_post);		

die();
}
/*************************************************************************************/


// function for AJAX to set settings *************
add_action('wp_ajax_updateSliderSettings', 'updateSliderSettings');
add_action('wp_ajax_nopriv_updateSliderSettings', 'updateSliderSettings');
function updateSliderSettings() {

	$sliderID = $_POST['sliderID'];	
	$slider_transition = $_POST['slider_transition'];	
	$slider_duration = $_POST['slider_duration'];	
	$slider_transition_type = $_POST['slider_transition_type'];	
	
	$pause_on_hover = $_POST['pause_on_hover'];	
	$autoplay = $_POST['autoplay'];	
	$stop_on_action = $_POST['stop_on_action'];	
	$navigation_skin = $_POST['navigation_skin'];	
	$navigation_dots = $_POST['navigation_dots'];	
	$navigation_arrows = $_POST['navigation_arrows'];	
	$auto_hide_arrows = $_POST['auto_hide_arrows'];	
	$auto_hide_dots = $_POST['auto_hide_dots'];	
	
	/*$my_post = array();
	$my_post['ID'] = $sliderID;	
	$my_post['post_title'] = $_POST['slider_name'];
	
	wp_update_post($my_post);	*/
	update_post_meta($sliderID, 'slider_transition', $slider_transition);	
	update_post_meta($sliderID, 'slider_duration', $slider_duration);	
	update_post_meta($sliderID, 'slider_transition_type', $slider_transition_type);	
	update_post_meta($sliderID, 'pause_on_hover', $pause_on_hover);	
	update_post_meta($sliderID, 'autoplay', $autoplay);	
	update_post_meta($sliderID, 'stop_on_action', $stop_on_action);	
	update_post_meta($sliderID, 'navigation_skin', $navigation_skin);	
	update_post_meta($sliderID, 'navigation_dots', $navigation_dots);	
	update_post_meta($sliderID, 'navigation_arrows', $navigation_arrows);	
	update_post_meta($sliderID, 'auto_hide_arrows', $auto_hide_arrows);	
	update_post_meta($sliderID, 'auto_hide_dots', $auto_hide_dots);	

	//$attributes['id']=$sliderID;
	//alcyone_slider_draw($attributes);

die();
}
/**********************************************************************************************************************************************/

/* Admin preview slider ********************************************************************************************/
add_action('wp_ajax_previewAlcyoneSlider', 'previewAlcyoneSlider');
add_action('wp_ajax_nopriv_previewAlcyoneSlider', 'previewAlcyoneSlider');

function previewAlcyoneSlider($banner_id) {
	$sliderID = $_POST['sliderID'];	
	echo do_shortcode('[alcyone_slider id="'.$sliderID.'"]');
	
	die();
}
	
/****************************************************************************************************************************/
add_action('wp_ajax_editSlide', 'editSlide');
add_action('wp_ajax_nopriv_editSlide', 'editSlide');

function editSlide() {
		$sliderID = $_POST['sliderID'];
		$slide_id = $_POST['slide_id'];		
		$custom = get_post_custom($sliderID);
		$image_id = $custom[$slide_id][0];						
		$url= wp_get_attachment_url($image_id);
		
	    $banner_height = $custom['height'][0];	
	    $banner_width = $custom['width'][0];					
		?>
		<script language="JavaScript" type="text/JavaScript">	
				jQuery(function() {
					  jQuery.fn.elementDraggable= function elementDraggable() {
						this.each(function() {
							jQuery(this).draggable(  {	
								containment: "parent",
								drag: function () {
									caption_box_id  =  jQuery(this).find(".caption_box_id").val();
									l = jQuery(this).position();
									t = jQuery(this).position();
									jQuery("#caption_"+caption_box_id+" .pos_x").val(l.left);
									jQuery("#caption_"+caption_box_id+" .pos_y").val(t.top);
									jQuery(".caption_content > div").removeClass('active_caption_content');
									jQuery("#caption_nav li a").removeClass('active_caption_nav');
									jQuery("#caption_"+caption_box_id).addClass('active_caption_content') ;						
									jQuery('#caption_nav li a[href="'+caption_box_id+'"]').addClass('active_caption_nav') ;		
								}					
							});
						});
						return this;
					  };
					  jQuery.fn.elementResizable = function elementResizable() {
						this.each(function() {
							jQuery(this).resizable({
								containment: "parent",
								handles: {											
									'se': '#segrip',											
								},
								resize: function( event, ui ) {
									caption_box_id  =  jQuery(this).find(".caption_box_id").val();
									jQuery("#caption_"+caption_box_id+" .element_width").val(ui.size.width);
									jQuery("#caption_"+caption_box_id+" .element_height").val(ui.size.height);
									jQuery(".caption_content > div").removeClass('active_caption_content');
									jQuery("#caption_nav li a").removeClass('active_caption_nav');
									jQuery("#caption_"+caption_box_id).addClass('active_caption_content') ;						
									jQuery('#caption_nav li a[href="'+caption_box_id+'"]').addClass('active_caption_nav') ;
								}
							});					
						});
						return this;
					  };
					  
					  jQuery.fn.elementColorpickerBG= function elementColorpickerBG() {
						this.each(function() {
							caption_box_id  =  jQuery(this).parent().parent().find(".caption_box_id").val();						
							jQuery(this).farbtastic("#caption_"+caption_box_id+" .bg_color").mouseup(function (){
								inner_caption_box_id  =  jQuery(this).parent().parent().find(".caption_box_id").val();
								jQuery("#elementResizable_"+inner_caption_box_id+" .bg_color").css("background-color",jQuery.farbtastic(this).color);      
							});						
						});
						return this;
					  };
					  
					  jQuery.fn.elementColorpickerFont = function elementColorpickerFont() {
						this.each(function() {
							caption_box_id  =  jQuery(this).parent().parent().find(".caption_box_id").val();
							jQuery(this).farbtastic("#caption_"+caption_box_id+" .font_color").mouseup(function (){
								inner_caption_box_id  =  jQuery(this).parent().parent().find(".caption_box_id").val();
								jQuery("#elementResizable_"+inner_caption_box_id+" .content").css("color",jQuery.farbtastic(this).color);      
							});							
						});
						return this;
					  };
					});
			
				jQuery(document).ready(function($) {
					$('.colorpicker_bg').hide();
					$('.color_boxes .colorpicker_bg').elementColorpickerBG();
					$('.colorpicker_font').hide();					
					$('.color_boxes .colorpicker_font').elementColorpickerFont();
					
					
					$('.caption_boxes .bg_color').live('click', function() {
						$(this).parent().parent().find('.colorpicker_bg').fadeIn();								
					});
					$(document).mousedown(function() {
						$('.caption_boxes .bg_color').each(function() {
							var display = $(this).parent().find('.colorpicker_bg').css('display');
							if ( display == 'block' )
								$(this).parent().parent().find('.colorpicker_bg').fadeOut();
						});
					});		

					
					
										  
					$('.caption_boxes .font_color').live('click', function() {
						$(this).parent().find('.colorpicker_font').fadeIn();								
					});
					$(document).mousedown(function() {
						$('.caption_boxes .font_color').each(function() {
							var display = $(this).parent().parent().find('.colorpicker_font').css('display');
							if ( display == 'block' )
								$(this).parent().find('.colorpicker_font').fadeOut();
						});
					});	
									
					$('.slide_image .elementResizable').elementDraggable();
					$('.slide_image .elementResizable').elementResizable();					
					$(".elementResizable").live('click', function(){
						var thisID = $(this).attr("id");						
						var elemID = thisID.replace("elementResizable_",""); 
						jQuery(".elementResizable").removeClass('active_caption_box');						
						jQuery(".caption_content > div").removeClass('active_caption_content');
						jQuery("#caption_nav li a").removeClass('active_caption_nav');
						jQuery("#caption_"+elemID).addClass('active_caption_content') ;						
						jQuery('#caption_nav li a[href="'+elemID+'"]').addClass('active_caption_nav') ;		
						jQuery(this).addClass('active_caption_box') ;												
					});
						
					jQuery('#caption_nav li a').live('click', function(event) {
						event.preventDefault();	
						caption_id =  jQuery(this).attr('href');						
						jQuery(".caption_content > div").removeClass('active_caption_content');
						jQuery(".elementResizable").removeClass('active_caption_box');						
						jQuery("#caption_nav li a").removeClass('active_caption_nav');
						jQuery("#caption_"+caption_id).addClass('active_caption_content') ;
						jQuery("#elementResizable_"+caption_id).addClass('active_caption_box') ;						
						jQuery(this).addClass('active_caption_nav') ;
					});
					
					jQuery(".caption_boxes .custom_text").live('keyup', function(){														
						custom_text =  jQuery(this).val().replace( /\n/g, '<br \\>' );
						caption_id =  jQuery(this).parent().parent().find(".caption_box_id").val();						
						jQuery("#elementResizable_"+caption_id+" .image_name").html(custom_text);
					});						
					
					jQuery(".caption_boxes .bg_color").live('keyup', function(){																		
						bg_color = jQuery(this).val();
						caption_id =  jQuery(this).parent().parent().find(".caption_box_id").val();
						jQuery("#elementResizable_"+caption_id+" .bg_color").css( {background: "#"+bg_color});						
					});						

					$('#add_caption_box').click(function() {
						jQuery.ajax({						
								url: "<?php echo admin_url('admin-ajax.php'); ?>",
								type: 'POST',
								data: {
								action: 'addCaption',
								sliderID: <?php echo $sliderID; ?>,														
								slide_id: '<?php echo $slide_id; ?>'
								},						
								success: function(response){
									if(response){
										jQuery(".caption_content > div").removeClass('active_caption_content');
										jQuery("#caption_nav li a").removeClass('active_caption_nav');
										resultObj = eval(response);
										jQuery("#caption_nav").append(resultObj[0]);
										jQuery(".caption_content").append(resultObj[1]);	
										jQuery(".slide_image").append(resultObj[2]);	
										//alert( resultObj[0]+"-"+resultObj[1]+"-"+resultObj[2]);
										$('.slide_image .elementResizable').elementDraggable();
										$('.slide_image .elementResizable').elementResizable();
										$('.color_boxes .colorpicker_bg').elementColorpickerBG();			
										$('.color_boxes .colorpicker_font').elementColorpickerFont();
										$('.colorpicker_bg').hide();
										$('.colorpicker_font').hide();	
									  }else{
										alert("error");
									  }	
								}
								
						});
						jQuery(".elementResizable").removeClass('active_caption_box');										
					});
					
					
					$('.delete_caption_box').live('click', function(event) {
						if(event.handled !== true){
							caption_id =  jQuery(this).parent().find(".caption_box_id").val();
							jQuery.ajax({						
									url: "<?php echo admin_url('admin-ajax.php'); ?>",
									type: 'POST',
									data: {
									action: 'deleteCaption',
									sliderID: <?php echo $sliderID; ?>,														
									slide_id: '<?php echo $slide_id; ?>',								
									caption_box_id: caption_id
									},						
									success: function(response){	
										//alert(response);										
										jQuery("#caption_<?php echo $slide_id; ?>_caption_"+response).addClass('active_caption_content') ;
										jQuery('#caption_nav li a[href="<?php echo $slide_id; ?>_caption_'+response+'"]').addClass('active_caption_nav') ;
										jQuery("#elementResizable_<?php echo $slide_id; ?>_caption_"+response).addClass('active_caption_box') ;
										$( "#elementResizable_"+caption_id ).remove();
										$( "#caption_nav a[href^="+caption_id+"]" ).parent().remove();
										$( ".caption_content #caption_"+caption_id ).remove();
									}				  
									
							});
							event.handled = true;
						}
						return false;
					});
					$('#save_all_caption_box').live('click', function(event) {
						if(event.handled !== true){
							var len = $('.caption_content > div').length;
							$('.caption_content > div').each(function(index) {
								caption_id =  jQuery(this).find(".caption_box_id").val();
								var opacity_pos  = jQuery("#caption_"+caption_id + " .opacity a").position();						
								var opacity_var = parseInt(opacity_pos.left) / 100;
								
								jQuery.ajax({						
									url: "<?php echo admin_url('admin-ajax.php'); ?>",
									type: 'POST',
									data: {
									action: 'saveCaption',
									sliderID: <?php echo $sliderID; ?>,														
									slide_id: '<?php echo $slide_id; ?>',									
									custom_text: jQuery("#caption_"+caption_id + " .custom_text").val(),
									pos_x: jQuery("#caption_"+caption_id + " .pos_x").val(),
									pos_y: jQuery("#caption_"+caption_id + " .pos_y").val(),
									element_width: jQuery("#caption_"+caption_id + " .element_width").val(),
									element_height: jQuery("#caption_"+caption_id + " .element_height").val(),
									font_color: jQuery("#caption_"+caption_id + " .font_color").val(),
									bg_color: jQuery("#caption_"+caption_id + " .bg_color").val(),
									opacity: opacity_var,
									caption_box_id: caption_id
									},						
									success: function(response){
										if (index == len - 1) {
											jQuery("#box-header-indicator").hide();
										}										
										//alert(response);
									}
								});
								jQuery("#box-header-indicator").show();								
							});
							event.handled = true;							
						}
						return false;
					});
					
					$('.save_caption_box').live('click', function() {
						caption_id =  jQuery(this).parent().parent().find(".caption_box_id").val();
						var opacity_pos  = jQuery("#caption_"+caption_id + " .opacity a").position();						
						var opacity_var = parseInt(opacity_pos.left) / 100;
						
						jQuery.ajax({						
								url: "<?php echo admin_url('admin-ajax.php'); ?>",
								type: 'POST',
								data: {
								action: 'saveCaption',
								sliderID: <?php echo $sliderID; ?>,														
								slide_id: '<?php echo $slide_id; ?>',
								custom_text: jQuery("#caption_"+caption_id + " .custom_text").val(),
								pos_x: jQuery("#caption_"+caption_id + " .pos_x").val(),
								pos_y: jQuery("#caption_"+caption_id + " .pos_y").val(),
								element_width: jQuery("#caption_"+caption_id + " .element_width").val(),
								element_height: jQuery("#caption_"+caption_id + " .element_height").val(),
								font_color: jQuery("#caption_"+caption_id + " .font_color").val(),
								bg_color: jQuery("#caption_"+caption_id + " .bg_color").val(),
								opacity: opacity_var,
								caption_box_id: caption_id
								},						
								success: function(response){																		
									//alert("saved");
								}				  
								
						});
					});					
				});
			</script>
 			
		<div class="wrap">
			<h2 class="alcyone_title">Edit Slide</h2>
			<div class="slide_image" style="width:<?php echo $banner_width; ?>px;">
				<img src="<?php echo $url; ?>" />
				<?php 
					$captions = get_post_meta($sliderID, $slide_id.'_captions');
					$captions = $captions[0];
					$custom = get_post_custom($sliderID);					
					
					$i=0;
					$loop_count = 0;
					$first = true;
					$loop = true;
					while ($loop) {					
						$i++; 
						if ($custom[$slide_id.'_caption_'.$i][0]) {
							
							$opacity = $custom[$slide_id.'_caption_'.$i.'_opacity'][0];
							if (empty($opacity) && $opacity != 0) {$opacity = 0.5;}
							$bg_color = $custom[$slide_id.'_caption_'.$i.'_bg_color'][0];
							if (empty($bg_color)) {$bg_color = "#000"; }
							$font_color = $custom[$slide_id.'_caption_'.$i.'_font_color'][0];
							if (empty($font_color)) {$font_color = "#fff"; }
												
						?>
							<div class='elementResizable <?php if ($first) {echo 'active_caption_box'; $first = false;}; ?>' id="elementResizable_<?php echo $slide_id.'_caption_'.$i; ?>" style="top:<?php echo $custom[$slide_id.'_caption_'.$i.'_pos_y'][0]; ?>px;left:<?php echo $custom[$slide_id.'_caption_'.$i.'_pos_x'][0]; ?>px;width:<?php echo $custom[$slide_id.'_caption_'.$i.'_element_width'][0]; ?>px;height:<?php echo $custom[$slide_id.'_caption_'.$i.'_element_height'][0]; ?>px;">
								<div class="bg_color" style="background:<?php echo $bg_color; ?>;opacity:<?php echo $opacity; ?>"></div>
								<div class="content image_name" style="color:<?php echo $font_color; ?>;">
									<?php echo apply_filters( 'the_content', $custom[$slide_id.'_caption_'.$i.'_custom_text'][0]); ?>											
								</div>										
								<div class="ui-resizable-handle ui-resizable-se" id="segrip"></div>		
								<input type="hidden" class="caption_box_id" value="<?php echo $slide_id.'_caption_'.$i; ?>"/>								
							</div>	
						<?php
							$loop_count++;
							if ($loop_count == $captions) {
								$loop = false;
							}
						}	
						if($i>100) {
							$loop = false;
						}
					}	
		
				?>
					
				<?php ?>
			</div>
			<div class="controls">
				<div class="alcyone_edit_box_buttons">
					<input type="submit" id="add_caption_box" value="Add caption box">
					<input type="submit" id="save_all_caption_box" value="Save captions">
					<br/>
					<span style="position:relative;display:none;width:30px;box-shadow:none;" id="box-header-indicator"><img src="<?php echo plugins_url('../images/loading.png', __FILE__); ?>" style="width:32px;margin:5px;float:left;"/><p style="color:#fff;position:absolute;float:left;left:5px;top:10px;"><?php _e('Saving...', 'AlcyoneSlider'); ?><p></span>
				</div>
				<div class="caption_boxes">
					<ul id="caption_nav">
						<?php						
						$i=0;
						$first = true;
						$loop_count = 0;
						$loop = true;
						while ($loop) {						
							$i++; 
							if ($custom[$slide_id.'_caption_'.$i][0]) {
							?>
								<li><a href="<?php echo $slide_id.'_caption_'.$i; ?>" <?php if ($first) {echo 'class="active_caption_nav"'; $first = false;}; ?>>Caption</a></li>								
							<?php
								
								$loop_count++;
								if ($loop_count == $captions) {
									$loop = false;
								}
							}	
							if($i>100) {
								$loop = false;
							}
						}	
						?>	
										
					</ul>
					<div class="caption_content">
						<?php
						$i=0;
						$first = true;
						$loop_count = 0;
						$loop = true;
						while ($loop) {
						//for ($i=0;$i<10;$i++){
							$i++; 
							if ($custom[$slide_id.'_caption_'.$i][0]) {
							?>
								
								<div id="caption_<?php echo $slide_id.'_caption_'.$i; ?>" <?php if ($first) {echo 'class="active_caption_content"'; $first = false;}; ?>>
									
									<div class="left_edit_area">
										<label>
											<a href="#" class="info"><img src="<?php echo plugins_url('../images/info_gray.png', __FILE__); ?>"/>
												<div class="info_text">
													<p>Text of caption box. You can use html tag elements <i>(e.g. h1, h2 h3... a href, span, strong etc...)</i></p>
												</div>
											</a>Content:</label></br>											
										<textarea class="custom_text" name="custom_text_<?php echo $slide_id.'_caption_'.$i; ?>" id="custom_text_<?php echo $slide_id.'_caption_'.$i; ?>"><?php echo $custom[$slide_id.'_caption_'.$i.'_custom_text'][0]; ?></textarea></br>
									</div>
									<div class="right_edit_area">	
										<a href="#" class="delete_caption_box">Delete caption</a>
										<br class="clear"/>
										<label>
											<a href="#" class="info"><img src="<?php echo plugins_url('../images/info_gray.png', __FILE__); ?>"/>
												<div class="info_text">
													<p>Position of caption box from left side in pixels</p>
												</div>
											</a>Left:
										</label><input class="pos_x num"  value="<?php echo $custom[$slide_id.'_caption_'.$i.'_pos_x'][0]; ?>"/>
										<label>
											<a href="#" class="info"><img src="<?php echo plugins_url('../images/info_gray.png', __FILE__); ?>"/>
												<div class="info_text">
													<p>Position of caption box from top in pixels</p>
												</div>
											</a>Top:</label><input class="pos_y num"   value="<?php echo $custom[$slide_id.'_caption_'.$i.'_pos_y'][0]; ?>"/>
										<label>
											<a href="#" class="info"><img src="<?php echo plugins_url('../images/info_gray.png', __FILE__); ?>"/>
												<div class="info_text">
													<p>Width of caption box in pixels</p>
												</div>
											</a>Width:</label><input class="element_width num" value="<?php echo $custom[$slide_id.'_caption_'.$i.'_element_width'][0]; ?>"/>
										<label>
											<a href="#" class="info"><img src="<?php echo plugins_url('../images/info_gray.png', __FILE__); ?>"/>
												<div class="info_text">
													<p>Height of caption box in pixels</p>
												</div>
											</a>Height</label><input class="element_height num" value="<?php echo $custom[$slide_id.'_caption_'.$i.'_element_height'][0]; ?>"/></br>
										<label class="wider">
											<a href="#" class="info"><img src="<?php echo plugins_url('../images/info_gray.png', __FILE__); ?>"/>
												<div class="info_text">
													<p>Color of fonts in caption boxes <i>(note: this can be overvritten by template style)</i></p>
												</div>
											</a>Font color:</label>
											<div class="color_boxes">
												<?php $font_color = $custom[$slide_id.'_caption_'.$i.'_font_color'][0];
												if (empty($font_color)) {$font_color = "#fff"; }
												?>
												<input class="font_color" type="text" size="25" style="width:80px;" name="font_color" value="<?php echo $font_color; ?>"/></br>											
												<div style="position: absolute;z-index:9999999;" class="colorpicker_font"></div>
											</div>
										</br>
										<label class="wider">
											<a href="#" class="info"><img src="<?php echo plugins_url('../images/info_gray.png', __FILE__); ?>"/>
												<div class="info_text">
													<p>Background of caption box </p>
												</div>
											</a>Bg color:</label> 											
											<div class="color_boxes">
												<?php $bg_color = $custom[$slide_id.'_caption_'.$i.'_bg_color'][0];
												if (empty($bg_color)) {$bg_color = "#000"; }
												?>
												<input class="bg_color" type="text" size="25" style="width:80px;" name="bg_color" value="<?php echo $bg_color; ?>" />	<br />
												<div style="position: absolute;z-index:9999999;" class="colorpicker_bg"></div>
											</div></br>
										<label>
											<a href="#" class="info"><img src="<?php echo plugins_url('../images/info_gray.png', __FILE__); ?>"/>
												<div class="info_text">
													<p>Transparency of caption box background</p>
												</div>
											</a>opacity:</label>
										<script>
										<?php
											$opacity = $custom[$slide_id.'_caption_'.$i.'_opacity'][0];											
											if (empty($opacity) && $opacity != 0) {$opacity = 0.5;}
											?>
										jQuery(document).ready(function($) {
											$(function() {
												$( "#caption_<?php echo $slide_id.'_caption_'.$i; ?> .opacity" ).slider({
												value: <?php echo ($opacity*100); ?>,
												change: function( event, ui ) {
													var p = $(this).find('a');
													var position = p.position();
													var opacity = parseInt(position.left) / 100;
													caption_id =  jQuery(this).parent().parent().find(".caption_box_id").val();							
													jQuery("#elementResizable_"+caption_id+" .bg_color").css( {opacity: opacity});						
												}
												});
											});
										});
										</script>										
										<div class="opacity" id="opacity_<?php echo $slide_id.'_caption_'.$i; ?>"></div>										
										<input type="hidden" class="caption_box_id" value="<?php echo $slide_id.'_caption_'.$i; ?>"/>
										
									</div>
									<br class="clear"/>
								</div>
							<?php
								
								$loop_count++;
							if ($loop_count == $captions) {
								$loop = false;
							}
							}	
							if($i>100) {
								$loop = false;
							}
						}	
						?>	
						
						
					</div>
					
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<?php
die();
}
/*******************************************************************************************************/
add_action('wp_ajax_saveCaption', 'saveCaption');
add_action('wp_ajax_nopriv_saveCaption', 'saveCaption');
 function saveCaption() { 

		$slide_id = $_POST['slide_id'];
		$sliderID = $_POST['sliderID'];
		$custom_text = $_POST['custom_text'];
		$pos_x = $_POST['pos_x'];
		$pos_y = $_POST['pos_y'];
		$element_width = $_POST['element_width'];
		$element_height = $_POST['element_height'];
		$font_color = $_POST['font_color'];
		$bg_color = $_POST['bg_color'];
		$opacity = $_POST['opacity'];
		$caption_box_id = $_POST['caption_box_id'];
		
		update_post_meta($sliderID, $caption_box_id.'_custom_text', $custom_text);
		update_post_meta($sliderID, $caption_box_id.'_pos_x', $pos_x);
		update_post_meta($sliderID, $caption_box_id.'_pos_y', $pos_y);
		update_post_meta($sliderID, $caption_box_id.'_element_width', $element_width);
		update_post_meta($sliderID, $caption_box_id.'_element_height', $element_height);
		update_post_meta($sliderID, $caption_box_id.'_font_color', $font_color);
		update_post_meta($sliderID, $caption_box_id.'_bg_color', $bg_color);
		update_post_meta($sliderID, $caption_box_id.'_opacity', $opacity);
		
		//echo "caption saved: " . $test;
		
die();
}

add_action('wp_ajax_deleteCaption', 'deleteCaption');
add_action('wp_ajax_nopriv_deleteCaption', 'deleteCaption');
 function deleteCaption() { 

		$slide_id = $_POST['slide_id'];
		$sliderID = $_POST['sliderID'];		
		$caption_box_id = $_POST['caption_box_id'];
		$captions = get_post_meta($sliderID, $slide_id.'_captions');
		$captions = (int)$captions[0];
		
		delete_post_meta($sliderID, $caption_box_id.'_custom_text');
		delete_post_meta($sliderID, $caption_box_id.'_pos_x');
		delete_post_meta($sliderID, $caption_box_id.'_pos_y');
		delete_post_meta($sliderID, $caption_box_id.'_element_width');
		delete_post_meta($sliderID, $caption_box_id.'_element_height');
		delete_post_meta($sliderID, $caption_box_id.'_font_color');
		delete_post_meta($sliderID, $caption_box_id.'_bg_color');
		delete_post_meta($sliderID, $caption_box_id.'_opacity');
		delete_post_meta($sliderID, $caption_box_id );	
		$new_c = update_post_meta($sliderID, $slide_id.'_captions', $captions-1 );
		
		$custom = get_post_custom($sliderID);		
		if ($captions > 1) {
			$i=0;
			$loop = true;
			while ($loop) {
			//for ($i=0;$i<10;$i++){
				$i++;  
				if ($custom[$slide_id.'_caption_'.$i][0]) {
					$active = $i;
					break;
				}			
			}	
		} 
		echo $active;
		
die();
}

add_action('wp_ajax_addCaption', 'addCaption');
add_action('wp_ajax_nopriv_addCaption', 'addCaption');
 function addCaption() {  
		$slide_id = $_POST['slide_id'];
		$sliderID = $_POST['sliderID'];
		$custom = get_post_custom($sliderID);
		$captions = get_post_meta($sliderID, $slide_id.'_captions');
		$captions = (int)$captions[0];
		
		
		$i=0;
		$loop = true;
		while ($loop) {
		//for ($i=0;$i<10;$i++){
			$i++;  
			if (!$custom[$slide_id.'_caption_'.$i][0]) {
				update_post_meta($sliderID, $slide_id.'_caption_'.$i, true );	
				update_post_meta($sliderID, $slide_id.'_captions', $captions+1 );	
				//$slide_id = 'alcyone_slide_'.$i;
				break;
			}			
		}	
		$caption_nav = '<li><a href="'.$slide_id.'_caption_'.$i.'" class="active_caption_nav">Caption</a></li>';
		$caption_content = '<div id="caption_'.$slide_id.'_caption_'.$i.'" class="active_caption_content">
									
									<div class="left_edit_area">
										<label><a href="#" class="info"><img src="'.plugins_url('../images/info_gray.png', __FILE__).'"/>
												<div class="info_text">
													<p>Text of caption box. You can use html tag elements <i>(e.g. h1, h2 h3... a href, span, strong etc...)</i></p>
												</div>
											</a>Content:</label></br>											
										<textarea class="custom_text" name="custom_text_'.$slide_id.'_caption_'.$i.'" id="custom_text_'.$slide_id.'_caption_'.$i.'"></textarea></br>										

									</div>
									<div class="right_edit_area">	
										<a href="#" class="delete_caption_box">Delete caption</a>
										<br class="clear">
										<label><a href="#" class="info"><img src="'.plugins_url('../images/info_gray.png', __FILE__).'"/>
												<div class="info_text">
													<p>Position of caption box from left side in pixels</p>
												</div>
											</a>Left:</label><input class="pos_x num"  value="20"/>
										<label><a href="#" class="info"><img src="'.plugins_url('../images/info_gray.png', __FILE__).'"/>
												<div class="info_text">
													<p>Position of caption box from top in pixels</p>
												</div>
											</a>Top:</label><input class="pos_y num"   value="20"/>
										<label><a href="#" class="info"><img src="'.plugins_url('../images/info_gray.png', __FILE__).'"/>
												<div class="info_text">
													<p>Width of caption box in pixels</p>
												</div>
											</a>Width:</label><input class="element_width num" value="100"/>
										<label><a href="#" class="info"><img src="'.plugins_url('../images/info_gray.png', __FILE__).'"/>
												<div class="info_text">
													<p>Height of caption box in pixels</p>
												</div>
											</a>Height</label><input class="element_height num" value="40"/></br>
										<label class="wider"><a href="#" class="info"><img src="'.plugins_url('../images/info_gray.png', __FILE__).'"/>
												<div class="info_text">
													<p>Color of fonts in caption boxes <i>(note: this can be overvritten by template style)</i></p>
												</div>
											</a>Font color:</label>
											<div class="color_boxes">												
												<input class="font_color" type="text" size="25" style="width:80px;" name="font_color" value="#fff"/></br>											
												<div style="position: absolute;z-index:9999999;" class="colorpicker_font"></div>
											</div>
										</br>
										<label class="wider"><a href="#" class="info"><img src="'.plugins_url('../images/info_gray.png', __FILE__).'"/>
												<div class="info_text">
													<p>Background of caption box </p>
												</div>
											</a>Bg color:</label> 											
											<div class="color_boxes">												
												<input class="bg_color" type="text" size="25" style="width:80px;" name="bg_color" value="#000" />	<br />
												<div style="position: absolute;z-index:9999999;" class="colorpicker_bg"></div>
											</div></br>
										<label><a href="#" class="info"><img src="'. plugins_url('../images/info_gray.png', __FILE__).'"/>
												<div class="info_text">
													<p>Transparency of caption box background</p>
												</div>
											</a>opacity:</label>
										<script>										
										jQuery(document).ready(function($) {
											$(function() {
												$( "#caption_'.$slide_id.'_caption_'.$i.' .opacity" ).slider({
												value: 0.5*100,
												change: function( event, ui ) {
													var p = $(this).find("a");
													var position = p.position();
													var opacity = parseInt(position.left) / 100;
													caption_id =  jQuery(this).parent().parent().find(".caption_box_id").val();							
													jQuery("#elementResizable_"+caption_id+" .bg_color").css( {opacity: opacity});						
												}
												});
											});
										});
										</script>										
										<div class="opacity" id="opacity_'.$slide_id.'_caption_'.$i.'"></div>										
										<input type="hidden" class="caption_box_id" value="'. $slide_id.'_caption_'.$i.'"/>										
									</div>
									<br class="clear">
								</div>';
		$caption_box = '<div class="elementResizable active_caption_box" id="elementResizable_'. $slide_id.'_caption_'.$i.'" style="top:20px;left:20px;width:100px;height:40px;">
								<div class="bg_color" style="background:#000;opacity:0.5"></div>
								<div class="content image_name" style="color:#fff;">									
								</div>										
								<div class="ui-resizable-handle ui-resizable-se" id="segrip"></div>		
								<input type="hidden" class="caption_box_id" value="'. $slide_id.'_caption_'.$i.'"/>								
								
							</div>';
							
		$array = array($caption_nav, $caption_content, $caption_box);
		echo json_encode( $array );
			
		
die();
}
 
/****************************************************************************************************************************/
add_action('wp_ajax_step_2', 'step_2');
add_action('wp_ajax_nopriv_step_2', 'step_2');

  function step_2() {
  
		$banner_id = $_POST['sliderID'];
		$attachment_id = $_POST['attachment_id'];
		$image_attributes = wp_get_attachment_image_src( $attachment_id, "full");			
		$url = $image_attributes[0];
		$width = $image_attributes[1];
		$height = $image_attributes[2];		

		$available = get_post_meta($banner_id, 'available_headers');
	    $custom = get_post_custom($banner_id);
	    $banner_height = $custom['height'][0];	
	    $banner_width = $custom['width'][0];
	  
		if ( $width == $banner_width && $height == $banner_height ) {
						
			//do_action('wp_create_file_in_uploads', $file, $id); // For replication
			echo 1;
			die();
			
		} elseif ( $width > $banner_width ) {
			$oitar = $width / $banner_width;
			$url= wp_get_attachment_url( $attachment_id);
			
			$width = $width / $oitar;
			$height = $height / $oitar;
			
		} else {
			$oitar = 1;
		}
		?>
<script type="text/javascript">
/* <![CDATA[ */
	function onEndCrop( coords ) {
		jQuery( '#x1' ).val(coords.x);
		jQuery( '#y1' ).val(coords.y);
		jQuery( '#width' ).val(coords.w);
		jQuery( '#height' ).val(coords.h);
	}

	jQuery(document).ready(function() {
		var xinit = <?php echo $banner_width; ?>;
		var yinit = <?php echo $banner_height; ?>;
		var ratio = xinit / yinit;
		var ximg = jQuery('img#upload').width();
		var yimg = jQuery('img#upload').height();

		if ( yimg < yinit || ximg < xinit ) {
			if ( ximg / yimg > ratio ) {
				yinit = yimg;
				xinit = yinit * ratio;
			} else {
				xinit = ximg;
				yinit = xinit / ratio;
			}
		}

		
		jQuery('img#upload').imgAreaSelect({
			handles: true,
			keys: true,
			parent: "#crop_image",			
			aspectRatio: xinit + ':' + yinit,
			show: true,
			x1: 0,
			y1: 0,
			x2: xinit,
			y2: yinit,
			maxHeight: <?php echo $banner_height; ?>,
			maxWidth: <?php echo $banner_width; ?>,
			onInit: function () {
				jQuery('#width').val(xinit);
				jQuery('#height').val(yinit);
			},
			onSelectChange: function(img, c) {
				jQuery('#x1').val(c.x1);
				jQuery('#y1').val(c.y1);
				jQuery('#width').val(c.width);
				jQuery('#height').val(c.height);
			}
		});
	});
/* ]]> */
</script>
<div class="wrap">
<h2 class="alcyone_title"><?php _e( 'Crop Slider Image' ); ?></h2>

<form method="post" onsubmit="return step_3(this);">	

	<div id="crop_image" style="position: relative">
		<img src="<?php echo esc_url( $url ); ?>" id="upload" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
	</div>

	<p class="submit">
	<input type="hidden" name="x1" id="x1" value="0"/>
	<input type="hidden" name="y1" id="y1" value="0"/>
	<input type="hidden" name="width" id="width" value="<?php echo esc_attr( $width ); ?>"/>
	<input type="hidden" name="height" id="height" value="<?php echo esc_attr( $height ); ?>"/>
	<input type="hidden" name="attachment_id" id="attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>" />
	<input type="hidden" name="oitar" id="oitar" value="<?php echo esc_attr( $oitar ); ?>" />
	<?php wp_nonce_field( 'custom-header-crop-image' ) ?>
	<input type="submit" class="button-primary save_alcyone" value="<?php esc_attr_e( 'Crop and Insert' ); ?>" />
	</p>
	<p class="hide-if-no-js"><?php _e('Choose the part of the image you want to add in your slider.'); ?></p>
	<p class="hide-if-js"><strong><?php _e( 'You need Javascript to choose a part of the image.'); ?></strong></p>
</form>
</div>
		<?php
		die();
  }
  
/**********************************************************************************************************************************************/

add_action('wp_ajax_step_3', 'step_3');
add_action('wp_ajax_nopriv_step_3', 'step_3');

function step_3() {
		//check_admin_referer('custom-header-crop-image');
		if ( $_POST['oitar'] > 1 ) {
			$_POST['x1'] = $_POST['x1'] * $_POST['oitar'];
			$_POST['y1'] = $_POST['y1'] * $_POST['oitar'];
			$_POST['width'] = $_POST['width'] * $_POST['oitar'];
			$_POST['height'] = $_POST['height'] * $_POST['oitar'];
		}

		$custom = get_post_custom($_POST['banner']);
	    $banner_height = $custom['height'][0];	
	    $banner_width = $custom['width'][0];
		
		$original_id = get_attached_file( $_POST['attachment_id'] );
		
		$cropped = wp_crop_image($original_id, $_POST['x1'], $_POST['y1'], $_POST['width'], $_POST['height'], $banner_width, $banner_height);
		if ( is_wp_error( $cropped ) )
			wp_die( __( 'Image could not be processed.  Please go back and try again.' ), __( 'Image Processing Error' ) );

		$cropped = apply_filters('wp_create_file_in_uploads', $cropped, $original_id); // For replication

		$parent = get_post($original_id);
		$parent_url = $parent->guid;
		$url = str_replace(basename($parent_url), basename($cropped), $parent_url);

		// Construct the object array
		$object = array(			
			'post_title' => basename($cropped),
			'post_content' => $url,
			'post_mime_type' => 'image/jpeg',
			'guid' => $url
		);

		// Update the attachment
		$cropped_image = wp_insert_attachment($object, $cropped);
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $cropped_image, $cropped );
		wp_update_attachment_metadata( $cropped_image, $attach_data );
		
		$cropped_image_url = wp_get_attachment_url( $cropped_image ); 
		$cropped_image_id = $cropped_image; 
				
		$sliderID = $_POST['banner'];
		$url= wp_get_attachment_url($cropped_image_id);
		
	    $custom = get_post_custom($sliderID);
	    $banner_height = $custom['height'][0];	
	    $banner_width = $custom['width'][0];	
		$alcyone_slides = $custom['slides'][0];	
	
		$i=0;
		$loop = true;
		while ($loop) {
		//for ($i=0;$i<10;$i++){
			$i++;  
			if ($custom['alcyone_slide_'.$i][0] == "") {
				update_post_meta($sliderID, 'alcyone_slide_'.$i, $cropped_image_id );	
				update_post_meta($sliderID, 'slides', $alcyone_slides+1 );	
				$slide_id = 'alcyone_slide_'.$i;
				$loop = false;
			}			
		}	  
		
			  
		?>
		<li>
			<img src="<?php echo $url; ?>" width="<?php echo $prew_width; ?>" /></br>
			<input type="hidden" id="image_id" value="<?php echo $cropped_image_id; ?>"/>
			<input type="hidden" id="slide_id" value="<?php echo $slide_id; ?>"/>
			<input type="submit" value="" class="settings"/>						              
			<input type="submit" value="" class="delete"/>                
		</li>
		<?php
		
		// cleanup
		$medium = str_replace(basename($original), 'midsize-'.basename($original), $original);
		@unlink( apply_filters( 'wp_delete_file', $medium ) );
		@unlink( apply_filters( 'wp_delete_file', $original ) );
		//return finished();
		
die();
  }
/*******************************************************************************************************/

 
add_action('wp_ajax_insertSliderImage', 'insertSliderImage');
add_action('wp_ajax_nopriv_insertSliderImage', 'insertSliderImage');
 function insertSliderImage() {  
		$attachment_id = $_POST['attachment_id'];
		$sliderID = $_POST['sliderID'];
		$url= wp_get_attachment_url($attachment_id);
		
	    $custom = get_post_custom($_POST['sliderID']);
	    $banner_height = $custom['height'][0];	
	    $banner_width = $custom['width'][0];	
		$alcyone_slides = $custom['slides'][0];	
	
		$i=0;
		$loop = true;
		while ($loop) {
		//for ($i=0;$i<10;$i++){
			$i++;  
			if ($custom['alcyone_slide_'.$i][0] == "") {
				update_post_meta($sliderID, 'alcyone_slide_'.$i, $attachment_id );	
				update_post_meta($sliderID, 'slides', $alcyone_slides+1 );	
				$slide_id = 'alcyone_slide_'.$i;
				$loop = false;
			}			
		}		
		
		?>
		<li>
			<img src="<?php echo $url; ?>" width="<?php echo $prew_width; ?>" /></br>
			<input type="hidden" id="image_id" value="<?php echo $attachment_id; ?>"/>
			<input type="hidden" id="slide_id" value="<?php echo $slide_id; ?>"/>
			<input type="submit" value="" class="settings"/>						              
			<input type="submit" value="" class="delete"/>                
		</li>
		<?php
die();
}
/*******************************************************************************************************/

add_action('wp_ajax_get_alcyone_slider_generator', 'get_alcyone_slider_generator');
add_action('wp_ajax_nopriv_get_alcyone_slider_generator', 'get_alcyone_slider_generator');
 function get_alcyone_slider_generator() { 
		
		$alcyone_slides = '
		<script>
			jQuery(".select_slider #insert_shortcode").click(function(e){
						e.preventDefault();	
						number = jQuery("#slider_ID").val();
						shortcode = \'[alcyone_slider id="\' + number + \'"/]\';
						tinyMCE.activeEditor.execCommand(\'mceInsertContent\', 0, shortcode); 
						tb_remove();
				});
		</script>
		
		<h3>'. __( 'Select slider' ) .'</h3>
		<div class="select_slider">
			<select id="slider_ID" name="slider_ID" >';
		
		$sliders = get_posts("post_type=alcyoneslider&posts_per_page=-1"); 						  
		foreach ( $sliders as $slider ) {
			$custom = get_post_custom($slider->ID);
			$banner_height = $custom['height'][0];	
			$banner_width = $custom['width'][0];
			$alcyone_options  .= '<option value="'.$slider->ID.'" ';				
			$alcyone_options .= '">';
			$alcyone_options .= $slider->post_title." - ".$banner_width."px/".$banner_height."px";
			$alcyone_options .= '</option>';
		}
			
		$alcyone_options .= '</select><input type="submit" class="button-primary" id="insert_shortcode" value="insert slider"/></div>'; 	
				
		echo $alcyone_slides.$alcyone_options;
die();		
}