<?php /*
Plugin Name: Pointelle Slider
Plugin URI: 
Description: Pointelle Slider adds a beautiful featured posts/pages/images slideshow with Fade or sliding effect and vertically sliding pointers to the slides to any location of your blog.
Version: 1.6
Author: Tejaswini Deshpande
Author URI: http://slidervilla.com/
Wordpress version supported: 3.0 and above
*/
/*  Copyright 2010-2013  Slider Villa  (email : tedeshpa@gmail.com)
*/
//defined global variables and constants here
global $pointelle_slider,$default_pointelle_slider_settings;
$pointelle_slider = get_option('pointelle_slider_options');
$default_pointelle_slider_settings = array('speed'=>'10', 
                           'pause'=>'4',
	                       'no_posts'=>'3',
						   'transition'=>'fade',
						   'width'=>'900',
						   'height'=>'299',
						   'bg_color'=>'#000000',
						   'bg'=>'0',						   
						   'img_pick'=>array('1','slider_thumbnail','1','1','1','0'), //use custom field/key, name of the key, use post featured image, pick the image attachment, attachment order,scan images
						   'img_align'=>'left',
						   'img_width'=>'600',
						   'img_height'=>'299',
						   'img_border'=>'0',
						   'img_brcolor'=>'#D8E7EE',
						   'image_only'=>'0',
						   'hovercontent'=>'0',
						   'copyprotect'=>'0',
						   'ptitle_font'=>'Trebuchet MS,sans-serif',
						   'ptitle_fsize'=>'18',
						   'ptitle_fstyle'=>'normal',
						   'ptitle_fcolor'=>'#ffffff',
						   'show_title'=>'1',
						   'content_font'=>'Arial,Helvetica,sans-serif',
						   'content_fsize'=>'13',
						   'content_fstyle'=>'normal',
						   'content_fcolor'=>'#ffffff',
						   'content_from'=>'content',
						   'content_chars'=>'',
						   'content_limit'=>'22',
						   'show_content'=>'1',
						   'nav_control_w'=>'300',
						   'nav_control_h'=>'299',
						   'nav_bg_color' => '#fafafa',
						   'nav_bg' => '0',
                           'nav_brcolor' => '#cecece',
                           'nav_img_width' => '70',
						   'nav_img_height' => '70',
						   'nav_img_border' => '1',
						   'nav_img_brcolor' => '#F6F6F6',
						   'nav_title_font' => 'Arial,Helvetica,sans-serif',
						   'nav_title_fcolor' => '#666',
						   'nav_title_fsize' => '13',
						   'nav_title_fstyle' => 'bold',
						   'meta_title_font' => 'Georgia,serif',
						   'meta_title_fcolor' => '#a6a6a6',
						   'meta_title_fsize' => '11',
						   'meta_title_fstyle' => 'normal',
						   'meta1_fn' => 'pointelle_get_slide_author',
						   'meta1_parms' => 'field=display_name',
						   'meta1_before' => 'Posted by ',
						   'meta1_after' => ' ',
						   'meta2_fn' => 'pointelle_get_slide_pub_date',
						   'meta2_parms' => 'format=M j, Y',
						   'meta2_before' => 'on ',
						   'meta2_after' => '',
						   'disable_thumbs'=>'0',
						   'disable_navtext'=>'0',
						   'disable_nav'=>'0',
						   'allowable_tags'=>'',
						   'readmore'=>'',
						   'user_level'=>'edit_others_posts',
						   'custom_nav'=>'',
						   'crop'=>'0',
						   'multiple_sliders'=>'1',
						   'slide_nav_limit'=>'5',
						   'stylesheet'=>'default',
						   'shortcode'=>'1',
						   'rand'=>'0',
						   'ver'=>'1',
						   'support'=>'1',
						   'rtl'=>'0',
						   'fouc'=>'0',
						   'timthumb'=>'0',
						   'navpos'=>'1',
						   'custom_post'=>'0',
						   'preview'=>'2',
						   'slider_id'=>'1',
						   'catg_slug'=>'',
						   'css'=>'',
						   'onhover'=>'0',
						   'autoslide'=>'1',
						   'setname'=>'Set',
						   'nextprev'=>'1',
						   'scroll_nav_posts'=>'3',
						   'fields'=>'',
						   'a_attr'=>'',
						   'pphoto'=>'0',
						   'responsive'=>'0',
						   'disable_preview'=>'0',
						   'remove_metabox'=>array(),
						   'w_241'=>'175',
						   'w_320'=>'255',
						   'w_400'=>'315',
						   'w_480'=>'415',
						   'w_568'=>'500',
						   'w_685'=>'620',
						   'w_768'=>'700',
						   'w_960'=>'800',
						   'noscript'=>'This page is having a slideshow that uses Javascript. Your browser either doesn\'t support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.'
			              );
