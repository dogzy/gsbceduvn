<?php
function return_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo='0',$data=array()){
	$slider_html='';
	$slider_html=get_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo,$data);
	return $slider_html;
}

function return_pointelle_slider($slider_id='',$set='',$offset=0,$data=array()) {
	global $pointelle_slider; 
 	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}
 
	if($pointelle_slider['multiple_sliders'] == '1' and is_singular() and (empty($slider_id) or !isset($slider_id))){
		global $post;
		$post_id = $post->ID;
		$slider_id = get_pointelle_slider_for_the_post($post_id);
	}
	if(empty($slider_id) or !isset($slider_id)){
	  $slider_id = '1';
	}
	$slider_handle='pointelle_slider_'.$slider_id;
	$slider_html='';
	if(!empty($slider_id)){
		$data['slider_handle']=$slider_handle;
		$r_array = pointelle_carousel_posts_on_slider($pointelle_slider_curr['no_posts'], $offset, $slider_id, $echo = '0', $set,$data); 
		$slider_html=return_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo='0',$data);
	} //end of not empty slider_id condition
	
	return $slider_html;
}

function pointelle_slider_simple_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => '',
		'set' => '',
		'offset'=>'0',
	), $atts));

	return return_pointelle_slider($id,$set,$offset);
}
add_shortcode('pointelleslider', 'pointelle_slider_simple_shortcode');

function return_pointelle_slider_category($catg_slug='', $set='', $offset=0, $data=array()) {
	global $pointelle_slider; 
 	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}
	$slider_handle='pointelle_slider_'.$catg_slug;
	$data['slider_handle']=$slider_handle;
    $r_array = pointelle_carousel_posts_on_slider_category($pointelle_slider_curr['no_posts'], $catg_slug, $offset, '0', $set, $data); 
	//get slider 
	$slider_html=return_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo='0',$data);
	
	return $slider_html;
}

function pointelle_slider_category_shortcode($atts) {
	extract(shortcode_atts(array(
		'catg_slug' => '',
		'set' => '',
		'offset'=>'0',
	), $atts));

	return return_pointelle_slider_category($catg_slug,$set,$offset);
}
add_shortcode('pointellecategory', 'pointelle_slider_category_shortcode');

function return_pointelle_slider_recent($set='',$offset=0, $data=array()) {
	global $pointelle_slider; 
 	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}
	$slider_handle='pointelle_slider_recent';
	$data['slider_handle']=$slider_handle;
	$r_array = pointelle_carousel_posts_on_slider_recent($pointelle_slider_curr['no_posts'], $offset, '0', $set,$data);  
	//get slider 
	$slider_html=return_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo='0',$data);
	
	return $slider_html;
}

function pointelle_slider_recent_shortcode($atts) {
	extract(shortcode_atts(array(
		'set' => '',
		'offset'=>'0',
	), $atts));
	return return_pointelle_slider_recent($set,$offset);
}
add_shortcode('pointellerecent', 'pointelle_slider_recent_shortcode');
?>