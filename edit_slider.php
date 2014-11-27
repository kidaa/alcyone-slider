<?php 
/**********************************************************************************************************************************************/
  function step_1($banner_id) {
  
	add_image_size( "prod_thumb", 150, 150, true );
	wp_enqueue_media();
	wp_enqueue_style('thickbox');
	wp_enqueue_script('thickbox');  
	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic' );  
	wp_enqueue_script('wplink');
	wp_enqueue_script( 'jquery' ); 	
	wp_enqueue_script( 'jquery-ui-slider' ); 	 	
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	wp_enqueue_script( 'jquery-ui-sortable' );
  ?>
  
			<script language="javascript" type="text/javascript">	
			jQuery(document).ready(function() {
				
			jQuery("#upload").change(function(){
				jQuery("#upload-form").submit();
			});
	
			jQuery('a.delete').click(function(e) {
				return confirm("Are you sure you want to delete this slider?");
			});
	  
			jQuery("#inactive").sortable({connectWith:'#active', stop: serializeLists});
			jQuery("#active").sortable({connectWith:'#inactive', stop: serializeLists});
			jQuery("#title").live('change',updateSliderTitle);
			jQuery("#inactive input.text").live('change',serializeLists);
			jQuery("#active input.text").live('change',serializeLists);
			jQuery("#inactive input.save").live('click',serializeLists);
			jQuery("#active input.save").live('click',serializeLists);
			jQuery("#inactive input.delete").live('click',function(e) { 
				e.preventDefault();
				jQuery('#response').empty().slideUp();
				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
					action:'deleteSlide', 
					slide_id: jQuery(this).parent("li").find("#slide_id").val(), 
					post_id:<?php echo $banner_id ?>}, 
					function(data) {						
					}
				);				
				//if (confirm("Are you sure you want to remove this from your pool of rotating headers? You will not be able to recover it.")) {
				jQuery(this).parent("li").remove();
				serializeLists();
				//}
			});
			
			jQuery("#active input.delete").live('click',function(e) { e.preventDefault();
				jQuery("#inactive").append(jQuery(this).parent("li").detach());
				jQuery(this).parent().find('.settings').stop().animate({opacity: 0});
				jQuery(this).parent().find('.delete').stop().animate({opacity: 0});
				serializeLists();
			});
			

			jQuery(".image_holder ul li").live({
				mouseenter:
				  function () {
					jQuery(this).find('.settings').stop().animate({opacity: 1});
					jQuery(this).find('.delete').stop().animate({opacity: 1});
				},
				mouseleave:
				  function () {
					jQuery(this).find('.settings').stop().animate({opacity: 0});
					jQuery(this).find('.delete').stop().animate({opacity: 0});
					//jQuery(this).find('.image_settings').stop().animate({opacity: 0}).hide(0);	
				}
			});			
			
				
			jQuery(".image_holder ul li .settings").live('click', function (e) {						
				jQuery('#response').empty().slideUp();
				slide_id = jQuery(this).parent().find('#slide_id').val();
				editSlide(slide_id);
			});
			function editSlide(slide_id){
				
				jQuery.ajax({						
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type: 'POST',
						data: {
						action: 'editSlide',
						sliderID: <?php echo $_GET['banner']; ?>,						
						slide_id: slide_id						
						},						
						success: function(response) {						
							jQuery("#upload-indicator").hide();							
							jQuery("#response").html(response).slideDown();
							jQuery("#close_response").show();
							jQuery(".preview_slider-indicator").hide();							
						}				  
					
				});
			};
			
			jQuery(".image_holder ul li .image_settings .close_image_settings").live( "click", 
					function () {	
						//jQuery(this).parent().stop().slideUp(500).animate({opacity: 0});	
						jQuery(this).parent().find('.settings').removeClass("active").animate({opacity: 0});
						jQuery(this).parent().find('.delete').removeClass("inactive").animate({opacity: 0});									
					}					
				);				

			jQuery(document).mouseup(function (e)
			{
				var container = jQuery(".image_settings");

				if (container.has(e.target).length === 0)
				{
					container.stop().slideUp(500).animate({opacity: 0});	
					container.parent().find('.settings').removeClass("active").animate({opacity: 0});
					container.parent().find('.delete').removeClass("inactive").animate({opacity: 0});
				}
			});	

			
			/*
				
			function serializeLists() {
				var active = jQuery("#active li").map(function() { return { slide_id: jQuery(this).find('#slide_id').val(); };															 
                                                    });
				var inactive = jQuery("#inactive li").map(function() { return { slide_id: jQuery(this).find('#slide_id').val(); }; });
				
				
				jQuery("#header-indicator").show();				
				jQuery(".header-indicator").show();				
				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
					action:'update_alcyone_slider', 
					active:active.get(), 
					inactive:inactive.get(), 
					post_id:<?php echo $banner_id ?>}, 
					function(data) {
						jQuery("#preview-header").html(data);
						//$("#preview-header .rotating-header").dl({reset:true});
						jQuery("#header-indicator").hide();
						jQuery(".header-indicator").hide();
					}
				);
			}
			*/
			
			function updateSliderTitle() {
				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
					action:'updateSliderTitle', 
					title: jQuery("#title").val(), 
					post_id:<?php echo $banner_id ?>}, 
					function(data) {						
					}
				);
			}
			
				jQuery("#close_response").click(function() {
					jQuery("#response").slideUp().html("");						
					
					jQuery("#close_response").hide();
					jQuery('img#upload').imgAreaSelect({remove:true});					
				});
				jQuery('.preview_slider').click(function() {  
					jQuery('#response').empty().slideUp();
					jQuery(".preview_slider-indicator").show();				
					previewSlider();
					return false;  

				}); 				
				

				var selector = jQuery(this).selector; // Get the selector
				// Set default options
				var defaults = {
					'preview' : '.preview-upload',
					'text' : '.text-upload',
					'button' : '#add_slider_image',
				};
				var options = jQuery.extend(defaults, options);
						 
				var _custom_media = true;
				var _orig_send_attachment = wp.media.editor.send.attachment;
						 
				// When the Button is clicked...
				jQuery(options.button).click(function() {

					jQuery(".imgareaselect-selection").parent().remove();
					jQuery(".imgareaselect-outer").remove();
					// Get the Text element.
					var button = jQuery(this);
					var text = jQuery(this).siblings(options.text);
					var send_attachment_bkp = wp.media.editor.send.attachment;
											 
					//Extend the wp.media object
					uploader = wp.media.frames.file_frame = wp.media({
						title: "Select image for slider",
						button: {
							text: "Insert into slider"
							},
						multiple: false
					});

					//When a file is selected, grab the URL and set it as the text field's value
					uploader.on('select', function() {
						attachment = uploader.state().get('selection').first().toJSON();
						step_2(attachment.id);
					});
					
					//Open the uploader dialog
					uploader.open();
					
					return false;
				});
				
			});
			
			function previewSlider(){
				
				jQuery.ajax({						
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type: 'POST',
						data: {
						action: 'previewAlcyoneSlider',
						sliderID: <?php echo $_GET['banner']; ?>						
						},						
						success: function(response) {						
							jQuery("#upload-indicator").hide();
							
							jQuery("#response").html(response).slideDown();
							jQuery("#close_response").show();
							jQuery(".preview_slider-indicator").hide();							
						}				  
					
				});
			};
			
			function step_2(attachment_id){
				
				jQuery.ajax({						
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type: 'POST',
						data: {
						action: 'step_2',
						sliderID: <?php echo $_GET['banner']; ?>,						
						attachment_id: attachment_id
						},						
						success: function(response){
							direct = parseInt(response);							
							if (direct == 1) {
								insertSliderImage(attachment_id);
							} else {
								jQuery("#upload-indicator").hide();
								
								jQuery("#response").html(response).slideDown();
								jQuery("#close_response").show();								
							}
						}				  
					
				});
			};
			function insertSliderImage(attachment_id) {
				
				jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type: 'POST',
						data: {
						action: 'insertSliderImage',
						sliderID: <?php echo $_GET['banner']; ?>,						
						attachment_id: attachment_id
						},
						dataType: 'html',
						success: function(response) {						
							jQuery("#response").html("");	
							jQuery("#inactive").prepend(response);					
							serializeLists();
						
						}
					});
					jQuery("#header-indicator").show();			
					//startIndicator();
					return false;		
			}
			function step_3() {
				
				jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type: 'POST',
						data: {
						action: 'step_3',
						banner: <?php echo $_GET['banner']; ?>,
						x1: jQuery("#x1").attr("value"),
						y1: jQuery("#y1").attr("value"),
						width: jQuery("#width").attr("value"),
						height: jQuery("#height").attr("value"),
						attachment_id: jQuery("#attachment_id").attr("value"),
						oitar: jQuery("#oitar").attr("value"),
						_wpnonce: jQuery("#_wpnonce").attr("value")
						},
						dataType: 'html',
						success: function(response) {
						jQuery('img#upload').imgAreaSelect({remove:true});
						jQuery("#response").slideUp().html("");	
						
						jQuery("#close_response").hide();							
						tb_remove();
						
						jQuery("#response").html("");	
						jQuery("#inactive").prepend(response);					
						serializeLists();
						
						}
					});
					jQuery("#header-indicator").show();			
					//startIndicator();
					return false;		
			}
									
			function updateSliderSettings(){
				jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type: 'POST',
						data: {
						action: 'updateSliderSettings',
						sliderID: <?php echo $_GET['banner']; ?>,
						slider_name: jQuery("#slider_name").attr("value"),
						slider_transition: jQuery("#slider_transition").attr("value"),
						slider_duration: jQuery("#slider_duration").attr("value"),
						slider_transition_type: jQuery("#slider_transition_type").attr("value"),						
						pause_on_hover: jQuery("#pause_on_hover").prop("checked"),
						autoplay: jQuery("#autoplay").prop("checked"),
						stop_on_action: jQuery("#stop_on_action").prop("checked"),
						navigation_skin: jQuery("#navigation_skin").attr("value"),
						navigation_dots: jQuery("#navigation_dots").attr("value"),
						navigation_arrows: jQuery("#navigation_arrows").attr("value"),
						auto_hide_arrows: jQuery("#auto_hide_arrows").prop("checked"),
						auto_hide_dots: jQuery("#auto_hide_dots").prop("checked")
						},
						dataType: 'html',
						success: function(response) {
						//alert(response);
						jQuery("#header-indicator").hide();
						jQuery("#preview-header").html(response);
						//dieIndicator();		
						//return true;
						//window.location = "?page=rotating_banner&action=new&banner="+response;					
						//document.getElementById("admin_rooms").innerHTML = response;								
						}
					});
					jQuery("#header-indicator").show();			
					
					return false;				
				}
			function serializeLists() {
				var active = jQuery("#active li").map(function() { return { 
																	slide_id: jQuery(this).find('#slide_id').val(),			
                                                             };															 
                                                    });
													

				var inactive = jQuery("#inactive li").map(function() { return { 
																	slide_id: jQuery(this).find('#slide_id').val(),			
                                                             };															 
                                                    });
				
				
				jQuery("#header-indicator").show();				
				jQuery(".header-indicator").show();				
				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
					action:'update_alcyone_slider', 
					active:active.get(), 
					inactive:inactive.get(), 
					post_id:<?php echo $banner_id ?>}, 
					function(data) {
						jQuery("#preview-header").html(data);
						//$("#preview-header .rotating-header").dl({reset:true});
						jQuery("#header-indicator").hide();
						jQuery(".header-indicator").hide();
					}
				);
			}
			
		
			</script>	
			
  <div class="wrap" style="position:relative;">	
	<div id="alcyone-icon" class="icon32"><img src="<?php echo plugins_url( 'images/favicon_32.png' , __FILE__ ) ; ?>"/></div> <h2 ><?php _e('Edit Slider')?></h2>
      <style type="text/css">
        #inactive, #active {  
			list-style-type: none; 
			margin: 0; 
			padding: 0; 
			float: left; 
			margin-right: 0px; 			
			width: auto; 
			max-width:350px; 
			min-width:100px;
			min-height:300px; 
			position:relative;
		}
		.image_holder .postbox {			
			border: 1px solid #DFDFDF;
			border-radius: 3px;
			width: 355px; 
		}
        #inactive li, #active li { margin: 5px; padding: 5px; font-size: 1.2em; width: auto; cursor: move; position:relative;background: transparent url('<?php echo plugins_url('/images/loading.png', __FILE__); ?>') no-repeat center center !important;min-height:50px;}
		.image_holder ul li .settings {position:absolute; top:10px; left: 10px; opacity:0;font-weight:bold;font-size:30px;width:32px;height:32px;display:block;background: transparent url('<?php echo plugins_url('/images/settings.jpg', __FILE__); ?>') no-repeat center center !important; border:none !important;cursor:pointer;border-radius: 17px;box-shadow: 0 0 5px #000000;}
		.image_holder ul li .active {display:block !important; opacity:1 !important;}
		.image_holder ul li .inactive {display:none !important; opacity:0 !important;}
		.image_holder ul li .delete {position:absolute;top:10px; right:15px;width:32px;height:32px;display:block;opacity:0;font-weight:bold;font-size:20px;background: transparent url('<?php echo plugins_url('/images/delete.jpg', __FILE__); ?>') no-repeat center center !important; border:none !important;cursor:pointer;border-radius: 17px;box-shadow: 0 0 5px #000000;}
		.image_holder img { box-shadow: 5px 5px 5px #999999;border-radius:2px; max-width:325px;}
		.image_settings { cursor: pointer;display:none; opacity:0;position:absolute;top:39px;left:39px;background: transparent url('<?php echo plugins_url('/images/overlay_white.png', __FILE__); ?>') repeat center center !important;border-radius:5px;padding:10px;width:100%;box-shadow: 3px 3px 8px #000;z-index:9999;}
		.image_settings div { padding:5px 0 ; margin:0 ;width:250px !important;}
		.image_settings div:first-child { border-bottom:1px solid #ccc;}
		.image_settings div > div:first-child { border-bottom:none;}
		.image_settings div label{ font-size:12px;display:inline-block;width:80px;vertical-align:top;padding-top:7px;}
		.image_settings div select{ font-size:12px;font-size:12px;}
		.image_settings div textarea{ width:160px;height:80px;font-size:12px;}
		.image_settings div input[type="text"] {width:160px !important;font-size:12px;}
		.image_settings div .save {float:right;margin-right:5px;}
		.post, .custom, .link {display:block;}
		.image_settings div small {font-size:10px;color:#aaa;}
		a:active, a:focus {border:none;}
		.slider_title {width:400px !important;}
		#titlediv .top_items {float:left;padding:0 0 5px;}
		#titlediv .top_items label {display:inline-block;padding:0 5px 0 20px;}
		#titlediv #slider_shortcode { font-size: 1.7em; line-height: 100%; outline: 0 none;padding: 3px 8px;width: 300px;}
		.slider_settings {border-top:2px solid #888888;width:310px;margin-top:20px;float:right;clear:right;position:relative;}
		.header-indicator img {position:relative;top:5px;box-shadow:none;}
		.close_image_settings {position:absolute;top:-15px;right:-15px;box-shadow:none !important;}
		#preview-header {margin:10px 0 20px;float:left;clear:left;}
		.slider_content_area {clear:left;float: left;}
		.general_settings {margin:10px;}
		.general_settings label{display:inline-block;width:100px;}
		#response {
			box-shadow:2px 2px 5px #666;
			background: linear-gradient(to top, #666, #999) repeat scroll 0 0 #F5F5F5;
			border-radius:3px;			
			width:100%;	
			display:none;			
			margin:10px 0 20px;
			}
		#response .wrap {
			padding:10px;
		}
		#response .wrap h2, #response .wrap p{
			color:#fff;text-shadow:none;
		}
		
		.black {position:relative;margin-top: 15px;}
		#close_response {
			position:absolute;
			top:25px;
			right:25px;
			width:20px;
			height:20px;
			background: transparent url('<?php echo plugins_url('/images/close_response.png', __FILE__); ?>') no-repeat center center !important;
			cursor:pointer;
			display:none;
			}
		#close_response:hover {
			opacity:0.5;
			}
		#response .rotating_slider {
			padding:25px 60px 10px 25px;
			}
		.preview_slider {
			float:right;
			}
		.slider_content_area.post-body-content {
			margin-bottom: 160px;
		}	
			
		.info > img {
			float: right;
			opacity: 0.5;
			position: relative;
			top: 5px;
		}
		.info > img:hover { 
			opacity: 1; 
		}
		.info:hover .info_text {
			display:block;
		}
		.info_text {
			display:none;
			position:absolute;
			right:0;
			top:25px;
			border:1px solid #ccc;
			background:#dff384;
			padding:5px 15px;
			box-shadow:2px 2px 5px #999;
			width:90%;
			z-index:999999;
			color:#000;
			opacity:0.9;
		}
		.general_settings > li {
			position:relative;
		}
		
		.image_holder{
			position:relative;
		}	
		
		#add_slider_image, .preview_slider {
			position:absolute;
			top:8px;
			right:15px;
			background: url('<?php echo plugins_url('/images/button_bg.jpg', __FILE__); ?>') repeat-x scroll center center !important;
			border: medium none !important;
			border-radius: 3px;
			box-shadow: 1px 1px 2px #444;			
			font-size: 16px !important;
			font-weight: bold;
			height: 37px !important;			
			padding:8px 15px !important;
			color:#333 !important;
			text-shadow: 0px 0px 2px #fff !important;
		}
		
		#available_images h3, #active_images h3 {
			padding:20px;
			
		}
		.slider_title {
			width:358px !important;			
			margin-right:10px !important;
		}
		#slider_shortcode {			
			width:355px !important;			
		}


      </style>

	  <?php
		$custom = get_post_custom($_GET['banner']);
		$banner_height = $custom['height'][0];	
		$banner_width = $custom['width'][0];
	  ?>
