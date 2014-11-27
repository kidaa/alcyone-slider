<?php
/*
Plugin Name: Alcyone Slider
Plugin URI: http://www.alcyone.hr/	
Description: Easy to use Image slider
Author: Marko Kosmac
Version: 0.1
Author URI: http://www.alcyone.hr
Licence: GPL2
*/

// Include files *************************/
define( 'CD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once( CD_PLUGIN_PATH . 'widgets/widgets.php' );
require_once( CD_PLUGIN_PATH . 'functions/ajax-functions.php' );
require_once( CD_PLUGIN_PATH . '/slider_list.php' );
/**************************************************************************************************************************************************/

class AlcyoneSlider {

	function AlcyoneSlider() {
		
		add_action('wp_print_styles', array(&$this, "add_alcyone_slider_css"), 10); /* eniqueue Slider CSS */ 
		add_action('wp_enqueue_scripts',  array(&$this, "add_alcyone_slider_js"), 10 ); // wp_enqueue_scripts action hook to link only on the front-end

		add_action('init', array(&$this, 'register_items'));	
		add_action('init', array(&$this, 'alcyone_slider_generator'));	
		
		add_action('admin_menu', 'init_alcyone_slider_thickbox');
		
		add_action('admin_menu', array(&$this, 'alcyoneslider_admin_menu'));		
		add_action('admin_print_scripts', array(&$this,'admin_js_includes') );						
		add_action('admin_print_styles', array(&$this,'add_admin_alcyone_slider_css') );						
		add_action("admin_head", array(&$this, 'js'), 50);					
		add_action("admin_head", array(&$this, 'admin_css_includes'));							
		
	}
	/**********************************************************************************************************************************************/
	// Alcyone Style include **********************************************/ 
	function add_alcyone_slider_css() {
	  wp_enqueue_style('alcyone_slider_css', plugins_url('/alcyone-slider/css/slide.css'));	  	  
	}
	// Alcyone Admin Style include **********************************************/ 
	function add_admin_alcyone_slider_css() {	  
	  wp_enqueue_style('alcyone_slider_admin_css', plugins_url('/alcyone-slider/css/admin.css'));	  
	}
	/**********************************************************************************************************************************************/
	// Alcyone Javascrtipt include **********************************************/ 
	function add_alcyone_slider_js() {
	  wp_enqueue_script('alcyone_slider', plugins_url('/alcyone-slider/alcyone-slider.js'), array( 'jquery' ) );
	}
	/**********************************************************************************************************************************************/	
	// Admin JS included **********************************************/ 
	function admin_js_includes() {		
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_script('editor'); 
			wp_enqueue_script( 'jquery' ); 	
			wp_enqueue_script( 'jquery-ui-slider' ); 	 	
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-resizable' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'postbox' );			
			wp_enqueue_script('imgareaselect');
			wp_enqueue_style('imgareaselect');
			wp_enqueue_script('alcyone_slider', plugins_url('/alcyone-slider/alcyone-slider.js'), array( 'jquery' ) );		

			
	}
	/**********************************************************************************************************************************************/
	
	function js() {
		$step = alcyone_admin_step();
		if ( 1 == $step || 3 == $step )
			$this->js_1();
		elseif ( 2 == $step )
			$this->js_2();
	}
	/**********************************************************************************************************************************************/
	
	function admin_css_includes() {
		$step = alcyone_admin_step();

		if ( ( 1 == $step || 3 == $step ) ) {
			wp_enqueue_style('farbtastic');
			wp_enqueue_style('dl', plugins_url('/alcyone-slider/css/slide.css'));
		} elseif ( 2 == $step ) {
			wp_enqueue_style('imgareaselect');
		}
	}
	/**********************************************************************************************************************************************/
	
	function js_1() { 
	if (get_query_var('banner')) {
		$banner_id = get_query_var('banner');
		if ( empty($banner_id )) {
			$banner_id = 0;
		}
	}
	
	?>
	<script type="text/javascript">
		
		
		function checkUploadField( form )
		{
		   if (form.upload.value == "") {			
			form.upload.focus();
			return false ;
		  }
		  step_2();
		  jQuery("#upload-indicator").show();
		  // ** END **
		  return false ;
		}
		
	</script>
	<?php
	}
	/**********************************************************************************************************************************************/	
	
	function register_items() {
		$args = array(
			'labels' => array(
				'name' => __( 'Alcyone Slider' ),
				'singular_name' => __( 'Alcyone Slider') )
		);
		register_post_type( 'alcyoneslider', $args );
		

		

	}	
	function alcyone_slider_generator() {
		add_filter("mce_external_plugins", "add_alcyone_slider_shortcode");
		add_filter('mce_buttons', 'register_alcyone_slider_shortcode');
	}	
	
		
	function alcyoneslider_admin_menu() {		
		add_menu_page( 'Alcyone Slider', 'Alcyone Slider', 'edit_posts', 'alcyoneslider', 'alcyoneslider_admin_management_page' , 'div' );
		add_submenu_page( 'sliders', 'Edit Slider', 'Edit', 'edit_posts' , 'alcyoneslider', 'alcyoneslider_admin_management_page' );
	}
	
}
function add_alcyone_slider_shortcode($plugin_array) {
		$plugin_array['alcyoneSlider'] = plugins_url( 'functions/shortcode-generator.js' , __FILE__ ) ;
		return $plugin_array;
	}
	function register_alcyone_slider_shortcode($buttons) {
		array_push( $buttons, 'add_alcyone_slider_shortcode' ); 
		return $buttons;
	}
/*** end of CLASS Rotating header **************/
/**********************************************************************************************************************************************/		
/**********************************************************************************************************************************************/

function alcyoneslider_admin_management_page() {
	
	if (!isset( $_GET['banner'])) {	
		
		/*******************************************************************************************************************************/		
		$testListTable = new Alcyone_Slider_List_Table();			
		$testListTable->prepare_items();			
		?>
		<div class="wrap">
			
			<script language="javascript" type="text/javascript">
			 jQuery(document).ready(function(){ 	
				jQuery('#predifined_size').change(function(){
						str = jQuery(this).attr('value');
						var n=str.split("/"); 
						jQuery('#width').val(n[0]);
						jQuery('#height').val(n[1]);
					});
				jQuery('a.delete').click(function(e) {
					return confirm("Are you sure you want to delete this slider?");
				});			
			});
			function addNewSlider(){
				jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type: 'POST',
						data: {
						action: 'addNewSlider',
						height: jQuery("#height").attr("value"),
						width: jQuery("#width").attr("value")
						},
						dataType: 'html',
						success: function(response) {
						jQuery("#header-indicator").hide();						
						window.location = "?page=alcyoneslider&action=new&banner="+response;											
						}
					});
					jQuery("#header-indicator").show();
					return false;				
				}
			</script>	
			<div id="alcyone-icon" class="icon32"><img src="<?php echo plugins_url( 'images/favicon_32.png' , __FILE__ ) ; ?>"/></div>
				<h2>Alcyone Sliders <a class="button-primary  thickbox<?php if ( $unsaved ) echo ' current'; ?>" href="#TB_inline?height=320&width=380&inlineId=rban-size-select-modal" style="position:relative;top:-4px;"><?php echo esc_html('Add New'); ?></a></h2> 
				

			
			<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
			<form id="movies-filter" method="get">
				<!-- For plugins, we also need to ensure that the form posts back to our current page -->
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<!-- Now we can render the completed list table -->
				<?php $testListTable->display() ?>
			</form>
			
		</div>
		
		<div id="rban-size-select-modal" class="hidden">
			
			<h3>Select slider size</h3>		
					
			<form action="" method="post" class="create_new_alcyone_slider_modal" onsubmit="return addNewSlider(this);">
				<input type="hidden" name="page" value="rotating_banner" />
				<input type="hidden" name="banner" value="new" />
				<ul class="new_alcyone_slider_modal">
					<li>
						<label>Predefined</label><select name="predifined_size" id="predifined_size">
							<option value="" selected="selected">Select size</option>				
							<option value="900/300">Header W:900px/H:300px</option>
							<option value="960/300">Header W:960px/H:300px</option>
							<option value="1024/360">Header W:1024px/H:360px</option>
							<option value="320/300">Sidebar W:320px/H:300px</option>
							<option value="240/300">Sidebar W:240px/H:300px</option>
							<option value="180/240">Sidebar W:180px/H:240px</option>
						</select>
					</li>
					
					<li>
						<label>Width</label><input type="text" name="width" id="width" value="960" /><span> px</span><br/>
						<label>Height</label><input type="text" name="height" id="height" value="300" /><span> px</span>
					</li>
					<li>
						<label><span style="position:relative;display:none;width:30px;box-shadow:none;" id="header-indicator"><img src="<?php echo plugins_url('/images/loading.png', __FILE__); ?>" style="width:32px;margin:5px;float:left;"/></span></label><input type="submit" class="button save_alcyone" value="Create slider" />
					</li>
			</form>
		</div>
		<?php
	
		/**************************************************************************************************************************/
	} else {
	
		if ( $_GET['action'] == "delete" && current_user_can('edit_theme_options'))  {
			wp_delete_post( $_GET['banner']);
			$redir = get_option('siteurl').'/wp-admin/admin.php?page=alcyoneslider&settings-updated=true';
			?>
			<script>
				window.location="<?php echo $redir ?>";
			</script>
			<?php
		} else {
		
			require_once( plugin_dir_path( __FILE__ ) . 'edit_slider.php' );
			
			alcyone_slider_edit_page();
	
		}
		/**********************************************************************************************************************************************/
	}
}
/**********************************************************************************************************************************************/	


