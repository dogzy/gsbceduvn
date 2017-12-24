<?php // Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'pointelle_slider_settings');
  add_action( 'admin_init', 'register_pointelle_settings' ); 
} 

// function for adding settings page to wp-admin
function pointelle_slider_settings() {
    // Add a new submenu under Options:
	add_menu_page( 'Pointelle Slider', 'Pointelle Slider', 'manage_options','pointelle-slider-admin', 'pointelle_slider_create_multiple_sliders', pointelle_slider_plugin_url( 'images/pointelle_slider_icon.gif' ) );
	add_submenu_page('pointelle-slider-admin', 'Pointelle Sliders', 'Sliders', 'manage_options', 'pointelle-slider-admin', 'pointelle_slider_create_multiple_sliders');
	add_submenu_page('pointelle-slider-admin', 'Pointelle Slider Settings', 'Settings', 'manage_options', 'pointelle-slider-settings', 'pointelle_slider_settings_page');
}
include('sliders.php');
// This function displays the page content for the Pointelle Slider Options submenu
function pointelle_slider_settings_page() {
global $pointelle_slider,$default_pointelle_slider_settings;
$scounter=get_option('pointelle_slider_scounter');
$cntr = $_GET['scounter'];

//Create Set
$new_settings_msg='';
if ($_POST['create_set'] and $_POST['create_set']=='Create New Settings Set') {
  $scounter++;
  update_option('pointelle_slider_scounter',$scounter);
  $options='pointelle_slider_options'.$scounter;
  update_option($options,$default_pointelle_slider_settings);
  $current_url = admin_url('admin.php?page=pointelle-slider-settings');
  $current_url = add_query_arg('scounter',$scounter,$current_url);
  $new_settings_msg='<div id="message" class="updated fade" style="clear:left;"><h3>'.sprintf(__('Settings Set %s created successfully. ','pointelle-slider'),$scounter).'<a href="'.$current_url.'">'.__('Click here to edit the new Settings set =&gt;','pointelle-slider').'</a></h3></div>';
}

//Reset Settings
if ( $_POST['pointelle_reset_settings_submit'] and $_POST['pointelle_reset_settings']!='n' ) {
  $pointelle_reset_settings=$_POST['pointelle_reset_settings'];
  $options='pointelle_slider_options'.$cntr;
  $optionsvalue=get_option($options);
  if( $pointelle_reset_settings == 'g' ){
	$new_settings_value=$default_pointelle_slider_settings;
	$new_settings_value['setname']=$optionsvalue['setname'];
	update_option($options,$new_settings_value);
  }
  else{
	if( $pointelle_reset_settings == '1' ){
		$new_settings_value=get_option('pointelle_slider_options');
		$new_settings_value['setname']=$optionsvalue['setname'];
		update_option($options,	$new_settings_value );
	}
	else{
		$new_option_name='pointelle_slider_options'.$pointelle_reset_settings;
		$new_settings_value=get_option($new_option_name);
		$new_settings_value['setname']=$optionsvalue['setname'];
		update_option($options,	$new_settings_value );
	}
  }
}

//Delete Set
if ($_POST['delete_set'] and $_POST['delete_set']=='Delete this Set' and isset($cntr) and !empty($cntr)) {
  $options='pointelle_slider_options'.$cntr;
  delete_option($options);
  $cntr='';
}
$group='pointelle-slider-group'.$cntr;
$pointelle_slider_options='pointelle_slider_options'.$cntr;
$pointelle_slider_curr=get_option($pointelle_slider_options);
if(!isset($cntr) or empty($cntr) or !$pointelle_slider_curr ){$curr = 'Default';}
else{$curr = $cntr;}
?>

<div class="wrap" style="clear:both;">
<h2 style="float:left;"><?php _e('Pointelle Slider Settings ','pointelle-slider'); echo $curr; ?> </h2>
<form style="float:left;margin:10px 20px" action="" method="post">
<?php if(isset($cntr) and !empty($cntr)){ ?>
<input type="submit" class="button-primary" value="Delete this Set" name="delete_set"  onclick="return confirmSettingsDelete()" />
<?php } ?>
</form>
<div class="svilla_cl"></div>
<?php echo $new_settings_msg;?>
<?php if($pointelle_slider_curr['disable_preview'] != '1'){?>
<div id="settings_preview"><h2 style="clear:left;"><?php _e('Preview','pointelle-slider'); ?></h2> 
<?php 
if ($pointelle_slider_curr['preview'] == "0")
	get_pointelle_slider($pointelle_slider_curr['slider_id'],$cntr);
elseif($pointelle_slider_curr['preview'] == "1")
	get_pointelle_slider_category($pointelle_slider_curr['catg_slug'],$cntr);
else
	get_pointelle_slider_recent($cntr);
?> </div>
<?php } ?>

<div id="pointelle_settings" style="float:left;width:70%;">
<form method="post" action="options.php" id="pointelle_slider_form">
<?php settings_fields($group); ?>

<?php
if(!isset($cntr) or empty($cntr)){}
else{?>
	<table class="form-table">
		<tr valign="top">
		<th scope="row"><h3><?php _e('Setting Set Name','pointelle-slider'); ?></h3></th>
		<td><h3><input type="text" name="<?php echo $pointelle_slider_options;?>[setname]" id="pointelle_slider_setname" class="regular-text" value="<?php echo $pointelle_slider_curr['setname']; ?>" /></h3></td>
		</tr>
	</table>
<?php }
?>

<div id="slider_tabs">
        <ul class="ui-tabs">
            <li style="font-weight:bold;font-size:12px;"><a href="#basic">Basic Settings</a></li>
            <li style="font-weight:bold;font-size:12px;"><a href="#slides">Left Slides Panel</a></li>
             <li style="font-weight:bold;font-size:12px;"><a href="#slidesnav">Right Navigation buttons</a></li>
			 <li style="font-weight:bold;font-size:12px;"><a href="#responsive">Responsiveness</a></li>
			 <li style="font-weight:bold;font-size:12px;"><a href="#preview">Preview Settings</a></li>
			 <li style="font-weight:bold;font-size:12px;"><a href="#cssvalues">Generated CSS</a></li>
        </ul>

<div id="basic">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Basic Settings','pointelle-slider'); ?></h2> 

<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Auto Sliding','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[autoslide]" >
<option value="1" <?php if ($pointelle_slider_curr['autoslide'] == "1"){ echo "selected";}?> ><?php _e('ON','pointelle-slider'); ?></option>
<option value="0" <?php if ($pointelle_slider_curr['autoslide'] == "0"){ echo "selected";}?> ><?php _e('OFF','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Transition Speed','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[speed]" id="pointelle_slider_speed" class="small-text" value="<?php echo $pointelle_slider_curr['speed']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('The duration of Slide Animation in milliseconds. Lower value indicates fast animation. Enter numeric values like 10 or 11.','pointelle-slider'); ?>
	</div>
</span>
<br /><small style="color:#FF0000"><?php _e(' (IMP!! Enter value > 0)','pointelle-slider'); ?></small>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Time interval before next transition','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[pause]" id="pointelle_slider_pause" class="small-text" value="<?php echo $pointelle_slider_curr['pause']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Valid if auto sliding is not disabled. Lower value indicates Slides will Appear Faster. Enter value like 4 or 5.','pointelle-slider'); ?>
	</div>
</span> &nbsp;
<small style="color:#FF0000"><?php _e(' (IMP!! Enter value > 0)','pointelle-slider'); ?></small></td>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Transition Effect','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[transition]" >
<option value="fade" <?php if ($pointelle_slider_curr['transition'] == "fade"){ echo "selected";}?> ><?php _e('Fade effect','pointelle-slider'); ?></option>
<option value="scrollUp" <?php if ($pointelle_slider_curr['transition'] == "scrollUp"){ echo "selected";}?> ><?php _e('Vertical Sliding','pointelle-slider'); ?></option>
<option value="scrollLeft" <?php if ($pointelle_slider_curr['transition'] == "scrollLeft"){ echo "selected";}?> ><?php _e('Horizontal Sliding','pointelle-slider'); ?></option>
<option value="uncover" <?php if ($pointelle_slider_curr['transition'] == "uncover"){ echo "selected";}?> ><?php _e('Uncovering Effect','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top"> 
	<th scope="row" ><label for="<?php echo $pointelle_slider_options;?>[onhover]"><?php _e('Transition on','pointelle-slider'); ?></label></th> 
	<td><input id="onhover0" name="<?php echo $pointelle_slider_options;?>[onhover]" type="radio" value="0" <?php checked('0', $pointelle_slider_curr['onhover']); ?>  /> <label for="onhover0"><?php _e('Click','pointelle-slider'); ?></label> &nbsp; &nbsp; 
	<input id="onhover1" name="<?php echo $pointelle_slider_options;?>[onhover]" type="radio" value="1" <?php checked('1', $pointelle_slider_curr['onhover']); ?>  /> <label for="onhover1"><?php _e('Hover','pointelle-slider'); ?></label>
	</td> 
</tr>

<tr valign="top">
<th scope="row"><?php _e('Total Number of Posts in the Pointelle Slider','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[no_posts]" id="pointelle_slider_no_posts" class="small-text" value="<?php echo $pointelle_slider_curr['no_posts']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Complete Slider Width','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[width]" id="pointelle_slider_width" class="small-text" value="<?php echo $pointelle_slider_curr['width']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If set to 0, will take the container\'s width','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slider Height','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[height]" id="pointelle_slider_height" class="small-text" value="<?php echo $pointelle_slider_curr['height']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Background Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[bg_color]" id="color_value_8" value="<?php echo $pointelle_slider_curr['bg_color']; ?>" />&nbsp; <img id="color_picker_8" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_8"></div> <br /> 
<label for="pointelle_slider_bg"><input name="<?php echo $pointelle_slider_options;?>[bg]" type="checkbox" id="pointelle_slider_bg" value="1" <?php checked('1', $pointelle_slider_curr['bg']); ?>  /><?php _e(' Use Transparent Background','pointelle-slider'); ?></label> </td>
</tr>

</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Miscellaneous','pointelle-slider'); ?></h2> 

<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Retain these html tags','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[allowable_tags]" class="regular-text code" value="<?php echo $pointelle_slider_curr['allowable_tags']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Link (\'a\' element) attributes  ','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[a_attr]" class="regular-text code" value="<?php echo htmlentities( $pointelle_slider_curr['a_attr'] , ENT_QUOTES); ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('eg. target="_blank" rel="external nofollow"','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Use PrettyPhoto (Lightbox) for Slide Images','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[pphoto]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['pphoto']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If checked, when user clicks the slide image, it will appear in a modal lightbox','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Custom fields to display for post/pages','pointelle-slider'); ?></th>
<td><textarea name="<?php echo $pointelle_slider_options;?>[fields]"  rows="2" cols="40" class="regular-text code"><?php echo $pointelle_slider_curr['fields']; ?></textarea>
<span class="moreInfo">
<span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Separate different fields using commas eg. description,customfield2','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Continue Reading Text','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[readmore]" class="regular-text" value="<?php echo $pointelle_slider_curr['readmore']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Randomize Slides in Slider','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[rand]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['rand']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this if you want the slides added to appear in random order.','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<?php if(!isset($cntr) or empty($cntr)){?>

<tr valign="top">
<th scope="row"><?php _e('Minimum User Level to add Post to the Slider','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[user_level]" >
<option value="manage_options" <?php if ($pointelle_slider_curr['user_level'] == "manage_options"){ echo "selected";}?> ><?php _e('Administrator','pointelle-slider'); ?></option>
<option value="edit_others_posts" <?php if ($pointelle_slider_curr['user_level'] == "edit_others_posts"){ echo "selected";}?> ><?php _e('Editor and Admininstrator','pointelle-slider'); ?></option>
<option value="publish_posts" <?php if ($pointelle_slider_curr['user_level'] == "publish_posts"){ echo "selected";}?> ><?php _e('Author, Editor and Admininstrator','pointelle-slider'); ?></option>
<option value="edit_posts" <?php if ($pointelle_slider_curr['user_level'] == "edit_posts"){ echo "selected";}?> ><?php _e('Contributor, Author, Editor and Admininstrator','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Text to display in the JavaScript disabled browser','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[noscript]" class="regular-text code" value="<?php echo $pointelle_slider_curr['noscript']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Add Shortcode Support','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[shortcode]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['shortcode']); ?>  />&nbsp;<?php _e('check this if you want to use Pointelle Slider Shortcode i.e [pointelleslider]','pointelle-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Pointelle Slider Styles to Use on Other than Post/Pages','pointelle-slider'); ?> <small><?php _e('(i.e. for index.php,category.php,archive.php etc)','pointelle-slider'); ?></small></th>
<td><select name="<?php echo $pointelle_slider_options;?>[stylesheet]" >
<?php 
$directory = POINTELLE_SLIDER_CSS_DIR;
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { ?>
      <option value="<?php echo $file;?>" <?php if ($pointelle_slider_curr['stylesheet'] == $file){ echo "selected";}?> ><?php echo $file;?></option>
 <?php  } }
    closedir($handle);
}
?>
</select><small><?php _e('Caution: Do not change from default, until you are sure you need to','pointelle-slider'); ?></small>
</td>
</tr>
<?php } ?>

<?php if(!isset($cntr) or empty($cntr)){?>
<tr valign="top">
<th scope="row"><?php _e('Multiple Slider Feature','pointelle-slider'); ?></th>
<td><label for="pointelle_slider_multiple"> 
<input name="<?php echo $pointelle_slider_options;?>[multiple_sliders]" type="checkbox" id="pointelle_slider_multiple" value="1" <?php checked("1", $pointelle_slider_curr['multiple_sliders']); ?> /> 
 <?php _e('Enable Multiple Slider Function on Edit Post/Page','pointelle-slider'); ?></label></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Create "SliderVilla Slides" Custom Post Type','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[custom_post]" >
<option value="0" <?php if ($pointelle_slider_curr['custom_post'] == "0"){ echo "selected";}?> ><?php _e('No','pointelle-slider'); ?></option>
<option value="1" <?php if ($pointelle_slider_curr['custom_post'] == "1"){ echo "selected";}?> ><?php _e('Yes','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Remove Pointelle Slider Metabox on','pointelle-slider'); ?></th>
<td>
<select name="<?php echo $pointelle_slider_options;?>[remove_metabox][]" multiple="multiple" size="3" style="min-height:6em;">
<?php 
$args=array(
  'public'   => true
); 
$output = 'objects'; // names or objects, note names is the default
$post_types=get_post_types($args,$output); $remove_post_type_arr=$pointelle_slider_curr['remove_metabox'];
if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		foreach($post_types as $post_type) { ?>
                  <option value="<?php echo $post_type->name;?>" <?php if(in_array($post_type->name,$remove_post_type_arr)){echo 'selected';} ?>><?php echo $post_type->labels->name;?></option>
                <?php } ?>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('You can select single/multiple post types using Ctrl+Mouse Click. To deselect a single post type, use Ctrl+Mouse Click','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<?php } ?>

<tr valign="top">
<th scope="row"><?php _e('Enable FOUC','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[fouc]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['fouc']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this if you would not want to disable Flash of Unstyled Content in the slider when the page is loaded.','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>


<?php if(!isset($cntr) or empty($cntr)){?>

<tr valign="top">
<th scope="row"><?php _e('Custom Styles','pointelle-slider'); ?></th>
<td><textarea name="<?php echo $pointelle_slider_options;?>[css]"  rows="5" cols="40" class="regular-text code"><?php echo $pointelle_slider_curr['css']; ?></textarea>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('custom css styles that you would want to be applied to the slider elements','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Show Promotionals on Admin Page','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[support]" >
<option value="1" <?php if ($pointelle_slider_curr['support'] == "1"){ echo "selected";}?> ><?php _e('Yes','pointelle-slider'); ?></option>
<option value="0" <?php if ($pointelle_slider_curr['support'] == "0"){ echo "selected";}?> ><?php _e('No','pointelle-slider'); ?></option>
</select>
</td>
</tr>
<?php } ?>
</table>
</div>
<?php do_action('pointelle_addon_settings',$cntr,$pointelle_slider_options,$pointelle_slider_curr);?>
</div> <!--Basic -->

<div id="slides">

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Slide Image','pointelle-slider'); ?></h2> 
<p><?php _e('Customize the looks of the big image in the slide for each of the sliding post here','pointelle-slider'); ?></p> 
<table class="form-table">

<tr valign="top"> 
<th scope="row"><?php _e('Image Pick Preferences','pointelle-slider'); ?> <small><?php _e('(The first one is having priority over second, the second having priority on third and so on)','pointelle-slider'); ?></small></th> 
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Image Pick Sequence','pointelle-slider'); ?> <small><?php _e('(The first one is having priority over second, the second having priority on third and so on)','pointelle-slider'); ?></small> </span></legend> 
<input name="<?php echo $pointelle_slider_options;?>[img_pick][0]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['img_pick'][0]); ?>  /> <?php _e('Use Custom Field/Key','pointelle-slider'); ?> &nbsp; &nbsp; 
<input type="text" name="<?php echo $pointelle_slider_options;?>[img_pick][1]" class="text" value="<?php echo $pointelle_slider_curr['img_pick'][1]; ?>" /> <?php _e('Name of the Custom Field/Key','pointelle-slider'); ?>
<br />
<input name="<?php echo $pointelle_slider_options;?>[img_pick][2]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['img_pick'][2]); ?>  /> <?php _e('Use Featured Post/Thumbnail (Wordpress 3.0 +  feature)','pointelle-slider'); ?>&nbsp; <br />
<input name="<?php echo $pointelle_slider_options;?>[img_pick][3]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['img_pick'][3]); ?>  /> <?php _e('Consider Images attached to the post','pointelle-slider'); ?> &nbsp; &nbsp; 
<input type="text" name="<?php echo $pointelle_slider_options;?>[img_pick][4]" class="small-text" value="<?php echo $pointelle_slider_curr['img_pick'][4]; ?>" /> <?php _e('Order of the Image attachment to pick','pointelle-slider'); ?> &nbsp; <br />
<input name="<?php echo $pointelle_slider_options;?>[img_pick][5]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['img_pick'][5]); ?>  /> <?php _e('Scan images from the post, in case there is no attached image to the post','pointelle-slider'); ?>&nbsp; 
</fieldset></td> 
</tr> 

<tr valign="top">
<th scope="row"><?php _e('Wordpress Image Extract Size','pointelle-slider'); ?>
</th>
<td><select name="<?php echo $pointelle_slider_options;?>[crop]" id="pointelle_slider_img_crop" >
<option value="0" <?php if ($pointelle_slider_curr['crop'] == "0"){ echo "selected";}?> ><?php _e('Full','pointelle-slider'); ?></option>
<option value="1" <?php if ($pointelle_slider_curr['crop'] == "1"){ echo "selected";}?> ><?php _e('Large','pointelle-slider'); ?></option>
<option value="2" <?php if ($pointelle_slider_curr['crop'] == "2"){ echo "selected";}?> ><?php _e('Medium','pointelle-slider'); ?></option>
<option value="3" <?php if ($pointelle_slider_curr['crop'] == "3"){ echo "selected";}?> ><?php _e('Thumbnail','pointelle-slider'); ?></option>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This is for fast page load, in case you choose \'Custom Size\' setting from below, you would not like to extract \'full\' size image from the media library. In this case you can use, \'medium\' or \'thumbnail\' image. This is because, for every image upload to the media gallery WordPress creates four sizes of the same image. So you can choose which to load in the slider and then specify the actual size.','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top"> 
<th scope="row"><?php _e('Image Size','pointelle-slider'); ?></th> 
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Image Size','pointelle-slider'); ?></span></legend> 
<label for="<?php echo $pointelle_slider_options;?>[img_width]"><?php _e('Width','pointelle-slider'); ?></label>
<input type="text" name="<?php echo $pointelle_slider_options;?>[img_width]" class="small-text" value="<?php echo $pointelle_slider_curr['img_width']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?> &nbsp;&nbsp; 
<label for="<?php echo $pointelle_slider_options;?>[img_height]"><?php _e('Height','pointelle-slider'); ?></label>
<input type="text" name="<?php echo $pointelle_slider_options;?>[img_height]" class="small-text" value="<?php echo $pointelle_slider_curr['img_height']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?> &nbsp;&nbsp; 
</fieldset></td> 
</tr>

<tr valign="top">
<th scope="row"><?php _e('Border Thickness','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[img_border]" id="pointelle_slider_img_border" class="small-text" value="<?php echo $pointelle_slider_curr['img_border']; ?>" />&nbsp;<?php _e('px  (put 0 if no border is required)','pointelle-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Border Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[img_brcolor]" id="color_value_4" value="<?php echo $pointelle_slider_curr['img_brcolor']; ?>" />&nbsp; <img id="color_picker_4" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_4"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Disable Image Cropping (using timthumb)','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[timthumb]" >
<option value="0" <?php if ($pointelle_slider_curr['timthumb'] == "0"){ echo "selected";}?> ><?php _e('No','pointelle-slider'); ?></option>
<option value="1" <?php if ($pointelle_slider_curr['timthumb'] == "1"){ echo "selected";}?> ><?php _e('Yes','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Protect the Large Images from Copying','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[copyprotect]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['copyprotect']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Once enabled, user cannot right click and open the large images on left side in the browser and copy them unless viewing the html source.','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Make pure Image Slider','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[image_only]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['image_only']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this to convert Pointelle Slider to Image Slider with no content','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Show Content on Image Hover','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[hovercontent]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['hovercontent']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If you do not want to completely convert to Image Slider, using this option you can display the content over image only on hover','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Show Next Previous arrows on Slide Images on hover','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[nextprev]" >
<option value="1" <?php if ($pointelle_slider_curr['nextprev'] == "1"){ echo "selected";}?> ><?php _e('Yes','pointelle-slider'); ?></option>
<option value="0" <?php if ($pointelle_slider_curr['nextprev'] == "0"){ echo "selected";}?> ><?php _e('No','pointelle-slider'); ?></option>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('These will be navigation arrows on the large slide panel.','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Post Title','pointelle-slider'); ?></h2> 
<p><?php _e('Customize the looks of the title of each of the sliding post here','pointelle-slider'); ?></p> 
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Show title in slides above excerpt/content','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[show_title]" >
<option value="1" <?php if ($pointelle_slider_curr['show_title'] == "1"){ echo "selected";}?> ><?php _e('Yes','pointelle-slider'); ?></option>
<option value="0" <?php if ($pointelle_slider_curr['show_title'] == "0"){ echo "selected";}?> ><?php _e('No','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[ptitle_font]" id="pointelle_slider_ptitle_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($pointelle_slider_curr['ptitle_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($pointelle_slider_curr['ptitle_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[ptitle_fcolor]" id="color_value_3" value="<?php echo $pointelle_slider_curr['ptitle_fcolor']; ?>" />&nbsp; <img id="color_picker_3" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_3"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[ptitle_fsize]" id="pointelle_slider_ptitle_fsize" class="small-text" value="<?php echo $pointelle_slider_curr['ptitle_fsize']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[ptitle_fstyle]" id="pointelle_slider_ptitle_fstyle" >
<option value="bold" <?php if ($pointelle_slider_curr['ptitle_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','pointelle-slider'); ?></option>
<option value="bold italic" <?php if ($pointelle_slider_curr['ptitle_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','pointelle-slider'); ?></option>
<option value="italic" <?php if ($pointelle_slider_curr['ptitle_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','pointelle-slider'); ?></option>
<option value="normal" <?php if ($pointelle_slider_curr['ptitle_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','pointelle-slider'); ?></option>
</select>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Post Content','pointelle-slider'); ?></h2> 
<p><?php _e('Customize the looks of the content of each of the sliding post here','pointelle-slider'); ?></p> 
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Show content/description below title','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[show_content]" >
<option value="1" <?php if ($pointelle_slider_curr['show_content'] == "1"){ echo "selected";}?> ><?php _e('Yes','pointelle-slider'); ?></option>
<option value="0" <?php if ($pointelle_slider_curr['show_content'] == "0"){ echo "selected";}?> ><?php _e('No','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[content_font]" id="pointelle_slider_content_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($pointelle_slider_curr['content_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($pointelle_slider_curr['content_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($pointelle_slider_curr['content_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($pointelle_slider_curr['content_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($pointelle_slider_curr['content_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($pointelle_slider_curr['content_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($pointelle_slider_curr['content_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($pointelle_slider_curr['content_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($pointelle_slider_curr['content_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($pointelle_slider_curr['content_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($pointelle_slider_curr['content_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($pointelle_slider_curr['content_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($pointelle_slider_curr['content_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($pointelle_slider_curr['content_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($pointelle_slider_curr['content_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($pointelle_slider_curr['content_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($pointelle_slider_curr['content_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[content_fcolor]" id="color_value_5" value="<?php echo $pointelle_slider_curr['content_fcolor']; ?>" />&nbsp; <img id="color_picker_5" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_5"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[content_fsize]" id="pointelle_slider_content_fsize" class="small-text" value="<?php echo $pointelle_slider_curr['content_fsize']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[content_fstyle]" id="pointelle_slider_content_fstyle" >
<option value="bold" <?php if ($pointelle_slider_curr['content_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','pointelle-slider'); ?></option>
<option value="bold italic" <?php if ($pointelle_slider_curr['content_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','pointelle-slider'); ?></option>
<option value="italic" <?php if ($pointelle_slider_curr['content_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','pointelle-slider'); ?></option>
<option value="normal" <?php if ($pointelle_slider_curr['content_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Pick content From','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[content_from]" id="pointelle_slider_content_from" >
<option value="slider_content" <?php if ($pointelle_slider_curr['content_from'] == "slider_content"){ echo "selected";}?> ><?php _e('Slider Content Custom field','pointelle-slider'); ?></option>
<option value="excerpt" <?php if ($pointelle_slider_curr['content_from'] == "excerpt"){ echo "selected";}?> ><?php _e('Post Excerpt','pointelle-slider'); ?></option>
<option value="content" <?php if ($pointelle_slider_curr['content_from'] == "content"){ echo "selected";}?> ><?php _e('From Content','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Maximum content size (in words)','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[content_limit]" id="pointelle_slider_content_limit" class="small-text" value="<?php echo $pointelle_slider_curr['content_limit']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('if specified will override the \'Maximum Content Size in Chracters\' setting below','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Maximum content size (in characters)','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[content_chars]" id="pointelle_slider_content_chars" class="small-text" value="<?php echo $pointelle_slider_curr['content_chars']; ?>" />&nbsp;<?php _e('characters','pointelle-slider'); ?> </td>
</tr>

</table>

</div>

</div> <!--#slides-->

<div id="slidesnav">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Navigation Button','pointelle-slider'); ?></h2> 

<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Disable Navigation','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[disable_nav]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['disable_nav']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this if you want to remove the navigation area completely','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Navigation Location','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[navpos]" >
<option value="1" <?php if ($pointelle_slider_curr['navpos'] == "1"){ echo "selected";}?> ><?php _e('Right','pointelle-slider'); ?></option>
<option value="0" <?php if ($pointelle_slider_curr['navpos'] == "0"){ echo "selected";}?> ><?php _e('Left','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Visible slides in the navigation','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[scroll_nav_posts]" id="pointelle_slider_scroll_nav_posts" class="small-text" value="<?php echo $pointelle_slider_curr['scroll_nav_posts']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Navigation Section Width','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_control_w]" id="pointelle_slider_nav_control_w" class="small-text" value="<?php echo $pointelle_slider_curr['nav_control_w']; ?>" />&nbsp;px</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Navigation Section Height','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_control_h]" id="pointelle_slider_nav_control_h" class="small-text" value="<?php echo $pointelle_slider_curr['nav_control_h']; ?>" />&nbsp;px</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Navigation Section Background Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_bg_color]" id="color_value_1" value="<?php echo $pointelle_slider_curr['nav_bg_color']; ?>" />&nbsp; <img id="color_picker_1" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_1"></div> <br /> 
<label for="pointelle_slider_nav_bg"><input name="<?php echo $pointelle_slider_options;?>[nav_bg]" type="checkbox" id="pointelle_slider_nav_bg" value="1" <?php checked('1', $pointelle_slider_curr['nav_bg']); ?>  /><?php _e(' Use Transparent Background','pointelle-slider'); ?></label> </td>
</tr>
 
<tr valign="top">
<th scope="row"><?php _e('Navigation Section Border Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_brcolor]" id="color_value_6" value="<?php echo $pointelle_slider_curr['nav_brcolor']; ?>" />&nbsp; <img id="color_picker_6" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_6"></div></td>
</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Thumbnail Image','pointelle-slider'); ?></h2>
<p><?php _e('Customize the looks of the thumbnail image in the navigation here','pointelle-slider'); ?></p> 

<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Disable Thumbs in Navigation','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[disable_thumbs]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['disable_thumbs']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this if you do not want thumbnail images in Pointelle Navigation','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Thumb Image Size','pointelle-slider'); ?></th>
<td><fieldset><label for="<?php echo $pointelle_slider_options;?>[nav_img_width]"><?php _e('Image Width','pointelle-slider'); ?></label><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_img_width]" class="small-text" value="<?php echo $pointelle_slider_curr['nav_img_width']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?> &nbsp; &nbsp; <label for="<?php echo $pointelle_slider_options;?>[nav_img_height]"><?php _e('Image Height','pointelle-slider'); ?></label><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_img_height]" class="small-text" value="<?php echo $pointelle_slider_curr['nav_img_height']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?> 
</fieldset>
</td>
</tr> 

<tr valign="top">
<th scope="row"><?php _e('Thumb Border Thickness','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_img_border]" id="pointelle_slider_nav_img_border" class="small-text" value="<?php echo $pointelle_slider_curr['nav_img_border']; ?>" />&nbsp;<?php _e('px ','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('put 0 if no border is required','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Thumb Border Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_img_brcolor]" id="color_value_7" value="<?php echo $pointelle_slider_curr['nav_img_brcolor']; ?>" />&nbsp; <img id="color_picker_7" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_7"></div></td>
</tr>


</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Title in Navigation','pointelle-slider'); ?></h2> 
<p><?php _e('Customize the looks of the title of the Slide in Navigation','pointelle-slider'); ?></p> 
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Disable Text in Navigation','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[disable_navtext]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['disable_navtext']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this if you do not want any text i.e. title and post meta in Pointelle Navigation','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Max. Slide Navigation Title Size (in words)','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[slide_nav_limit]" id="pointelle_slider_slide_nav_limit" class="small-text" value="<?php echo $pointelle_slider_curr['slide_nav_limit']; ?>" />&nbsp;<?php _e('words','pointelle-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[nav_title_font]" id="pointelle_slider_nav_title_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($pointelle_slider_curr['nav_title_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($pointelle_slider_curr['nav_title_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_title_fcolor]" id="color_value_2" value="<?php echo $pointelle_slider_curr['nav_title_fcolor']; ?>" />&nbsp; <img id="color_picker_2" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_2"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[nav_title_fsize]" id="pointelle_slider_nav_title_fsize" class="small-text" value="<?php echo $pointelle_slider_curr['nav_title_fsize']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[nav_title_fstyle]" id="pointelle_slider_nav_title_fstyle" >
<option value="bold" <?php if ($pointelle_slider_curr['nav_title_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','pointelle-slider'); ?></option>
<option value="bold italic" <?php if ($pointelle_slider_curr['nav_title_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','pointelle-slider'); ?></option>
<option value="italic" <?php if ($pointelle_slider_curr['nav_title_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','pointelle-slider'); ?></option>
<option value="normal" <?php if ($pointelle_slider_curr['nav_title_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','pointelle-slider'); ?></option>
</select>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Post Meta in Navigation','pointelle-slider'); ?></h2> 
<p><?php _e('Customize the content and looks of the Post Meta Info in the Slide in Navigation','pointelle-slider'); ?></p> 
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Meta Field 1','pointelle-slider'); ?></th>
<td>
<fieldset><table>
<tr><td style="padding:0"><label for="<?php echo $pointelle_slider_options;?>[meta1_fn]">Function Name</label></td><td style="padding:0"><input type="text" name="<?php echo $pointelle_slider_options;?>[meta1_fn]" class="regular-text code" value="<?php echo $pointelle_slider_curr['meta1_fn']; ?>" /> </td> </tr>
<tr><td style="padding:0"><label for="<?php echo $pointelle_slider_options;?>[meta1_parms]">Function Parameters</label></td><td style="padding:0"><input type="text" name="<?php echo $pointelle_slider_options;?>[meta1_parms]" class="regular-text code" value="<?php echo $pointelle_slider_curr['meta1_parms']; ?>" /> </td> </tr>
<tr><td style="padding:0"><label for="<?php echo $pointelle_slider_options;?>[meta1_before]">Before Text/HTML</label></td><td style="padding:0"><input type="text" name="<?php echo $pointelle_slider_options;?>[meta1_before]" class="regular-text code" value="<?php echo $pointelle_slider_curr['meta1_before']; ?>" /> </td> </tr> 
<tr><td style="padding:0"><label for="<?php echo $pointelle_slider_options;?>[meta1_after]">After Text/HTML</label></td><td style="padding:0"><input type="text" name="<?php echo $pointelle_slider_options;?>[meta1_after]" class="regular-text code" value="<?php echo $pointelle_slider_curr['meta1_after']; ?>" /> </td> </tr>
</table></fieldset>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Meta Field 2','pointelle-slider'); ?></th>
<td>
<fieldset><table>
<tr><td style="padding:0"><label for="<?php echo $pointelle_slider_options;?>[meta2_fn]">Function Name</label></td><td style="padding:0"><input type="text" name="<?php echo $pointelle_slider_options;?>[meta2_fn]" class="regular-text code" value="<?php echo $pointelle_slider_curr['meta2_fn']; ?>" /> </td></tr> 
<tr><td style="padding:0"><label for="<?php echo $pointelle_slider_options;?>[meta2_parms]">Function Parameters</label></td><td style="padding:0"><input type="text" name="<?php echo $pointelle_slider_options;?>[meta2_parms]" class="regular-text code" value="<?php echo $pointelle_slider_curr['meta2_parms']; ?>" /> </td></tr>
<tr><td style="padding:0"><label for="<?php echo $pointelle_slider_options;?>[meta2_before]">Before Text/HTML</label></td><td style="padding:0"><input type="text" name="<?php echo $pointelle_slider_options;?>[meta2_before]" class="regular-text code" value="<?php echo $pointelle_slider_curr['meta2_before']; ?>" /></td></tr> 
<tr><td style="padding:0"><label for="<?php echo $pointelle_slider_options;?>[meta2_after]">After Text/HTML</label></td><td style="padding:0"><input type="text" name="<?php echo $pointelle_slider_options;?>[meta2_after]" class="regular-text code" value="<?php echo $pointelle_slider_curr['meta2_after']; ?>" /></td></tr>
</table></fieldset>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[meta_title_font]" id="pointelle_slider_meta_title_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($pointelle_slider_curr['meta_title_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($pointelle_slider_curr['meta_title_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[meta_title_fcolor]" id="color_value_9" value="<?php echo $pointelle_slider_curr['meta_title_fcolor']; ?>" />&nbsp; <img id="color_picker_9" src="<?php echo pointelle_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','pointelle-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_9"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[meta_title_fsize]" id="pointelle_slider_meta_title_fsize" class="small-text" value="<?php echo $pointelle_slider_curr['meta_title_fsize']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[meta_title_fstyle]" id="pointelle_slider_meta_title_fstyle" >
<option value="bold" <?php if ($pointelle_slider_curr['meta_title_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','pointelle-slider'); ?></option>
<option value="bold italic" <?php if ($pointelle_slider_curr['meta_title_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','pointelle-slider'); ?></option>
<option value="italic" <?php if ($pointelle_slider_curr['meta_title_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','pointelle-slider'); ?></option>
<option value="normal" <?php if ($pointelle_slider_curr['meta_title_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','pointelle-slider'); ?></option>
</select>
</td>
</tr>
</table>
</div>

</div> <!--#slidesnav-->

<div id="responsive">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('Responsive Design Settings','pointelle-slider'); ?></h2> 

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Enable Responsive Design','pointelle-slider'); ?></th>
<td><input name="<?php echo $pointelle_slider_options;?>[responsive]" type="checkbox" value="1" <?php checked('1', $pointelle_slider_curr['responsive']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this if you want to enable the responsive layout for Pointelle (you should be using Responsive/Fluid WordPress theme for this feature to work!)','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>
</table>

<table class="form-table">
<tr valign="top" style="background:#efefef;">
<th scope="row"><strong><?php _e('Device & Site specific values can be entered below','pointelle-slider'); ?></strong></th>
</tr>
</table>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Actual Slider width on 241px wide device','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[w_241]" id="pointelle_slider_w_241" class="small-text" value="<?php echo $pointelle_slider_curr['w_241']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This will be the slider width for devices with screen resolution from 241px to 319px','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Actual Slider width on 320px wide device','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[w_320]" id="pointelle_slider_w_320" class="small-text" value="<?php echo $pointelle_slider_curr['w_320']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Specially for iPhone 3 + 4 + 5 Potrait, also covers Android (Samsung Galaxy) portrait. This will be the slider width for devices with screen resolution from 320px to 399px','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Actual Slider width on 400px wide device','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[w_400]" id="pointelle_slider_w_400" class="small-text" value="<?php echo $pointelle_slider_curr['w_400']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This will be the slider width for devices with screen resolution from 400px to 479px','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Actual Slider width on 480px wide device','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[w_480]" id="pointelle_slider_w_480" class="small-text" value="<?php echo $pointelle_slider_curr['w_480']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Specially for iPhone 3 + 4 Landscape. This will be the slider width for devices with screen resolution from 480px to 567px','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Actual Slider width on 568px wide device','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[w_568]" id="pointelle_slider_w_568" class="small-text" value="<?php echo $pointelle_slider_curr['w_568']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Specially for iPhone 5 Landscape, and also covers Kindle Potrait. This will be the slider width for devices with screen resolution from 568px to 684px','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Actual Slider width on 685px wide device','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[w_685]" id="pointelle_slider_w_685" class="small-text" value="<?php echo $pointelle_slider_curr['w_685']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Specially for Android (Samsung Galaxy) landscape. This will be the slider width for devices with screen resolution from 685px to 767px','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Actual Slider width on 768px wide device','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[w_768]" id="pointelle_slider_w_768" class="small-text" value="<?php echo $pointelle_slider_curr['w_768']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Specially for iPad Potrait. This will be the slider width for devices with screen resolution from 768px to 959px','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Actual Slider width on 960px wide device','pointelle-slider'); ?></th>
<td><input type="text" name="<?php echo $pointelle_slider_options;?>[w_960]" id="pointelle_slider_w_960" class="small-text" value="<?php echo $pointelle_slider_curr['w_960']; ?>" />&nbsp;<?php _e('px','pointelle-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This will be the slider width for devices with screen resolution from 959px to 1023px','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

</table>
</div>

</div> <!--#responsive-->

<div id="preview">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:0;">
<h2><?php _e('Preview on Settings Panel','pointelle-slider'); ?></h2> 

<table class="form-table">

<tr valign="top"> 
<th scope="row"><label for="pointelle_slider_disable_preview"><?php _e('Disable Preview Section','pointelle-slider'); ?></label></th> 
<td> 
<input name="<?php echo $pointelle_slider_options;?>[disable_preview]" type="checkbox" id="pointelle_slider_disable_preview" value="1" <?php checked("1", $pointelle_slider_curr['disable_preview']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If disabled, the \'Preview\' of Slider on this Settings page will be removed.','pointelle-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Pointelle Template Tag for Preview','pointelle-slider'); ?></th>
<td><select name="<?php echo $pointelle_slider_options;?>[preview]" >
<option value="2" <?php if ($pointelle_slider_curr['preview'] == "2"){ echo "selected";}?> ><?php _e('Recent Posts Slider','pointelle-slider'); ?></option>
<option value="1" <?php if ($pointelle_slider_curr['preview'] == "1"){ echo "selected";}?> ><?php _e('Category Slider','pointelle-slider'); ?></option>
<option value="0" <?php if ($pointelle_slider_curr['preview'] == "0"){ echo "selected";}?> ><?php _e('Custom Slider with Slider ID','pointelle-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top"> 
<th scope="row"><?php _e('Preview Slider Params','pointelle-slider'); ?></th> 
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Preview Slider Params','pointelle-slider'); ?></span></legend> 
<label for="<?php echo $pointelle_slider_options;?>[slider_id]"><?php _e('Slider ID in case of Custom Slider','pointelle-slider'); ?></label>
<input type="text" name="<?php echo $pointelle_slider_options;?>[slider_id]" class="small-text" value="<?php echo $pointelle_slider_curr['slider_id']; ?>" /> 
<br />  <br />
<label for="<?php echo $pointelle_slider_options;?>[catg_slug]"><?php _e('Category Slug in case of Category Slider','pointelle-slider'); ?></label>
<input type="text" name="<?php echo $pointelle_slider_options;?>[catg_slug]" class="regular-text code" style="width:90%;" value="<?php echo $pointelle_slider_curr['catg_slug']; ?>" /> 
</fieldset></td> 
</tr> 

</table>
</div>

</div><!-- preview tab ends-->

<div id="cssvalues">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:3px 0">
<h2><?php _e('CSS Generated thru these settings','pointelle-slider'); ?></h2> 
<p><?php _e('Save Changes for the settings first and then view this data. You can use this CSS in your \'custom\' stylesheets if you use other than \'default\' value for the Stylesheet folder.','pointelle-slider'); ?></p> 
<?php $pointelle_slider_css = pointelle_get_inline_css($cntr,$echo='1'); ?>
<div style="font-family:monospace;font-size:13px;background:#ddd;">
.pointelle_slider_set<?php echo $cntr;?>{<?php echo $pointelle_slider_css['pointelle_slider'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle-slides{<?php echo $pointelle_slider_css['pointelle_slides'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle-slider-control{<?php echo $pointelle_slider_css['pointelle_slider_control'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle-slider-nav{<?php echo $pointelle_slider_css['pointelle_slider_nav'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle_nav_thumb{<?php echo $pointelle_slider_css['pointelle_slider_nav_thumb'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle-slider-nav h2{<?php echo $pointelle_slider_css['pointelle_slider_nav_h2'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle-meta{<?php echo $pointelle_slider_css['pointelle_meta'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle_slideri{<?php echo $pointelle_slider_css['pointelle_slideri'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle_slider_thumbnail{<?php echo $pointelle_slider_css['pointelle_slider_thumbnail'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle-excerpt{<?php echo $pointelle_slider_css['pointelle-excerpt'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle_slideri h4{<?php echo $pointelle_slider_css['pointelle_slider_h4'];?>} <br />
.pointelle_slider_set<?php echo $cntr;?> .pointelle_slideri h4 a{<?php echo $pointelle_slider_css['pointelle_slider_h4_a'];?>}
</div>
</div>
</div> <!--#cssvalues-->

<div class="svilla_cl"></div><div class="svilla_cr"></div>
</div> <!--end of tabs -->

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>

<!--Form to reset Settings set-->
<form style="float:left;" action="" method="post">
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Reset Settings to','pointelle-slider'); ?></th>
<td><select name="pointelle_reset_settings" id="pointelle_slider_reset_settings" >
<option value="n" selected ><?php _e('None','pointelle-slider'); ?></option>
<option value="g" ><?php _e('Global Default','pointelle-slider'); ?></option>

<?php 
for($i=1;$i<=$scounter;$i++){
	if ($i==1){
	  echo '<option value="'.$i.'" >'.__('Default Settings Set','pointelle-slider').'</option>';
	}
	else {
	  if($settings_set=get_option('pointelle_slider_options'.$i)){
		echo '<option value="'.$i.'" >'.$settings_set['setname'].' (ID '.$i.')</option>';
	  }
	}
}
?>

</select>
</td>
</tr>
</table>

<p class="submit">
<input name="pointelle_reset_settings_submit" type="submit" class="button-primary" value="<?php _e('Reset Settings') ?>" />
</p>
</form>

</div> <!--end of float left -->

<div id="poststuff" class="metabox-holder has-right-sidebar" style="float:left;width:28%;max-width:350px;min-width:inherit;"> 
<form style="float:left;margin-right:10px;font-size:14px;margin-bottom:5px;" action="" method="post">
<input type="submit" class="button-primary" style="font-size:13px;" value="Create New Settings Set" name="create_set"  onclick="return confirmSettingsCreate()" />
</form>

<?php $url = pointelle_sslider_admin_url( array( 'page' => 'pointelle-slider-admin' ) );?>
<a href="<?php echo $url; ?>" title="<?php _e('Go to Sliders page where you can re-order the slide posts, delete the slides from the slider etc.','pointelle-slider'); ?>" class="button-primary" style="font-size:13px;margin-bottom:5px;"><?php _e('Go to Sliders Admin','pointelle-slider'); ?></a>
<div class="svilla_cl"></div>

<div class="postbox" style="margin:10px 0;"> 
			  <h3 class="hndle"><span></span><?php _e('Available Settings Sets','pointelle-slider'); ?></h3> 
			  <div class="inside">
<?php 
for($i=1;$i<=$scounter;$i++){
   if ($i==1){
      echo '<h4><a href="'.pointelle_sslider_admin_url( array( 'page' => 'pointelle-slider-settings' ) ).'" title="(Settings Set ID '.$i.')">Default Settings (ID '.$i.')</a></h4>';
   }
   else {
      if($settings_set=get_option('pointelle_slider_options'.$i)){
		echo '<h4><a href="'.pointelle_sslider_admin_url( array( 'page' => 'pointelle-slider-settings' ) ).'&scounter='.$i.'" title="(Settings Set ID '.$i.')">'.$settings_set['setname'].' (ID '.$i.')</a></h4>';
	  }
   }
}
?>
</div></div>

<div class="postbox"> 
<div style="background:#eee;line-height:200%"><a style="text-decoration:none;font-weight:bold;font-size:100%;color:#990000" href="http://guides.slidervilla.com/pointelle-slider/" title="Click here to read how to use the plugin and frequently asked questions about the plugin" target="_blank"> ==> Usage Guide and General FAQs</a></div>
</div>

<?php if ($pointelle_slider['support'] == "1"){ ?>
    
     		<div class="postbox"> 
			  <h3 class="hndle"><span></span><?php _e('Recommended Themes','pointelle-slider'); ?></h3> 
			  <div class="inside">
                     <div style="margin:10px 5px">
                        <a href="http://slidervilla.com/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank"><img src="<?php echo pointelle_slider_plugin_url('images/elegantthemes.gif');?>" alt="Recommended WordPress Themes" style="width:100%;" /></a>
                        <p><a href="http://slidervilla.com/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank">Elegant Themes</a> are attractive, compatible, affordable, SEO optimized WordPress Themes and have best support in community.</p>
                        <p><strong>Beautiful themes, Great support!</strong></p>
                        <p><a href="http://slidervilla.com/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank">For more info visit ElegantThemes</a></p>
                     </div>
               </div></div>
          
			<div class="postbox"> 
			  <h3 class="hndle"><span><?php _e('About this Plugin:','pointelle-slider'); ?></span></h3> 
			  <div class="inside">
                <ul>
                <li><a href="http://slidervilla.com/pointelle/" title="<?php _e('Pointelle Slider Homepage','pointelle-slider'); ?>
" ><?php _e('Plugin Homepage','pointelle-slider'); ?></a></li>
				<li><a href="http://support.slidervilla.com/" title="<?php _e('Support Forum','pointelle-slider'); ?>
" ><?php _e('Support Forum','pointelle-slider'); ?></a></li>
				<li><a href="http://guides.slidervilla.com/pointelle-slider/" title="<?php _e('Usage Guide','pointelle-slider'); ?>
" ><?php _e('Usage Guide','pointelle-slider'); ?></a></li>
				<li><strong>Current Version: 1.6</strong></li>
                </ul> 
              </div> 
			</div> 
	<?php } ?>
                 
 </div> <!--end of poststuff --> 

<div style="clear:left;"></div>
<div style="clear:right;"></div>

</div> <!--end of float wrap -->
<?php	
}

function register_pointelle_settings() { // whitelist options
  $scounter=get_option('pointelle_slider_scounter');
  for($i=1;$i<=$scounter;$i++){
	   if ($i==1){
		  register_setting( 'pointelle-slider-group', 'pointelle_slider_options' );
	   }
	   else {
	      $group='pointelle-slider-group'.$i;
		  $options='pointelle_slider_options'.$i;
		  register_setting( $group, $options );
	   }
  }
}
?>