<div id="poststuff" >	  
	<div id="post-body" class="metabox-holder columns-2">
	
		
		
		<div class="slider_content_area post-body-content">
			 <div id="titlediv">
				<div id="titlewrap" class="top_items">			
					<input type="text" autocomplete="off" id="title" class="slider_title" value="<?php echo get_the_title($banner_id); ?>" size="30" name="post_title" placeholder="<?php _e("Name of the slider", "AlcyoneSlider");?>">
				</div>
				<div class="top_items">
					
					<input size="30" id="slider_shortcode" type="text" name="slider_shortcode" value='[alcyone_slider id="<?php echo $banner_id; ?>"]' readonly="readonly" />
				</div>				
			  </div>
			  <div class="clear"></div>
			  
			  
			  <span style="display:none;float:right;width:30px;box-shadow:none;" class="preview_slider-indicator"><img src="/wp-admin/images/wpspin_light.gif"/></span>
			  
			  <div class="black">										
					<div id="close_response"></div>
					<div id="response">					
					</div>
			</div>			 
			  
			 
			  <div style="clear:left;" class="image_holder">
				<div id="available_images" style="float: left; margin-right: 10px;position:relative;" class="postbox" >
					<h3>Available Slides </h3><a class="button thickbox<?php if ( $unsaved ) echo ' current'; ?>" id="add_slider_image"  href="#TB_inline?height=200&width=300&inlineId=rban-add-slide-modal"><?php echo esc_html('Add images'); ?></a>
				  <ul id="inactive" >
				  
					<?php 
					  $available = get_post_meta($banner_id, 'available_headers');
					  //$available = get_theme_mod("available_headers");
					  if (is_array($available[0])) {
					  $avl = 0;
					  foreach ($available[0] as $header) {
						$avl++;
						// migrate old scheme				
						if (is_string($header)) {
						  $header = array('image' => $header); // assume old scheme that stored just an array of images
						}
						$slide_id = $header['slide_id'];
						$image_id = $custom[$slide_id][0];						
						$url= wp_get_attachment_url($image_id);
					?>
					  <li>						
						<img src="<?php echo $url; ?>" style="max-width:100%;" />                														
						<input type="hidden" id="image_id" value="<?php echo $image_id; ?>"/>
						<input type="hidden" id="slide_id" value="<?php echo $slide_id; ?>"/>
						<input type="submit" value="" class="settings"/>						              						
						<input type="submit" value="" class="delete"/>                
					  </li>
					<?php } } ?>
				  </ul>
				</div>
			
				<div id="active_images" style="float:left; position:relative;" class="postbox">				
				  <h3>Active Slides </h3><a class="button preview_slider thickbox<?php if ( $unsaved ) echo ' current'; ?>" href="#TB_inline?width=<?php echo $banner_width; ?>&height=<?php echo $banner_height; ?>&inlineId=rban-preview-header-modal"><?php echo esc_html('Preview slider'); ?></a>
				  <ul id="active" >
				  
					<?php 
					  $active = get_post_meta($banner_id, 'active_headers');
					  //$active = get_theme_mod("active_headers");
					  if (is_array($active[0])) {
						foreach ($active[0] as $header) { 			  
							$slide_id = $header['slide_id'];
							$image_id = $custom[$slide_id][0];						
							$url= wp_get_attachment_url($image_id);
							?>
							  <li>						
								<img src="<?php echo $url; ?>" style="max-width:100%;" />                														
								<input type="hidden" id="image_id" value="<?php echo $image_id; ?>"/>
								<input type="hidden" id="slide_id" value="<?php echo $slide_id; ?>"/>
								<input type="submit" value="" class="settings"/>						              						
								<input type="submit" value="" class="delete"/>                
							  </li>
					<?php } } ?>
				  </ul>
				</div>
			</div>
			<div class="clear"></div>
	
		</div><!-- end of div slider content area -->
		
		<div id="postbox-container-1">
		<form method="post" action="" onsubmit="return updateSliderSettings(this);">
			
			<div id="submitdiv" class="postbox ">
				<div class="handlediv" title="Click to toggle">
					<br>
				</div>
				<h3 class="hndle">
					<span>Settings</span>
				</h3>				
				<div class="inside">
					<h3>Slider dimensions</h3>
					<div id="submitpost" class="submitbox">
						<ul class="general_settings">		
							<li>
								<label for="slider_shortcode"><?php _e("Width"); ?>:</label>           
								<input size="8" type="text" name="slider_shortcode" value='<?php echo $banner_width; ?>px' readonly="readonly" />
							</li>
							<li>
								<label for="slider_shortcode"><?php _e("Height"); ?>:</label>           
								<input size="8" type="text" name="slider_shortcode" value='<?php echo $banner_height; ?> px' readonly="readonly" />
							</li>							
						</ul>
						
					</div>
				</div>
		
				<div class="inside">
					<h3>General settings</h3>
					<div id="submitpost" class="submitbox">
						
						  <?#php wp_nonce_field('update-options'); ?>
						  <?php settings_fields('rotating-header'); ?>				  
						 <ul class="general_settings">		
							<li>
								<label for="slider_transition"><?php _e("Transition Time"); ?>:</label>
								<?php
									$active = get_post_meta($id, 'active_headers');
									$selected = stripslashes($custom['slider_transition'][0]);
									if (!$selected) { $selected = 2; }
								?>
								<input size="8" id="slider_transition" type="text" name="slider_transition" value="<?php echo $selected ?>" /><span style="color:silver"> seconds</span>
								<a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Time to take transition between to slides (in seconds)</p>
									</div>
								</a>
							</li>
							<li>
								<label for="slider_duration"><?php _e("Duration"); ?>:</label>
								<?php
									$selected = stripslashes($custom['slider_duration'][0]);
									if (!$selected) { $selected = 4; }
								?>
								<input size="8" id="slider_duration" type="text" name="slider_duration" value="<?php echo $selected; ?>" /><span style="color:silver"> seconds</span>
								<a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Duration of each slide(in seconds)</p>
									</div>
								</a>
							</li>
							<li>
							  <label for="slider_transition_type"><?php _e("Transition Type"); ?>:</label>
								<?php		 
								  $selected = stripslashes($custom['slider_transition_type'][0]);
								  if (empty($selected)) { $selected = 'fade'; }
								?>
								<select id="slider_transition_type" name="slider_transition_type">                  
								  <option <?php if ($selected =='fade') { echo 'selected="selected"'; } ?> value="fade">Fade</option>				  
								  <option <?php if ($selected =='horiz'){ echo 'selected="selected"'; } ?> value="horiz">Horizontal</option>				  
								  <option <?php if ($selected =='vert'){ echo 'selected="selected"'; } ?> value="vert">Vertical</option>
								  <option <?php if ($selected =='vert_stripes'){ echo 'selected="selected"'; } ?> value="vert_stripes">Modern</option>				  
								  <option <?php if ($selected =='horiz_stripes'){ echo 'selected="selected"'; } ?> value="horiz_stripes">Modern two</option>				  
								</select>
								<a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Type of transition between slides</p>
									</div>
								</a>
								
							</li>              
							<li>
							  <label for="pause_on_hover"><?php _e("Pause on mouseover");  ?>:</label>  								
								<input id="pause_on_hover" size="20" name="pause_on_hover" type="checkbox" <?php if ($custom['pause_on_hover'][0] == "true") {  echo 'checked="checked"';  } ?> />
								<a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Pause sliding when users hover slider</p>
									</div>
								</a>
							</li>
							<li>
							  <label for="autoplay"><?php _e("Autoplay"); ?>:</label>             
							  <input id="autoplay" size="20" name="autoplay" type="checkbox" <?php if ($custom['autoplay'][0] == "true") { ?> <?php echo 'checked="checked"'; ?> <?php } ?> />
							  <a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Start slider on page load</p>
									</div>
								</a>
							</li>
							<li>
							  <label for="stop_on_action"><?php _e("Stop on action"); ?>:</label>             
							  <input id="stop_on_action" size="20" name="stop_on_action" type="checkbox" <?php if ($custom['stop_on_action'][0] == "true") { ?> <?php echo 'checked="checked"'; ?> <?php } ?> />
							  <a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Stop sliding when users make any action on slider navigation</p>
									</div>
								</a>
							</li>							
							
						  </ul>
				 
						  <!--<input type="hidden" name="action" value="update" />
						  <input type="hidden" name="page_options" value="rh_transition,rh_duration"/>-->
				 
						  
						
					</div>
				</div>			
				
				<div class="inside">
					<h3>Navigation</h3>
					<div id="submitpost" class="submitbox">
						
						 <ul class="general_settings">	
							
							<li>
							  <label for="navigation_dots"><?php _e("Navigation dots"); ?>:</label>
								<?php		 
								  $selected = stripslashes($custom['navigation_dots'][0]);
								  if (empty($selected)) { $selected = 'inside_center'; }
								?>
								<select id="navigation_dots" name="navigation_dots">                  
								  <option <?php if ($selected =='inside_center') { echo 'selected="selected"'; } ?> value="inside_center">Inside center</option>				  
								  <option <?php if ($selected =='inside_left') { echo 'selected="selected"'; } ?> value="inside_left">Inside left</option>				  
								  <option <?php if ($selected =='inside_right') { echo 'selected="selected"'; } ?> value="inside_right">Inside right</option>				  
								  <option <?php if ($selected =='outside_center') { echo 'selected="selected"'; } ?> value="outside_center">Outside center</option>				  
								  <option <?php if ($selected =='outside_left') { echo 'selected="selected"'; } ?> value="outside_left">Outside left</option>				  
								  <option <?php if ($selected =='outside_right') { echo 'selected="selected"'; } ?> value="outside_right">Outside right</option>				  
								  <option <?php if ($selected =='hide') { echo 'selected="selected"'; } ?> value="hide">Hide</option>				  
								</select>
								<a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Position of navigation dots</p>
									</div>
								</a>								
							</li>					
							<li>
							  <label for="navigation_arrows"><?php _e("Navigation arrows"); ?>:</label>  
								<?php		 
								  $selected = stripslashes($custom['navigation_arrows'][0]);
								  if (empty($selected)) { $selected = 'dots_side'; }
								?>
								<select id="navigation_arrows" name="navigation_arrows">                  
								  <option <?php if ($selected =='dots_side') { echo 'selected="selected"'; } ?> value="dots_side">On side of dots</option>				  
								  <option <?php if ($selected =='slider_side') { echo 'selected="selected"'; } ?> value="slider_side">On side of slider</option>
								  <option <?php if ($selected =='hide') { echo 'selected="selected"'; } ?> value="hide">Hide</option>				  
								</select>	
								<a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Position of navigaion arrows</p>
									</div>
								</a>								
							</li>	
															
							<li>
								<label for="auto_hide_arrows"><?php _e("Mouseover show arrows"); ?>:</label> 								
								<input id="auto_hide_arrows" size="20" name="auto_hide_arrows" type="checkbox" <?php if ($custom['auto_hide_arrows'][0] == "true") { ?> <?php echo 'checked="checked"'; ?> <?php } ?> />
								<a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Hide navigation arrows, and show them only when users mouse over slider</p>
									</div>
								</a>
							</li>
							<li>
							  <label for="auto_hide_dots"><?php _e("Mouseover show dots"); ?>:</label>             
							  <input id="auto_hide_dots" size="20" name="auto_hide_dots" type="checkbox" <?php if ($custom['auto_hide_dots'][0] == "true") { ?> <?php echo 'checked="checked"'; ?> <?php } ?> />
							  <a href="#" class="info"><img src="<?php echo plugins_url('/images/info.png', __FILE__); ?>"/>
									<div class="info_text">
										<p>Hide navigation dots, and show them when users mauseover slider</p>
									</div>
								</a>
							</li>
							<li>
								<p style="text-align:right;"><span style="display:none;width:30px;box-shadow:none;margin-right:10px;position:relative;top:5px;" id="header-indicator"><img src="<?php echo plugins_url('/images/wpspin_light.gif', __FILE__); ?>"/></span><input type="submit" class="button-primary save_alcyone_slider" value="<?php _e('Save settings') ?>" /></p>
							</li>
						  </ul>
				 
						  <!--<input type="hidden" name="action" value="update" />
						  <input type="hidden" name="page_options" value="rh_transition,rh_duration"/>-->
				 
						 
								
					</div>
				</div>
			</div> <!-- end of div sidebar settings -->
			
		</form>	
	<div class="alcyone_logo">
			<a href="http://www.alcyone.hr" target="_blank"><img src="<?php echo plugins_url('/images/alcyone-logo.jpg', __FILE__); ?>" /> Alcyone Studio</a>
		  </div>		
		</div><!-- end of div #poststuff  -->
		
	</div>
</div>	
<div class="clear"></div>

<?php }