/**********************************************************************************************************************************************/	
	function alcyone_admin_step() {
		if ( ! isset( $_GET['admin_step'] ) )
			return 1;

		$step = (int) $_GET['admin_step'];
		if ( $step < 1 || 3 < $step )
			$step = 1;

		return $step;
	}
/**********************************************************************************************************************************************/
	function alcyone_slider_edit_page() {
		if ( ! current_user_can('edit_theme_options') )
			wp_die(__('You do not have permission to customize rotating headers.'));
		
		$step = alcyone_admin_step();
		if ( 1 == $step )
			step_1($_GET['banner']);
		elseif ( 2 == $step )
			step_2($_GET['banner']);
		elseif ( 3 == $step )
			step_3($_GET['banner']);
		
	}
/**********************************************************************************************************************************************/



function init_alcyone_slider_thickbox() {
   add_thickbox();
}
/**********************************************************************************************************************************************/
function alcyone_slider_draw($attributes) {
 extract(shortcode_atts(array(
		'id' => ''
	), $attributes));   
	
	global $wpdb;	
	$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $id . "'", 'ARRAY_A');
	if($post_exists){
	
	$custom = get_post_custom($id);
	$banner_height = $custom['height'][0];	
	$banner_width = $custom['width'][0];
	$slider_transition = stripslashes($custom['slider_transition'][0]);
	$slider_duration = stripslashes($custom['slider_duration'][0]);
	$slider_transition_type = stripslashes($custom['slider_transition_type'][0]);
	
	
	$navigation_skin = stripslashes($custom['navigation_skin'][0]);
	$navigation_dots = stripslashes($custom['navigation_dots'][0]);
	$navigation_arrows = stripslashes($custom['navigation_arrows'][0]);
		
	if ($custom['pause_on_hover'][0] == "true" ) { $pause_on_hover = "true"; } 
	if ($custom['autoplay'][0] == "true" ) { $autoplay = "true"; } 
	if ($custom['stop_on_action'][0] == "true" ) { $stop_on_action = "true"; }
	if ($custom['auto_hide_arrows'][0] == "true" ) { $auto_hide_arrows = "true"; $arrows_visibility = "show_on_hover";}
	if ($custom['auto_hide_dots'][0] == "true" ) { $auto_hide_dots = "true"; $dots_visibility = "show_on_hover";}
	
	
	$active = get_post_meta($id, 'active_headers');
	$rel_size = ($banner_height/2)-20;
	$output = '
  
	<style>			
		#rslider_'. $id.' {			
			min-height: 10px;
			max-width:100%;			
			}
		.slices {max-width:100%;overflow:hidden;}
		#rslider_'. $id.' #slides {
			height:auto;
			overflow:hidden;
			position:relative;
			max-width:100%;			
			}
		#rslider_'. $id.' .rotating-slider {
			display: block;			
			max-height: 100%;
			margin: 0;
			padding: 0;
			max-width: 100% !important;}
		#rslider_'. $id.' .rotating_slides {
			display: block;
			height:auto;
			margin: 0;
			max-height: 100%;
			padding: 0;
			width: 100%;
		}
		#rslider_'. $id.' .slider_side {
			top: '. $rel_size .'px;			
		}
		#rslider_'. $id.' .rotating_slides li {position:absolute;margin:0px; padding:0px;width:auto;height:auto;}
		#rslider_'. $id.' .rotating_slides li img {width:auto;height:auto;}
	</style>
	
  <div class="rotating_slider alcyone_slider" id="rslider_'. $id.'">
	<div class="rotating-slider">';
    if (!empty($active[0])) {
	$output .= '<div class="click">	<div id="slides">
    <ul class="rotating_slides">';
		
			$a = 0;
			foreach ($active[0] as $slide) { 
				$slide_id = $slide['slide_id'];				
				$image_id = $custom[$slide_id][0];						
				$url= wp_get_attachment_url($image_id);
				
				/*
				if (!empty($url['url']) && $url['add_box'] == "link") {$slide_link = "onClick=\"location.href='".$url['url']."';return false;\"";}
				elseif (!empty($url['custom_url']) && $url['add_box'] == "custom") {$slide_link = "onClick=\"location.href='".$url['custom_url']."';return false;\"";}
				elseif ($url['add_box'] == "post") {$slide_link = "onClick=\"location.href='".get_permalink($url['post_id'])."';return false;\"";}				
				else {$slide_link ="";}
				*/
			$output .= '<li id="content-'. $a.'" '. $slide_link.'>
				<img src="'. $url.'"/>';		  				
				
					
					$captions = get_post_meta($id, $slide_id.'_captions');
					$custom = get_post_custom($id);
					
					
					$i=0;
					$loop_count = 0;
					$loop = true;
					while ($loop) {					
						$i++; 
						if ($custom[$slide_id.'_caption_'.$i][0]) {
							
							$opacity = $custom[$slide_id.'_caption_'.$i.'_opacity'][0];
							if (empty($opacity) && $opacity != 0 ) {$opacity = 0.5;}
							$bg_color = $custom[$slide_id.'_caption_'.$i.'_bg_color'][0];
							if (empty($bg_color)) {$bg_color = "#000"; }
							$font_color = $custom[$slide_id.'_caption_'.$i.'_font_color'][0];
							if (empty($font_color)) {$font_color = "#fff"; }												
							
								$top = $custom[$slide_id.'_caption_'.$i.'_pos_y'][0] / $banner_height * 100;
								$left =  $custom[$slide_id.'_caption_'.$i.'_pos_x'][0] / $banner_width * 100; 
								$message_width =  $custom[$slide_id.'_caption_'.$i.'_element_width'][0] / $banner_width * 100; 
								$message_height = $custom[$slide_id.'_caption_'.$i.'_element_height'][0] / $banner_height * 100;
						$output .= '<div class="message" id="elementResizable_'. $slide_id.'_caption_'.$i.'" style="top:'. $top .'%;left:'. $left.'%;width:'. $message_width.'%;height:'. $message_height.'%;">
								<div class="bg_color" style="position:absolute;background:'. $bg_color.';opacity:'. $opacity.';width:100%;height:100%;"></div>
								<div style="color:'. $font_color.';position:absolute;">
									'. apply_filters( 'the_content', $custom[$slide_id.'_caption_'.$i.'_custom_text'][0]).'											
								</div>																		
							</div>';	
						
							$loop_count++;
							if ($loop_count == $captions) {
								$loop = false;
							}
						}	
						if($i>100) {
							$loop = false;
						}
					}	
		
				
			$output .= '</li>';
			$a++; } 
    $output .= '</ul></div>';	if ($a == 1) {$navigation_dots = "hide";}
	$output .= '<div class="slices"></div>
	</div>';
		
		if ($navigation_arrows == "dots_side") {
			$output .= '<div class="banner_nav '. $navigation_skin.' '. $navigation_dots.'">
			<div id="previousSlide" class="'. $arrows_visibility.'" ><a href="#"></a></div>
			<div class="dots_custom" id="pager"></div>
			<div id="nextSlide"  class="'. $arrows_visibility.'"><a href="#"></a></div>			
		</div>';
		} else {
		$output .= '<div class="banner_nav '. $navigation_skin.' '. $navigation_dots.'">	
			<div class="dots_custom" id="pager"></div>			
		</div>';
		if ($navigation_arrows == "slider_side") {
				$output .= '<div id="previousSlide" class="slider_side '. $arrows_visibility.'"><a href="#"></a></div>
				<div id="nextSlide" class="slider_side '. $arrows_visibility.'"><a href="#"></a></div>';
		} 
		}
    }
	
	$output .= '</div> 
	
	
  </div>    
  <script type="text/javascript">
	$jq = jQuery.noConflict();	
	
	id = '. $id.';
	duration = '. $slider_duration.'*1000;
	transition = '. $slider_transition.' * 1000;		
	type = "'. $slider_transition_type.'";
	height = "'. $banner_height.'";
	width = "'. $banner_width.'";	
	arrows = "'. $navigation_arrows.'";
	dots = "'. $navigation_dots.'";	
	pause = "'. $pause_on_hover.'";
	autoplay = "'. $autoplay.'";
	stop = "'. $stop_on_action.'";
	hide_arrows = "'. $auto_hide_arrows.'";
	hide_dots = "'. $auto_hide_dots.'";
	
	if(document.getElementById("response") == null){
		scale = width/height;	
		slider_width = $jq("#rslider_'. $id.'").width();	
		if (slider_width <= width) {		
			new_height = slider_width/scale ;		
			height=new_height;		
			width=slider_width;				
		}
	}
	
	
	$jq("#rslider_'. $id.'").css({"width" : width});
	$jq("#rslider_'. $id.'  .rotating_slides").css({"height" : height});
	$jq("#rslider_'. $id.'  #slides").css({"height" : height});
	$jq("#rslider_'. $id.'  #slides img").css({"height" : height, "width" : width, maxWidth : width+"px"});
	$jq("#rslider_'. $id.'  .slider_side").css({"top" : (height/2-20)+"px"});		
		
	var slider_'. $id.' = new alcyoneSlider( id, duration, transition, type, height, width, arrows, dots, pause, autoplay, stop, hide_arrows, hide_dots );
			
	$jq(document).ready(function(){ 
		$jq("#rslider_'. $id.'  .more, #rslider_'. $id.'  .rotating_slides li").click(function(event) {			
			clearInterval(slider_'. $id.'.t);	
		});
		$jq("#rslider_'. $id.'  #pager a").click(function(event) {
			event.preventDefault();
			clearInterval(slider_'. $id.'.t);	
			new_i= $jq(this).text();
			new_i = parseInt(new_i)-1;									
			if (slider_'. $id.'.stop && slider_'. $id.'.timer_is_on){slider_'. $id.'.stopTimer(new_i);}	
			else {slider_'. $id.'.timedCount(new_i);		}
		});	
		$jq("#rslider_'. $id.' #previousSlide a").click(function(event) {
			event.preventDefault();
			clearInterval(slider_'. $id.'.t);				
			if (slider_'. $id.'.i==0) { new_i = slider_'. $id.'.divs.length-1; } else {new_i = slider_'. $id.'.i - 1;}			
			if (slider_'. $id.'.stop && slider_'. $id.'.timer_is_on){slider_'. $id.'.stopTimer(new_i);}	
			else {slider_'. $id.'.timedCount(new_i);		}
		});
		$jq("#rslider_'. $id.' #nextSlide a").click(function(event){		
			event.preventDefault();
			clearInterval(slider_'. $id.'.t);			
			new_i = slider_'. $id.'.i + 1;			
			if (slider_'. $id.'.stop && slider_'. $id.'.timer_is_on){slider_'. $id.'.stopTimer(new_i);}	
			else {slider_'. $id.'.timedCount(new_i);		}
		});
	
		$jq("#rslider_'. $id.' .rotating-slider").hover(
			function () {
				if (hide_arrows) { $jq("#rslider_'. $id.' #previousSlide a, #rslider_'. $id.' #nextSlide a").stop().animate({opacity: 1});}
				if (hide_dots) {$jq("#rslider_'. $id.' .dots_custom").stop().animate({opacity: 1});}
				if (slider_'. $id.'.str_pause && slider_'. $id.'.timer_is_on){slider_'. $id.'.pause();}					
			},
			function () {
				if (hide_arrows) {$jq("#rslider_'. $id.' #previousSlide a, #rslider_'. $id.' #nextSlide a").stop().animate({opacity: 0});	}
				if (hide_dots) {$jq("#rslider_'. $id.' .dots_custom").stop().animate({opacity: 0});}
				if (slider_'. $id.'.str_pause && slider_'. $id.'.timer_is_on){slider_'. $id.'.resume();}	
			}
			
		);
	
	});
</script>';
  
  
   
	return $output;

 }
}
	
add_shortcode('alcyone_slider', 'alcyone_slider_draw');

function create_alcyone_slider() {
	$alcyone_slider = new AlcyoneSlider();
}
$alcyone_slider = add_action( 'plugins_loaded', "create_alcyone_slider");




	
?>