$default_pointelle_slider_settings=apply_filters('default_pointelle_slider_settings',$default_pointelle_slider_settings);
define('POINTELLE_SLIDER_TABLE','pointelle_slider'); //Slider TABLE NAME
define('POINTELLE_SLIDER_META','pointelle_slider_meta'); //Meta TABLE NAME
define('POINTELLE_SLIDER_POST_META','pointelle_slider_postmeta'); //Meta TABLE NAME
define('POINTELLE_SLIDER_VER','1.6',false);//Current Version of Pointelle Slider
if ( ! defined( 'POINTELLE_SLIDER_PLUGIN_BASENAME' ) )
	define( 'POINTELLE_SLIDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'POINTELLE_SLIDER_CSS_DIR' ) ){
	define( 'POINTELLE_SLIDER_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/skins/' );
}
// Create Text Domain For Translations
load_plugin_textdomain('pointelle-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

function install_pointelle_slider() {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.POINTELLE_SLIDER_TABLE;
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
					id int(5) NOT NULL AUTO_INCREMENT,
					post_id int(11) NOT NULL,
					date datetime NOT NULL,
					slider_id int(5) NOT NULL DEFAULT '1',
					slide_order int(5) NOT NULL DEFAULT '0',
					UNIQUE KEY id(id)
				);";
		$rs = $wpdb->query($sql);
	}

   	$meta_table_name = $table_prefix.POINTELLE_SLIDER_META;
	if($wpdb->get_var("show tables like '$meta_table_name'") != $meta_table_name) {
		$sql = "CREATE TABLE $meta_table_name (
					slider_id int(5) NOT NULL AUTO_INCREMENT,
					slider_name varchar(100) NOT NULL default '',
					UNIQUE KEY slider_id(slider_id)
				);";
		$rs2 = $wpdb->query($sql);
		
		$sql = "INSERT INTO $meta_table_name (slider_id,slider_name) VALUES('1','Pointelle Slider');";
		$rs3 = $wpdb->query($sql);
	}
	
	$slider_postmeta = $table_prefix.POINTELLE_SLIDER_POST_META;
	if($wpdb->get_var("show tables like '$slider_postmeta'") != $slider_postmeta) {
		$sql = "CREATE TABLE $slider_postmeta (
					post_id int(11) NOT NULL,
					slider_id int(5) NOT NULL default '1',
					UNIQUE KEY post_id(post_id)
				);";
		$rs4 = $wpdb->query($sql);
	}
   // Need to delete the previously created options in old versions and create only one option field for Pointelle Slider
   $default_slider = array();
   global $default_pointelle_slider_settings;
   $default_slider = $default_pointelle_slider_settings;
   
   	   $default_scounter='1';
	   $scounter=get_option('pointelle_slider_scounter');
	   if(!isset($scounter) or $scounter=='' or empty($scounter)){
	      update_option('pointelle_slider_scounter',$default_scounter);
		  $scounter=$default_scounter;
	   }
	   
	   for($i=1;$i<=$scounter;$i++){
	       if ($i==1){
		    $pointelle_slider_options='pointelle_slider_options';
		   }
		   else{
		    $pointelle_slider_options='pointelle_slider_options'.$i;
		   }
		   $pointelle_slider_curr=get_option($pointelle_slider_options);
	   				 
		   if(!$pointelle_slider_curr and $i==1) {
			 $pointelle_slider_curr = array();
		   }
		
		   if($pointelle_slider_curr or $i==1) {
			   foreach($default_slider as $key=>$value) {
				  if(!isset($pointelle_slider_curr[$key])) {
					 if( !isset($pointelle_slider_curr['scroll_nav_posts']) and isset($pointelle_slider_curr['no_posts']) ) {
						$pointelle_slider_curr['scroll_nav_posts']=$pointelle_slider_curr['no_posts'];
					 }
					 $pointelle_slider_curr[$key] = $value;
				  }
			   }
			   delete_option($pointelle_slider_options);	  
			   update_option($pointelle_slider_options,$pointelle_slider_curr);
		   }
	   } //end for loop
}
register_activation_hook( __FILE__, 'install_pointelle_slider' );
require_once (dirname (__FILE__) . '/includes/pointelle-slider-functions.php');
require_once (dirname (__FILE__) . '/includes/pointelle-slider-meta-functions.php');
require_once (dirname (__FILE__) . '/includes/sslider-get-the-image-functions.php');

