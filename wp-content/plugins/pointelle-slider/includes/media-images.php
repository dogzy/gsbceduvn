<?php
//For media files
function pointelle_slider_media_lib_edit($form_fields, $post){
global $wp_version;
if ( version_compare( $wp_version, '3.5', '<' ) ) : // Using WordPress less than 3.5
	global $pointelle_slider;
	if (current_user_can( $pointelle_slider['user_level'] )) {
		$remove_post_type_arr=$pointelle_slider['remove_metabox'];
		if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		if(!in_array('attachment',$remove_post_type_arr)){
			if ( substr($post->post_mime_type, 0, 5) == 'image') {
				$post_id = $post->ID;
				$extra = "";

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
				
					  
			  $form_fields['pointelle-slider'] = array(
					  'label'      => __('Check the box and select the slider','pointelle-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='checkbox' style='margin-top:6px;' name='attachments[".$post->ID."][pointelle-slider]' value='pointelle-slider' ".$extra." /> &nbsp; <strong>".__( 'Add this Image to ', 'pointelle-slider' )."</strong>",
					  'value'      => 'pointelle-slider'
				   );
			  
			  $sname_html='';
		 
			  foreach ($sliders as $slider) { 
				 if(in_array($slider['slider_id'],$post_slider_arr)){$selected = 'selected';} else{$selected='';}
				 $sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" '.$selected.'>'.$slider['slider_name'].'</option>';
			  } 
			  $form_fields['pointelle_slider_name[]'] = array(
					  'label'      => __(''),
					  'input'      => 'html',
					  'html'       => '<select name="attachments['.$post->ID.'][pointelle_slider_name][]" multiple="multiple" size="3">'.$sname_html.'</select>',
					  'value'      => ''
				   );
			 
			 $pointelle_link_attr=get_post_meta($post_id, 'pointelle_link_attr', true);
			 $pointelle_sslider_link= get_post_meta($post_id, 'pointelle_slide_redirect_url', true);  
			 $pointelle_sslider_nolink=get_post_meta($post_id, 'pointelle_sslider_nolink', true);
			 if($pointelle_sslider_nolink=='1'){$checked= "checked";} else {$checked= "";}
			 $form_fields['pointelle_link_attr'] = array(
					  'label'      => __('Slide Link (anchor) attributes','pointelle-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='text' style='clear:left;' class='text urlfield' name='attachments[".$post->ID."][pointelle_link_attr]' value='" . esc_attr($pointelle_link_attr) . "' /><br /><small>".__( '(e.g. target="_blank" rel="external nofollow")', 'pointelle-slider' )."</small>",
					  'value'      => $pointelle_link_attr
				   );
			 $form_fields['pointelle_sslider_link'] = array(
					  'label'      => __('Pointelle Slide Link URL','pointelle-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='text' style='clear:left;' class='text urlfield' name='attachments[".$post->ID."][pointelle_sslider_link]' value='" . esc_attr($pointelle_sslider_link) . "' /><br /><small>".__( '(If left empty, it will be by default linked to attachment permalink.)', 'pointelle-slider' )."</small>",
					  'value'      => $pointelle_sslider_link
				   );
			 $form_fields['pointelle_sslider_nolink'] = array(
					  'label'      => __('Do not link this slide to any page(url)','pointelle-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='checkbox' name='attachments[".$post->ID."][pointelle_sslider_nolink]' value='1' ".$checked." />",
					  'value'      => 'pointelle-slider'
				   );
			 $pointelle_slide_nav=get_post_meta($post_id, 'slide_nav', true); 
			 $form_fields['slide_nav'] = array(
					  'label'      => __('Pointelle Slide Navigation Title','pointelle-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='text' style='clear:left;' class='text urlfield' name='attachments[".$post->ID."][slide_nav]' value='" . esc_attr($pointelle_slide_nav) . "' /><br /><small>".__( '(If left empty, it will be picked from the Image title.)', 'pointelle-slider' )."</small>",
					  'value'      => $pointelle_slide_nav
				   );
			 $pointelle_nav_thumb=get_post_meta($post_id, 'nav_thumb', true); 
			 $form_fields['nav_thumb'] = array(
					  'label'      => __('Pointelle Slide Navigation Thumb URL','pointelle-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='text' style='clear:left;' class='text urlfield' name='attachments[".$post->ID."][nav_thumb]' value='" . esc_attr($pointelle_nav_thumb) . "' /><br /><small>".__( '(If left empty, the thumbnail image in navigation will be same as slide image.)', 'pointelle-slider' )."</small>",
					  'value'      => $pointelle_nav_thumb
				   );
		  }
		  else {
			 unset( $form_fields['pointelle-slider'] );
			 unset( $form_fields['pointelle_slider_name[]'] );
			 unset( $form_fields['pointelle_sslider_link'] );
			 unset( $form_fields['pointelle_sslider_nolink'] );
			 unset( $form_fields['pointelle_link_attr'] );
			 unset( $form_fields['slide_nav'] );
			 unset( $form_fields['nav_thumb'] );
		  }
		} //attachment post type
	} //current user can
endif; //less than WP 3.5
return $form_fields;
}

add_filter('attachment_fields_to_edit', 'pointelle_slider_media_lib_edit', 10, 2);

function pointelle_slider_media_lib_save($post, $attachment){
global $wp_version;
if ( version_compare( $wp_version, '3.5', '<' ) ) : // Using WordPress less than 3.5
	global $pointelle_slider;
	if (current_user_can( $pointelle_slider['user_level'] )) {
		$remove_post_type_arr=$pointelle_slider['remove_metabox'];
		if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		if(!in_array('attachment',$remove_post_type_arr)){
			global $wpdb, $table_prefix;
			$table_name = $table_prefix.POINTELLE_SLIDER_TABLE;
			$post_id=$post['ID'];
			
			if(isset($attachment['pointelle-slider']) and !isset($attachment['pointelle_slider_name'])) {
			  $slider_id = '1';
			  if(is_post_on_any_pointelle_slider($post_id)){
				 $sql = "DELETE FROM $table_name where post_id = '$post_id'";
				 $wpdb->query($sql);
			  }
			  
			  if(isset($attachment['pointelle-slider']) and $attachment['pointelle-slider'] == "pointelle-slider" and !pointelle_slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			  }
			}
			if(isset($attachment['pointelle-slider']) and $attachment['pointelle-slider'] == "pointelle-slider" and isset($attachment['pointelle_slider_name'])){
			  $slider_id_arr = $attachment['pointelle_slider_name'];
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
			
			$pointelle_link_attr = get_post_meta($post_id,'pointelle_link_attr',true);
			$link_attr=htmlentities($_POST['pointelle_link_attr'],ENT_QUOTES);
			if($pointelle_link_attr != $link_attr) {
			  update_post_meta($post_id, 'pointelle_link_attr', $link_attr);	
			}
		
			$pointelle_sslider_link = get_post_meta($post_id,'pointelle_slide_redirect_url',true);
			$link=$attachment['pointelle_sslider_link'];
			if($pointelle_sslider_link != $link) {
			  update_post_meta($post_id, 'pointelle_slide_redirect_url', $link);	
			}
			
			$pointelle_sslider_nolink = get_post_meta($post_id,'pointelle_sslider_nolink',true);
			if($pointelle_sslider_nolink != $attachment['pointelle_sslider_nolink']) {
			  update_post_meta($post_id, 'pointelle_sslider_nolink', $attachment['pointelle_sslider_nolink']);	
			}
			
			$pointelle_slide_nav = get_post_meta($post_id,'slide_nav',true);
			$post_pointelle_slide_nav = $attachment['slide_nav'];
			if($pointelle_slide_nav != $post_pointelle_slide_nav) {
			  update_post_meta($post_id, 'slide_nav', $post_pointelle_slide_nav);	
			}
			
			$pointelle_nav_thumb = get_post_meta($post_id,'nav_thumb',true);
			$post_pointelle_nav_thumb = $attachment['nav_thumb'];
			if($pointelle_nav_thumb != $post_pointelle_nav_thumb) {
			  update_post_meta($post_id, 'nav_thumb', $post_pointelle_nav_thumb);
			}
			
		} //attachment post type
	} //current user can
endif; //less than WP 3.5
return $post;
} 

add_filter('attachment_fields_to_save', 'pointelle_slider_media_lib_save', 10, 2);
?>