//This adds the post to the slider
function pointelle_add_to_slider($post_id) {
global $pointelle_slider;
 if(isset($_POST['pointelle-sldr-verify']) and current_user_can( $pointelle_slider['user_level'] ) ) {
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.POINTELLE_SLIDER_TABLE;
	
	if(isset($_POST['pointelle-slider']) and !isset($_POST['pointelle_slider_name'])) {
	  $slider_id = '1';
	  if(is_post_on_any_pointelle_slider($post_id)){
	     $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	  }
	  
	  if(isset($_POST['pointelle-slider']) and $_POST['pointelle-slider'] == "pointelle-slider" and !pointelle_slider($post_id,$slider_id)) {
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
	  }
	}
	if(isset($_POST['pointelle-slider']) and $_POST['pointelle-slider'] == "pointelle-slider" and isset($_POST['pointelle_slider_name'])){
	  $slider_id_arr = $_POST['pointelle_slider_name'];
	  $post_sliders_data = pointelle_ss_get_post_sliders($post_id);
	  
	  foreach($post_sliders_data as $post_slider_data){
		if(!in_array($post_slider_data['slider_id'],$slider_id_arr)) {
		  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		  $wpdb->query($sql);
		}
	  }

		foreach($slider_id_arr as $slider_id) {
			if(!pointelle_slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			}
		}
	}
	
	$table_name = $table_prefix.POINTELLE_SLIDER_POST_META;
	if(isset($_POST['pointelle_display_slider']) and !isset($_POST['pointelle_display_slider_name'])) {
	  $slider_id = '1';
	}
	if(isset($_POST['pointelle_display_slider']) and isset($_POST['pointelle_display_slider_name'])){
	  $slider_id = $_POST['pointelle_display_slider_name'];
	}
  	if(isset($_POST['pointelle_display_slider'])){	
		  if(!pointelle_ss_post_on_slider($post_id,$slider_id)) {
		    $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
			$sql = "INSERT INTO $table_name (post_id, slider_id) VALUES ('$post_id', '$slider_id')";
			$wpdb->query($sql);
		  }
	}
	$pointelle_slider_style = get_post_meta($post_id,'_pointelle_slider_style',true);
	$post_pointelle_slider_style=$_POST['_pointelle_slider_style'];
	if($pointelle_slider_style != $post_pointelle_slider_style and isset($post_pointelle_slider_style) and !empty($post_pointelle_slider_style)) {
	  update_post_meta($post_id, '_pointelle_slider_style', $_POST['_pointelle_slider_style']);	
	}
	
	$thumbnail_key = $pointelle_slider['img_pick'][1];
	$pointelle_sslider_thumbnail = get_post_meta($post_id,$thumbnail_key,true);
	$post_slider_thumbnail=$_POST['pointelle_sslider_thumbnail'];
	if($pointelle_sslider_thumbnail != $post_slider_thumbnail) {
	  update_post_meta($post_id, $thumbnail_key, $_POST['pointelle_sslider_thumbnail']);	
	}
	
	$pointelle_link_attr = get_post_meta($post_id,'pointelle_link_attr',true);
	$link_attr=htmlentities($_POST['pointelle_link_attr'],ENT_QUOTES);
	if($pointelle_link_attr != $link_attr) {
	  update_post_meta($post_id, 'pointelle_link_attr', $link_attr);	
	}
	
	$pointelle_sslider_link = get_post_meta($post_id,'pointelle_slide_redirect_url',true);
	$link=$_POST['pointelle_sslider_link'];
	if($pointelle_sslider_link != $link) {
	  update_post_meta($post_id, 'pointelle_slide_redirect_url', $link);	
	}
	
	$pointelle_sslider_nolink = get_post_meta($post_id,'pointelle_sslider_nolink',true);
	$post_pointelle_sslider_nolink = $_POST['pointelle_sslider_nolink'];
	if($pointelle_sslider_nolink != $post_pointelle_sslider_nolink) {
	  update_post_meta($post_id, 'pointelle_sslider_nolink', $_POST['pointelle_sslider_nolink']);	
	}
	
	$pointelle_sslider_slide_nav = get_post_meta($post_id,'slide_nav',true);
	$slide_nav=$_POST['pointelle_sslider_slide_nav'];
	if( $pointelle_sslider_slide_nav != $slide_nav ) {
	  update_post_meta($post_id, 'slide_nav', $slide_nav);	
	}
	
	$pointelle_sslider_nav_thumb = get_post_meta($post_id,'nav_thumb',true);
	$nav_thumb=$_POST['pointelle_sslider_nav_thumb'];
	if($pointelle_sslider_nav_thumb != $nav_thumb) {
	  update_post_meta($post_id, 'nav_thumb', $nav_thumb);	
	}
	
  } //pointelle-sldr-verify
}

//Removes the post from the slider, if you uncheck the checkbox from the edit post screen
function pointelle_remove_from_slider($post_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.POINTELLE_SLIDER_TABLE;
	
	// authorization
	if (!current_user_can('edit_post', $post_id))
		return $post_id;
	// origination and intention
	if (!wp_verify_nonce($_POST['pointelle-sldr-verify'], 'PointelleSlider'))
		return $post_id;
	
    if(empty($_POST['pointelle-slider']) and is_post_on_any_pointelle_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	
	$display_slider = $_POST['pointelle_display_slider'];
	$table_name = $table_prefix.POINTELLE_SLIDER_POST_META;
	if(empty($display_slider) and pointelle_ss_slider_on_this_post($post_id)){
	  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
	}
} 
  
function pointelle_delete_from_slider_table($post_id){
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.POINTELLE_SLIDER_TABLE;
    if(is_post_on_any_pointelle_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	$table_name = $table_prefix.POINTELLE_SLIDER_POST_META;
    if(pointelle_ss_slider_on_this_post($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
}

// Slider checkbox on the admin page

function pointelle_slider_edit_custom_box(){
   pointelle_add_to_slider_checkbox();
}

function pointelle_slider_add_custom_box() {
 global $pointelle_slider;
 if (current_user_can( $pointelle_slider['user_level'] )) {
	if( function_exists( 'add_meta_box' ) ) {
	    $post_types=get_post_types(); 
		$remove_post_type_arr=$pointelle_slider['remove_metabox'];
		if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		foreach($post_types as $post_type) {
			if(!in_array($post_type,$remove_post_type_arr)){
				add_meta_box( 'pointelle_slider_box', __( 'Pointelle Slider' , 'pointelle-slider'), 'pointelle_slider_edit_custom_box', $post_type, 'advanced' );
			}
		}
		//add_meta_box( $id,   $title,     $callback,   $page, $context, $priority ); 
	} 
 }
}
/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'pointelle_slider_add_custom_box');

function pointelle_add_to_slider_checkbox() {
	global $post, $pointelle_slider;
	if (current_user_can( $pointelle_slider['user_level'] )) {
		$extra = "";
		
		$post_id = $post->ID;
		
		if(isset($post->ID)) {
			$post_id = $post->ID;
			if(is_post_on_any_pointelle_slider($post_id)) { $extra = 'checked="checked"'; }
		} 
		
		$post_slider_arr = array();
		
		$post_sliders = pointelle_ss_get_post_sliders($post_id);
		if($post_sliders) {
			foreach($post_sliders as $post_slider){
			   $post_slider_arr[] = $post_slider['slider_id'];
			}
		}
		
		$sliders = pointelle_ss_get_sliders();
?>
		<div class="slider_checkbox">
		<table class="form-table">
				
				<tr valign="top">
				<th scope="row"><input type="checkbox" class="sldr_post" name="pointelle-slider" value="pointelle-slider" <?php echo $extra;?> />
				<label for="pointelle-slider"><?php _e('Add this post/page to','pointelle-slider'); ?> </label></th>
				<td><select name="pointelle_slider_name[]" multiple="multiple" size="3" >
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(in_array($slider['slider_id'],$post_slider_arr)){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select>
				<input type="hidden" name="pointelle-sldr-verify" id="pointelle-sldr-verify" value="<?php echo wp_create_nonce('PointelleSlider');?>" />
				</td>
				</tr>
                
         <?php if($pointelle_slider['multiple_sliders'] == '1') {?>
                <tr valign="top">
				<th scope="row">				
				<label for="pointelle_display_slider"><?php _e('Display ','pointelle-slider'); ?></label>
				<select name="pointelle_display_slider_name">
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(pointelle_ss_post_on_slider($post_id,$slider['slider_id'])){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select> 
				<?php _e('on this Post/Page','pointelle-slider'); ?></th>
				<td><input type="checkbox" class="sldr_post" name="pointelle_display_slider" value="1" <?php if(pointelle_ss_slider_on_this_post($post_id)){echo "checked";}?> /> 
				<?php _e('(Add the ','pointelle-slider'); ?><a href="http://guides.slidervilla.com/pointelle-slider/template-tags/simple-template-tag/" target="_blank"><?php _e('Pointelle Slider template tag','pointelle-slider'); ?></a> <?php _e('manually on your page.php/single.php or another page template file)','pointelle-slider'); ?></td>
				</tr>
          <?php } ?>
	    </div>
        <div>
        <?php
        $pointelle_slider_style = get_post_meta($post->ID,'_pointelle_slider_style',true);
        ?>
         <tr valign="top">
		 <th scope="row"><label for="_pointelle_slider_style"><?php _e('Stylesheet to use if slider is displayed on this Post/Page','pointelle-slider'); ?> </label></th>
		 <td><select name="_pointelle_slider_style" >
			<?php 
            $directory = POINTELLE_SLIDER_CSS_DIR;
            if ($handle = opendir($directory)) {
                while (false !== ($file = readdir($handle))) { 
                 if($file != '.' and $file != '..') { ?>
                  <option value="<?php echo $file;?>" <?php if (($pointelle_slider_style == $file) or (empty($pointelle_slider_style) and $pointelle_slider['stylesheet'] == $file)){ echo "selected";}?> ><?php echo $file;?></option>
             <?php  } }
                closedir($handle);
            }
            ?>
        </select></td>
		</tr>
        
  <?php         $thumbnail_key = $pointelle_slider['img_pick'][1];
                $pointelle_sslider_thumbnail= get_post_meta($post_id, $thumbnail_key, true); 
				$pointelle_sslider_link= get_post_meta($post_id, 'pointelle_slide_redirect_url', true);  
				$pointelle_sslider_nolink=get_post_meta($post_id, 'pointelle_sslider_nolink', true);
				$pointelle_link_attr=get_post_meta($post_id, 'pointelle_link_attr', true);
				$pointelle_sslider_slide_nav=get_post_meta($post_id, 'slide_nav', true);
				$pointelle_sslider_nav_thumb=get_post_meta($post_id, 'nav_thumb', true);
  ?>
				<tr valign="top">
				<th scope="row"><label for="pointelle_sslider_thumbnail"><?php _e('Custom Thumbnail Image(url)','pointelle-slider'); ?></label></th>
                <td><input type="text" name="pointelle_sslider_thumbnail" class="pointelle_sslider_thumbnail" value="<?php echo $pointelle_sslider_thumbnail;?>" size="50" /></td>
				</tr>
				
				<tr valign="top">
                <th scope="row"><label for="pointelle_link_attr"><?php _e('Slide Link (anchor) attributes ','pointelle-slider'); ?></label></th>
                <td><input type="text" name="pointelle_link_attr" class="pointelle_link_attr" value="<?php echo $pointelle_link_attr;?>" size="50" /><small><?php _e('e.g. target="_blank" rel="external nofollow"','pointelle-slider'); ?></small></td>
				</tr>
				
				<tr valign="top">
                <th scope="row"><label for="pointelle_sslider_link"><?php _e('Slide Link URL ','pointelle-slider'); ?></label></th>
                <td><input type="text" name="pointelle_sslider_link" class="pointelle_sslider_link" value="<?php echo $pointelle_sslider_link;?>" size="50" /><small><?php _e('If left empty, it will be by default linked to the permalink.','pointelle-slider'); ?></small> </td>
				</tr>
				
                <tr valign="top">
				<th scope="row"><label for="pointelle_sslider_nolink"><?php _e('Do not link this slide to any page(url)','pointelle-slider'); ?> </label></th>
                <td><input type="checkbox" name="pointelle_sslider_nolink" class="pointelle_sslider_nolink" value="1" <?php if($pointelle_sslider_nolink=='1'){echo "checked";}?>  /></td>
				</tr>
				
				<tr valign="top">
                <th scope="row"><label for="pointelle_sslider_slide_nav"><?php _e('Slide Navigation Title ','pointelle-slider'); ?></label></th>
                <td><input type="text" name="pointelle_sslider_slide_nav" class="pointelle_sslider_slide_nav" value="<?php echo $pointelle_sslider_slide_nav;?>" size="50" /><small><?php _e(' (optional)','pointelle-slider'); ?></small></td>
				</tr>
				
				<tr valign="top">
                <th scope="row"><label for="pointelle_sslider_nav_thumb"><?php _e('Slide Navigation Thumb URL ','pointelle-slider'); ?></label></th>
                <td><input type="text" name="pointelle_sslider_nav_thumb" class="pointelle_sslider_nav_thumb" value="<?php echo $pointelle_sslider_nav_thumb;?>" size="50" /><small><?php _e(' (optional)','pointelle-slider'); ?></small></td>
				</tr>
				
				</table>
				
                 </div>
<?php }
}

//CSS for the checkbox on the admin page
function pointelle_slider_checkbox_css() {
?><style type="text/css" media="screen">.slider_checkbox{margin: 5px 0 10px 0;padding:3px;font-weight:bold;}.slider_checkbox input,.slider_checkbox select{font-weight:bold;}.slider_checkbox label,.slider_checkbox input,.slider_checkbox select{vertical-align:top;}</style>
<?php
}

add_action('publish_post', 'pointelle_add_to_slider');
add_action('publish_page', 'pointelle_add_to_slider');
add_action('edit_post', 'pointelle_add_to_slider');
add_action('publish_post', 'pointelle_remove_from_slider');
add_action('edit_post', 'pointelle_remove_from_slider');
add_action('deleted_post','pointelle_delete_from_slider_table');

add_action('edit_attachment', 'pointelle_add_to_slider');
add_action('delete_attachment','pointelle_delete_from_slider_table');

function pointelle_slider_plugin_url( $path = '' ) {
	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) { // Using WordPress 2.7
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}

function pointelle_get_string_limit($output, $max_char)
{
    $output = str_replace(']]>', ']]&gt;', $output);
    $output = strip_tags($output);

  	if ((strlen($output)>$max_char) && ($espacio = strpos($output, " ", $max_char )))
	{
        $output = substr($output, 0, $espacio).'...';
		return $output;
   }
   else
   {
      return $output;
   }
}

function pointelle_slider_get_first_image($post) {
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];
	return $first_img;
}
add_filter( 'plugin_action_links', 'pointelle_sslider_plugin_action_links', 10, 2 );

function pointelle_sslider_plugin_action_links( $links, $file ) {
	if ( $file != POINTELLE_SLIDER_PLUGIN_BASENAME )
		return $links;

	$url = pointelle_sslider_admin_url( array( 'page' => 'pointelle-slider-settings' ) );

	$settings_link = '<a href="' . esc_attr( $url ) . '">'
		. esc_html( __( 'Settings') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

//New Custom Post Type
if( $pointelle_slider['custom_post'] == '1' and !post_type_exists('slidervilla') ){
	add_action( 'init', 'pointelle_post_type', 11 );
	function pointelle_post_type() {
			$labels = array(
			'name' => _x('SliderVilla Slides', 'post type general name'),
			'singular_name' => _x('SliderVilla Slide', 'post type singular name'),
			'add_new' => _x('Add New', 'pointelle'),
			'add_new_item' => __('Add New SliderVilla Slide'),
			'edit_item' => __('Edit SliderVilla Slide'),
			'new_item' => __('New SliderVilla Slide'),
			'all_items' => __('All SliderVilla Slides'),
			'view_item' => __('View SliderVilla Slide'),
			'search_items' => __('Search SliderVilla Slides'),
			'not_found' =>  __('No SliderVilla slides found'),
			'not_found_in_trash' => __('No SliderVilla slides found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => 'SliderVilla Slides'

			);
			$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail','excerpt','custom-fields')
			); 
			register_post_type('slidervilla',$args);
	}

	//add filter to ensure the text SliderVilla, or slidervilla, is displayed when user updates a slidervilla 
	add_filter('post_updated_messages', 'pointelle_updated_messages');
	function pointelle_updated_messages( $messages ) {
	  global $post, $post_ID;

	  $messages['pointelle'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('SliderVilla Slide updated. <a href="%s">View SliderVilla slide</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('SliderVilla Slide updated.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('SliderVilla Slide restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('SliderVilla Slide published. <a href="%s">View SliderVilla slide</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Pointelle saved.'),
		8 => sprintf( __('SliderVilla Slide submitted. <a target="_blank" href="%s">Preview SliderVilla slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('SliderVilla Slides scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview SliderVilla slide</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('SliderVilla Slide draft updated. <a target="_blank" href="%s">Preview SliderVilla slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );

	  return $messages;
	}
} //if custom_post is true

require_once (dirname (__FILE__) . '/includes/media-images.php');
require_once (dirname (__FILE__) . '/slider_versions/pointelle_1.php');
require_once (dirname (__FILE__) . '/settings/settings.php');

// Load the update-notification class
add_action('init', 'pointelle_update_notification');
function pointelle_update_notification()
{
    require_once (dirname (__FILE__) . '/includes/upgrade.php');
    $pointelle_upgrade_remote_path = 'http://support.slidervilla.com/sv-updates/pointelle.php';
    new pointelle_update_class ( POINTELLE_SLIDER_VER, $pointelle_upgrade_remote_path, POINTELLE_SLIDER_PLUGIN_BASENAME );
}
?>