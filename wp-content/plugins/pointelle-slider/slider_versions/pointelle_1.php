<?php 
function pointelle_global_posts_processor( $posts, $pointelle_slider_curr,$out_echo,$set,$data=array() ){
	global $pointelle_slider;
	$pointelle_slider_css = pointelle_get_inline_css($set);
	$html = '';
	$pointelle_sldr_j = 0;
	
	$slider_handle='';
	if ( is_array($data) and isset($data['slider_handle']) ) {
		$slider_handle=$data['slider_handle'];
	}
	
	$timthumb='1';
	if($pointelle_slider_curr['timthumb']=='1'){
		$timthumb='0';
	}
	
	foreach($posts as $post) {
		$id = $post->ID;	
		$post_title = stripslashes($post->post_title);
		$post_title = str_replace('"', '', $post_title);
		$post_id = $post->ID;
		//filter hook
		$post_title=apply_filters('pointelle_post_title',$post_title,$post_id,$pointelle_slider_curr,$pointelle_slider_css);	
		$slider_content = $post->post_content;
	
		$pointelle_slide_redirect_url = get_post_meta($post_id, 'pointelle_slide_redirect_url', true);
		$pointelle_sslider_nolink = get_post_meta($post_id,'pointelle_sslider_nolink',true);
		trim($pointelle_slide_redirect_url);
		if(!empty($pointelle_slide_redirect_url) and isset($pointelle_slide_redirect_url)) {
		   $permalink = $pointelle_slide_redirect_url;
		}
		else{
		   $permalink = get_permalink($post_id);
		}
		if($pointelle_sslider_nolink=='1'){
		  $permalink='';
		}
		
		$pointelle_sldr_j++;
		
		if( $pointelle_slider_curr['navpos']=='0' ) $activeclass = 'pointelle-active-lt' ;
		else $activeclass = 'pointelle-active' ;
		
		if($pointelle_sldr_j == '1'){
			$nav_active = $activeclass;$div_slides='<div class="pointelle_slides" '.$pointelle_slider_css['pointelle_slides'].'>';
			$div_control='<div class="pointelle-slider-control" '.$pointelle_slider_css['pointelle_slider_control'].'> ';
		}
		else{
			$nav_active = '';$div_slides='';$div_control='';
		}
		$pointelle_slide_nav = get_post_meta($post_id,'slide_nav',true);
		
		if(!empty($pointelle_slide_nav) and isset($pointelle_slide_nav)) {
			   $slide_nav = $pointelle_slide_nav;
		}
		else{
		       $slide_nav = $post_title;
		}
		
		$slide_nav = pointelle_slider_word_limiter( $slide_nav, $limit = $pointelle_slider_curr['slide_nav_limit'] );
		//filter hook
		$slide_nav=apply_filters('pointelle_nav_title',$slide_nav,$post_id,$pointelle_slider_curr,$pointelle_slider_css);
			
		//meta1
		$meta1_parms=$pointelle_slider_curr['meta1_parms'];
		if(function_exists($pointelle_slider_curr['meta1_fn'])){
	    	$fn_name=$pointelle_slider_curr['meta1_fn'];
		    $meta1_value=$fn_name($post,$meta1_parms);
		}
		//meta2
		$meta2_parms=$pointelle_slider_curr['meta2_parms'];
		if(function_exists($pointelle_slider_curr['meta2_fn'])){
	    	$fn_name=$pointelle_slider_curr['meta2_fn'];
		    $meta2_value=$fn_name($post,$meta2_parms);
		}

		//Slide link anchor attributes
		$a_attr='';$imglink='';
		$a_attr=get_post_meta($post_id,'pointelle_link_attr',true);
		if( empty($a_attr) and isset( $pointelle_slider_curr['a_attr'] ) ) $a_attr=$pointelle_slider_curr['a_attr'];
		$a_attr_orig=$a_attr;
		if( isset($pointelle_slider_curr['pphoto']) and $pointelle_slider_curr['pphoto'] == '1' ){
			if($pointelle_slider_curr['pphoto'] == '1') $a_attr.='rel="prettyPhoto"';
			if(!empty($pointelle_slide_redirect_url) and isset($pointelle_slide_redirect_url))
				$imglink=$pointelle_slide_redirect_url;
			else $imglink='1';
		}
		
//All Images 
		$pointelle_media = get_post_meta($post_id,'pointelle_media',true);
		   	
		if($pointelle_slider_curr['img_pick'][0] == '1'){
		 $custom_key = array($pointelle_slider_curr['img_pick'][1]);
		}
		else {
		 $custom_key = '';
		}
		
		$nav_thumb_value = get_post_meta($post_id,'nav_thumb',true);
		if( !$nav_thumb_value or empty($nav_thumb_value) ){
			$nav_custom_key=$custom_key;
		}
		else{
			$nav_custom_key='nav_thumb';
		}
		
		if($pointelle_slider_curr['img_pick'][2] == '1'){
		 $the_post_thumbnail = true;
		}
		else {
		 $the_post_thumbnail = false;
		}
		
		if($pointelle_slider_curr['img_pick'][3] == '1'){
		 $attachment = true;
		 $order_of_image = $pointelle_slider_curr['img_pick'][4];
		}
		else{
		 $attachment = false;
		 $order_of_image = '1';
		}
		
		if($pointelle_slider_curr['img_pick'][5] == '1'){
			 $image_scan = true;
		}
		else {
			 $image_scan = false;
		}
		
       $gti_width = $pointelle_slider_curr['img_width'];
	   $gti_height = $pointelle_slider_curr['img_height'];
	   $nav_thumb_width = $pointelle_slider_curr['nav_img_width'];
	   $nav_thumb_height = $pointelle_slider_curr['nav_img_height'];
		
		if($pointelle_slider_curr['crop'] == '0'){
		 $extract_size = 'full';
		}
		elseif($pointelle_slider_curr['crop'] == '1'){
		 $extract_size = 'large';
		}
		elseif($pointelle_slider_curr['crop'] == '2'){
		 $extract_size = 'medium';
		}
		else{
		 $extract_size = 'thumbnail';
		}
		
		$img_args = array(
			'custom_key' => $nav_custom_key,
			'post_id' => $post_id,
			'attachment' => $attachment,
			'size' => 'thumbnail',
			'the_post_thumbnail' => $the_post_thumbnail,
			'default_image' => false,
			'order_of_image' => $order_of_image,
			'link_to_post' => false,
			'image_class' => 'pointelle_nav_thumb',
			'image_scan' => $image_scan,
			'width' => $nav_thumb_width,
			'height' => $nav_thumb_height,
			'echo' => false,
			'permalink' => '',
			'timthumb'=>$timthumb,
			'style'=> $pointelle_slider_css['pointelle_slider_nav_thumb']
		);		
		
		//on hover
		$anav='';$anav_close='';
		if( $pointelle_slider_curr['onhover'] == '1' ) { 
			$anav='<a style="display:block;" href="'.$permalink.'" '.$a_attr_orig.'>';
			$anav_close='</a>';
		}
		
		$navigation .=$div_control.'<div class="pointelle-slider-nav '.$nav_active.'" '.$pointelle_slider_css['pointelle_slider_nav'].'>'.$anav;
		if($pointelle_slider_curr['disable_thumbs'] != '1')	{
			$navigation_image=pointelle_sslider_get_the_image($img_args);
			//filter hook
			$navigation_image=apply_filters('pointelle_nav_thumb',$navigation_image,$post_id,$pointelle_slider_curr,$pointelle_slider_css);
			$navigation .=  $navigation_image;
		}
		
		$pointelle_meta='<span class="pointelle-meta" '.$pointelle_slider_css['pointelle_meta'].'><span class="pointelle-meta1">'.$pointelle_slider_curr['meta1_before'].'<span class="pointelle-meta1-value">'.$meta1_value.'</span>'.$pointelle_slider_curr['meta1_after'].'</span><span class="pointelle-meta2">'.$pointelle_slider_curr['meta2_before'].'<span class="pointelle-meta2-value">'.$meta2_value.'</span>'.$pointelle_slider_curr['meta2_after']. '</span></span>';	
		//filter hook
		$pointelle_meta=apply_filters('pointelle_meta_html',$pointelle_meta,$post_id,$pointelle_slider_curr,$pointelle_slider_css);
		
		if($pointelle_slider_curr['disable_navtext'] == '1') $navtext='';
		else $navtext =  '<h2 '.$pointelle_slider_css['pointelle_slider_nav_h2'].'>'.$slide_nav.'</h2> 
						'.$pointelle_meta;
		
		$navigation .= $navtext.'<span class="pointelle-order">'.$pointelle_sldr_j.'</span> 
					<div class="sldr_clearlt"></div>'.$anav_close.'</div>';
		
		$html .= $div_slides.'<div class="pointelle_slideri" '.$pointelle_slider_css['pointelle_slideri'].'>
			<!-- pointelle_slide -->';
					
		$more_html='';					
		if($pointelle_slider_curr['show_title']=='1'){
		   if($permalink!='') { $slide_title = '<h4 '.$pointelle_slider_css['pointelle_slider_h4'].'><a href="'.$permalink.'" '.$pointelle_slider_css['pointelle_slider_h4_a'].' '.$a_attr_orig.' >'.$post_title.'</a></h4>';  
				$morefield=$pointelle_slider_curr['readmore'];
				if( !empty($morefield) and $morefield ){
					$more_html= '<p class="more"><a href="'.$permalink.'" '.$pointelle_slider_css['pointelle_slider_p_more'].' '.$a_attr_orig.'>'.$morefield.'</a></p>';
				} 
		   }
		   else{ $slide_title = '<h4 '.$pointelle_slider_css['pointelle_slider_h4'].'>'.$post_title.'</h4>';  }
		}
		else{
		   $slide_title = '';
		}
		//filter hook
		$slide_title=apply_filters('pointelle_slide_title_html',$slide_title,$post_id,$pointelle_slider_curr,$pointelle_slider_css);
		
		if ($pointelle_slider_curr['content_from'] == "slider_content") {
			$slider_content = get_post_meta($post_id, 'slider_content', true);
		}
		if ($pointelle_slider_curr['content_from'] == "excerpt") {
			$slider_content = $post->post_excerpt;
		}

		$slider_content = strip_shortcodes( $slider_content );

		$slider_content = stripslashes($slider_content);
		$slider_content = str_replace(']]>', ']]&gt;', $slider_content);

		$slider_content = str_replace("\n","<br />",$slider_content);
		$slider_content = strip_tags($slider_content, $pointelle_slider_curr['allowable_tags']);
		
		if(!$pointelle_slider_curr['content_limit'] or $pointelle_slider_curr['content_limit'] == '' or $pointelle_slider_curr['content_limit'] == ' ') 
		  $slider_excerpt = substr($slider_content,0,$pointelle_slider_curr['content_chars']);
		else 
		  $slider_excerpt = pointelle_slider_word_limiter( $slider_content, $limit = $pointelle_slider_curr['content_limit'], $dots = '...' );
		
		//filter hook
		$slider_excerpt=apply_filters('pointelle_slide_excerpt',$slider_excerpt,$post_id,$pointelle_slider_curr,$pointelle_slider_css);
		$trimmed=trim($slider_excerpt);
		if( $pointelle_slider_curr['show_content']=='1' and !empty($trimmed) )
			$slider_excerpt='<p '.$pointelle_slider_css['pointelle_excerpt_p'].'> '.$slider_excerpt.'</p>';
		else
			$slider_excerpt='';
		
		//filter hook
		$slider_excerpt=apply_filters('pointelle_slide_excerpt_html',$slider_excerpt,$post_id,$pointelle_slider_curr,$pointelle_slider_css);
		
		$pointelle_fields=$pointelle_slider_curr['fields'];		
		$fields_html='';
		if($pointelle_fields and !empty($pointelle_fields) ){
			$fields=explode( ',', $pointelle_fields );
			if($fields){
				foreach($fields as $field) {
					$field_val = get_post_meta($post_id, $field, true);
					if( $field_val and !empty($field_val) )
						$fields_html .='<div class="pointelle_'.$field.' pointelle_fields">'.$field_val.'</div>';
				}
			}
		}
				
		$img_args = array(
			'custom_key' => $custom_key,
			'post_id' => $post_id,
			'attachment' => $attachment,
			'size' => $extract_size,
			'the_post_thumbnail' => $the_post_thumbnail,
			'default_image' => false,
			'order_of_image' => $order_of_image,
			'link_to_post' => false,
			'image_class' => 'pointelle_slider_thumbnail',
			'image_scan' => $image_scan,
			'width' => $gti_width,
			'height' => $gti_height,
			'echo' => false,
			'permalink' => $permalink,
			'timthumb'=>$timthumb,
			'style'=> $pointelle_slider_css['pointelle_slider_thumbnail'],
			'a_attr'=> $a_attr,
			'imglink'=>$imglink
		);
		
		if( empty($pointelle_media) or $pointelle_media=='' or !($pointelle_media) ) {  
			$pointelle_large_image=pointelle_sslider_get_the_image($img_args);
		}
		else{
			$pointelle_large_image=$pointelle_media;
		}
		//filter hook
		$pointelle_large_image=apply_filters('pointelle_large_image',$pointelle_large_image,$post_id,$pointelle_slider_curr,$pointelle_slider_css);
		$html .= $pointelle_large_image;
		
		$protect='';
		if($pointelle_slider_curr['copyprotect'] == '1'){
		  if($permalink!='')
			$protect= '<a href="'.$permalink.'" ><span class="pointelle_overlay"></span></a>';	
		  else
		    $protect= '<span class="pointelle_overlay"></span>';	
		} 		
		if ($pointelle_slider_curr['image_only'] == '1') { 
			$html .= $protect.'</div>';
		}
		else {
		   if( !empty($slide_title) or !empty($slider_excerpt) )  $excerpt='<div class="pointelle-excerpt" '.$pointelle_slider_css['pointelle-excerpt'].'>'.$slide_title.$slider_excerpt.$fields_html.$more_html.'</div>';
		   else $excerpt='';
		   
		   $html .= $protect.$excerpt.'</div>';
		}
	}
	if($posts){
		$html .='<div id="pointelle_nav_next" class="pointelle_slide_arrow pointelle_nav_next"></div><div id="pointelle_nav_prev" class="pointelle_slide_arrow pointelle_nav_prev"></div></div>';
		$navigation .='</div>';
	}
	//If disable navigation is set true
	if($pointelle_slider_curr['disable_nav'] == '1')	$navigation =  '';
	
	if( $pointelle_slider_curr['navpos']=='0' ) $html = $navigation . $html ;
	else $html = $html . $navigation ; 
	
	$html=apply_filters('pointelle_extract_html',$html,$pointelle_sldr_j,$posts,$pointelle_slider_curr);
	
	if($out_echo == '1') {
	   echo $html;
	}
	$r_array = array( $pointelle_sldr_j, $html);
	$r_array=apply_filters('pointelle_r_array',$r_array,$posts, $pointelle_slider_curr,$set);
	return $r_array;
}

function get_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo='1',$data=array()){
	global $pointelle_slider; 
	$pointelle_sldr_j = $r_array[0];
	$pointelle_slider_css = pointelle_get_inline_css($set); 
	$slider_html='';
	
	$pointelle_media_queries='';$pointelle_media_queries_after='';
	$responsive_max_width=($pointelle_slider_curr['width']>0)?( $pointelle_slider_curr['width'].'px'  ) : ( '100%' );
	
	$w_241=($pointelle_slider_curr['w_241']>0)?($pointelle_slider_curr['w_241']) : ('175');
	$w_320=($pointelle_slider_curr['w_320']>0)?($pointelle_slider_curr['w_320']) : ('255');
	$w_400=($pointelle_slider_curr['w_400']>0)?($pointelle_slider_curr['w_400']) : ('315');
	$w_480=($pointelle_slider_curr['w_480']>0)?($pointelle_slider_curr['w_480']) : ('415');
	$w_568=($pointelle_slider_curr['w_568']>0)?($pointelle_slider_curr['w_568']) : ('500');
	$w_685=($pointelle_slider_curr['w_685']>0)?($pointelle_slider_curr['w_685']) : ('620');
	$w_768=($pointelle_slider_curr['w_768']>0)?($pointelle_slider_curr['w_768']) : ('700');
	$w_960=($pointelle_slider_curr['w_960']>0)?($pointelle_slider_curr['w_960']) : ('800');
	
	//Device Specific
	
	$slide_width_241_319=$w_241;
	$slide_height_241_319= ( ( $pointelle_slider_curr['height'] * $w_241 ) /  $pointelle_slider_curr['img_width'] );
	
	//iPhone 3 + 4 + 5 Potrait
	$slide_width_320_399=$w_320;
	$slide_height_320_399= ( ( $pointelle_slider_curr['height'] * $w_320 ) /  $pointelle_slider_curr['img_width'] );
	
	$slide_width_400_479=$w_400;
	$slide_height_400_479= ( ( $pointelle_slider_curr['height'] * $w_400 ) /  $pointelle_slider_curr['img_width'] );
	
	//iPhone 3 + 4 Landscape
	$slide_width_480_567=$w_480;
	$slide_height_480_567= ( ( $pointelle_slider_curr['height'] * $w_480 ) /  $pointelle_slider_curr['img_width'] );
	
	//iPhone 5 Landscape
	if($pointelle_slider_curr['disable_nav'] != '1') $nav_width_568_684=( $pointelle_slider_curr['nav_img_width'] + 32 );
	else $nav_width_568_684=0;
	$slide_width_568_684= ( $w_568 - $nav_width_568_684 );
	
	//Android (Samsung Galaxy) landscape
	if($pointelle_slider_curr['disable_nav'] != '1') $nav_width_685_767=( $pointelle_slider_curr['nav_img_width'] + 32 );
	else $nav_width_685_767=0;
	$slide_width_685_767= ( $w_685 - $nav_width_685_767 );
	
	//iPad Potrait
	if($pointelle_slider_curr['disable_nav'] != '1') $nav_width_768_959=( $pointelle_slider_curr['nav_img_width'] + 32 );
	else $nav_width_768_959=0;
	$slide_width_768_959=( $w_768 - $nav_width_768_959 );
	
	if($pointelle_slider_curr['disable_nav'] != '1') $nav_width_960_1023=( $pointelle_slider_curr['nav_control_w'] );
	else $nav_width_960_1023=0;
	$slide_width_960_1023=( $w_960 - $nav_width_960_1023 );

    if( $pointelle_slider_curr['responsive'] == '1' ) {
		$pointelle_media_queries='.pointelle_slider_set'.$set.'.pointelle_slider{width:100% !important;max-width:'.$responsive_max_width.' !important;display:block;}
		.pointelle_slider_set'.$set.' .pointelle_slides{margin:0 auto !important;}
		.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{max-width:100% !important;}
		@media only screen and (max-width: 219px) {
			.pointelle_slider_set'.$set.'.pointelle_slider{display:none !important;}
		}
		@media only screen and (min-width: 220px) and (max-width: 299px) {
			.pointelle_slider_set'.$set.'{height:'.$slide_height_241_319.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slides{width:'.$slide_width_241_319.'px !important;height:'.$slide_height_241_319.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slideri{width:100% !important;height:'.$slide_height_241_319.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{width:100% !important;;height:'.$slide_height_241_319.'px !important;;}
			.pointelle_slider_set'.$set.' .pointelle-excerpt,.pointelle_slider_set'.$set.' .pointelle-slider-control{display:none;}
		}
		@media only screen and (min-width: 300px) and (max-width: 374px) {
			.pointelle_slider_set'.$set.'{height:'.$slide_height_320_399.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slides{width:'.$slide_width_320_399.'px !important;height:'.$slide_height_320_399.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slideri{width:100% !important;height:'.$slide_height_320_399.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{width:100% !important;;height:'.$slide_height_320_399.'px !important;;}
			.pointelle_slider_set'.$set.' .pointelle-excerpt,.pointelle_slider_set'.$set.' .pointelle-slider-control{display:none;}
		}
		@media only screen and (min-width: 375px) and (max-width: 459px) {
			.pointelle_slider_set'.$set.'{height:'.$slide_height_400_479.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slides{width:'.$slide_width_400_479.'px !important;;height:'.$slide_height_400_479.'px !important;;}
			.pointelle_slider_set'.$set.' .pointelle_slideri{width:100% !important;height:'.$slide_height_400_479.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{width:100% !important;;height:'.$slide_height_400_479.'px !important;;}
			.pointelle_slider_set'.$set.' .pointelle-excerpt,.pointelle_slider_set'.$set.' .pointelle-slider-control{display:none;}
		}
		@media only screen and (min-width: 460px) and (max-width: 544px) {
			.pointelle_slider_set'.$set.'{height:'.$slide_height_480_567.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slides{width:'.$slide_width_480_567.'px !important;;height:'.$slide_height_480_567.'px !important;;}
			.pointelle_slider_set'.$set.' .pointelle_slideri{width:100% !important;height:'.$slide_height_480_567.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{width:100% !important;;height:'.$slide_height_480_567.'px !important;;}
			.pointelle_slider_set'.$set.' .pointelle-excerpt,.pointelle_slider_set'.$set.' .pointelle-slider-control{display:none;}
		}
		@media only screen and (min-width: 545px) and (max-width: 659px) {
			.pointelle_slider_set'.$set.' .pointelle_slides{width:'.$slide_width_568_684.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slideri{width:100% !important;height:100%;}
			.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{max-width:100%;height:auto;margin:0 auto !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-control{width:'.$nav_width_568_684.'px !important;left:'.$slide_width_568_684.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-nav .pointelle_nav_thumb{margin:0 !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-nav h2,.pointelle_slider_set'.$set.' .pointelle-slider-nav  .pointelle-meta{display:none;}
		}
		@media only screen and (min-width: 660px) and (max-width: 739px) {
			.pointelle_slider_set'.$set.' .pointelle_slides{width:'.$slide_width_685_767.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slideri{width:100% !important;height:100%;}
			.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{max-width:100%;height:auto;margin:0 auto !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-control{width:'.$nav_width_685_767.'px !important;left:'.$slide_width_685_767.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-nav .pointelle_nav_thumb{margin:0 !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-nav h2,.pointelle_slider_set'.$set.' .pointelle-slider-nav  .pointelle-meta{display:none;}
		}
		@media only screen and (min-width: 740px) and (max-width: 959px) {
			.pointelle_slider_set'.$set.' .pointelle_slides{width:'.$slide_width_768_959.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slideri{width:100%;height:100%;}
			.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{max-width:100%;height:auto;margin:0 auto !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-control{width:'.$nav_width_768_959.'px !important;left:'.$slide_width_768_959.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-nav .pointelle_nav_thumb{margin:0 !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-nav h2,.pointelle_slider_set'.$set.' .pointelle-slider-nav  .pointelle-meta{display:none;}
		}
		@media only screen and (min-width: 960px) and (max-width: 1000px) {
			.pointelle_slider_set'.$set.' .pointelle_slides{width:'.$slide_width_960_1023.'px !important;}
			.pointelle_slider_set'.$set.' .pointelle_slideri{width:100%;height:100%;}
			.pointelle_slider_set'.$set.' .pointelle_slider_thumbnail{max-width:100%;height:auto;margin:0 auto !important;}
			.pointelle_slider_set'.$set.' .pointelle-slider-control{width:'.$nav_width_960_1023.'px !important;left:'.$slide_width_960_1023.'px !important;}
		}';
		//filter hook
		$pointelle_media_queries=apply_filters('pointelle_media_queries',$pointelle_media_queries,$pointelle_slider_curr,$set);
		$line_breaks = array("\r\n", "\n", "\r");
		$pointelle_media_queries = str_replace($line_breaks, "", $pointelle_media_queries);
		
		if( $pointelle_slider_curr['scroll_nav_posts'] < $pointelle_slider_curr['no_posts'] ){
			$pointelle_media_queries_after='@media only screen and (max-width: 219px) {
				.pointelle_slider_set'.$set.' .pointelle_nav_wrapper,.pointelle_slider_set'.$set.' 	.pointelle_nav_arrows{display:none;}
			}	
			@media only screen and (min-width: 220px) and  (max-width: 544px) {
				.pointelle_slider_set'.$set.' .pointelle_nav_wrapper,.pointelle_slider_set'.$set.' .pointelle_nav_arrows{display:none;}
			}
			@media only screen and (min-width: 545px) and (max-width: 659px) {
				.pointelle_slider_set'.$set.' .pointelle_nav_wrapper{width:'.( $nav_width_568_684 + 2) .'px !important;left:'.$slide_width_568_684.'px !important;}
				.pointelle_slider_set'.$set.' .pointelle-slider-control{left:0 !important;}
				.pointelle_slider_set'.$set.' .pointelle_nav_arrows{display:none;}
			}
			@media only screen and (min-width: 660px) and (max-width: 739px) {
				.pointelle_slider_set'.$set.' .pointelle_nav_wrapper{width:'.( $nav_width_685_767 + 2) .'px !important;left:'.$slide_width_685_767.'px !important;}
				.pointelle_slider_set'.$set.' .pointelle-slider-control{left:0 !important;}
				.pointelle_slider_set'.$set.' .pointelle_nav_arrows{display:none;}
			}
			@media only screen and (min-width: 740px) and (max-width: 959px) {
				.pointelle_slider_set'.$set.' .pointelle_nav_wrapper{width:'.( $nav_width_768_959 + 2) .'px !important;left:'.$slide_width_768_959.'px !important;}
				.pointelle_slider_set'.$set.' .pointelle-slider-control{left:0 !important;}
				.pointelle_slider_set'.$set.' .pointelle_nav_arrows{display:none;}
			}
			@media only screen and (min-width: 960px) and (max-width: 1000px) {
				.pointelle_slider_set'.$set.' .pointelle_nav_wrapper{width:'.( $nav_width_960_1023 + 2).'px !important;left:'.$slide_width_960_1023.'px !important;}
				.pointelle_slider_set'.$set.' .pointelle-slider-control{left:0 !important;}
				.pointelle_slider_set'.$set.' .pointelle_nav_arrows{display:none;}
			}';
			$pointelle_media_queries_after = str_replace($line_breaks, "", $pointelle_media_queries_after);
		}
	}
	
	if( $pointelle_slider_curr['navpos']=='0' ) $activeclass = 'pointelle-active-lt' ;
	else $activeclass = 'pointelle-active' ;

	$texthoveron='';
	$texthoveroff='';
	$textstatus='';
	if ($pointelle_slider_curr['hovercontent'] == '1') {
	   $texthoveron='jQuery(this).find(".pointelle-excerpt").stop(true,true).slideDown(400);';
	   $texthoveroff='jQuery(this).find(".pointelle-excerpt").stop(true,true).slideUp(550);';
	   $textstatus='jQuery(".pointelle-excerpt").hide();';
	}
	
	//Transition - on hover or on click
	if( $pointelle_slider_curr['onhover'] == '1' ) {
		$transitionon='$pointelle_item.hover( function() { pause_scroll= true;pointelle_gonext(jQuery(this)); return false;}, 
							   function() { pause_scroll= false;});	';
	}
	else{
		if(! defined('POINTELLE_CONTINUE_ONCLICK')) $stoponclick='clearInterval(interval);';
		else $stoponclick='';
		$transitionon='$pointelle_item.click(function() {'.$stoponclick.'pointelle_gonext(jQuery(this)); return false;	});
				$pointelle_item.hover(function () {	pause_scroll= true;	}, 
								function () { pause_scroll= false;});';
	}
	
	//Autoslide - On or Off
	 if( $pointelle_slider_curr['autoslide'] != '0' ) {
		$autoslide='interval = setInterval(function () {
				var auto_number = $slider_control.find(".'.$activeclass.' span.pointelle-order").html();
				if (auto_number == $pointelle_item.length) auto_number = 0;
				$pointelle_item.eq(auto_number).trigger("pointelle_autonext");
			}, '. ( $pointelle_slider_curr["pause"] * 1000) .');';
	} 
	else{
		$autoslide='';
	}
	
	if($pointelle_slider_curr['disable_nav'] == '1' and $pointelle_slider_curr['autoslide'] != '0' ) $timeout= $pointelle_slider_curr['pause'] * 1000 ;
	else $timeout = 0;
	//Next Prev Arrows 
	$nextprev_arrows='';$js_slide_arrows='';
	if( $pointelle_slider_curr['nextprev'] != '0' ){
		$nextprev_arrows='next:   "#'.$slider_handle.' .pointelle_nav_next", prev:   "#'.$slider_handle.' .pointelle_nav_prev",onPrevNextEvent:pointelle_manual_transition,';
		$js_slide_arrows='var $pointelle_slide_arrow=jQuery("#'.$slider_handle.' .pointelle_slide_arrow");
		$pointelle_wrapper.hover(	function () {$pointelle_slide_arrow.stop(true,true).css("display","block");},
								function () {$pointelle_slide_arrow.stop(true,true).css("display","none");} );
		$pointelle_slide_arrow.hover(	function () {pause_scroll= true;},function () {pause_scroll= false;} );';
	}

	$js_scroll_nav='';$html_scroll_nav='';$js_scroll_advance='';
	if( $pointelle_slider_curr['scroll_nav_posts'] < $pointelle_slider_curr['no_posts'] ){
		wp_enqueue_script( 'jquery.carouFredSel', pointelle_slider_plugin_url( 'js/carouFredSel.js' ),
					array('jquery'), POINTELLE_SLIDER_VER, false); 
		$js_scroll_nav='jQuery("#'.$slider_handle.'  .pointelle-slider-control").carouFredSel({items:'. $pointelle_slider_curr['scroll_nav_posts'].',auto:false,direction:"up",next:{button:"#'. $slider_handle.' .pointelle_nav_down",items:'.$pointelle_slider_curr['scroll_nav_posts'].'}, prev:{button:"#'. $slider_handle.' .pointelle_nav_up",items:'.$pointelle_slider_curr['scroll_nav_posts'].'} }, { wrapper:{classname:"pointelle_nav_wrapper"} }	);
			jQuery("#'.$slider_handle.' .pointelle_nav_arrows").hover( function () {pause_scroll= true;},function () {pause_scroll= false;} );var pointelle_nav_wrapper_attr=jQuery("#'.$slider_handle.' .pointelle_nav_wrapper").attr("style");jQuery("#'.$slider_handle.' .pointelle_nav_wrapper").attr("style",pointelle_nav_wrapper_attr+"width:'.( $pointelle_slider_curr['nav_control_w'] + 2 ).'px;height:'.$pointelle_slider_curr['nav_control_h'].'px;");';
		$html_scroll_nav='<div class="pointelle_nav_arrows"><div id="pointelle_nav_down" class="pointelle_nav_down"></div><div id="pointelle_nav_up" class="pointelle_nav_up"></div></div>';
		$js_scroll_advance='jQuery("#'. $slider_handle.'  .pointelle-slider-control").trigger("slideTo", ordernumber - 1);';
	}
	
	if(!isset($pointelle_slider_curr['fouc']) or $pointelle_slider_curr['fouc']=='0' ){
		$fouc='jQuery("html").addClass("pointelle_slider_fouc");
	jQuery(document).ready(function() {   jQuery(".pointelle_slider_fouc #'.$slider_handle.'").css({"display" : "block"}); '.$textstatus.'});';
    }	
	else{
	    $fouc='';
	}	
	
	if($pointelle_slider_curr['disable_nav'] != '1') {
		$pointellenavjs='function pointelle_manual_transition(isNext){
			var manual_number = $slider_control.find(".'.$activeclass.' span.pointelle-order").html();
			if(!isNext) manual_number=manual_number-2;;
			if ( (manual_number == $pointelle_item.length) && isNext ) manual_number = 0;
			if ( (manual_number < 0) && !isNext ) manual_number = $pointelle_item.length-1;
			manual_transition=true;
			$pointelle_item.eq(manual_number).trigger("pointelle_autonext");
		}
		'.$js_scroll_nav.'
		$pointelle_item.find("img").fadeTo("fast", 0.7);$slider_control.find(".'.$activeclass.' img").fadeTo("fast", 1);
		function pointelle_gonext(this_element){
			$slider_control.find(".'.$activeclass.' img").stop(true,true).fadeTo("fast", 0.7);
			$slider_control.find(".'.$activeclass.'").removeClass("'.$activeclass.'");
			this_element.addClass("'.$activeclass.'");
			$slider_control.find(".'.$activeclass.' img").stop(true,true).fadeTo("fast", 1);
			ordernumber = this_element.find("span.pointelle-order").html();
			'.$js_scroll_advance.'
			if(!manual_transition) jQuery("#'.$slider_handle.' .pointelle_slides").cycle(ordernumber - 1);
			manual_transition=false;
		} 
		'.$transitionon.'
		var auto_number;var interval;
		$pointelle_item.bind("pointelle_autonext", function pointelle_autonext(){
			if (!(pause_scroll)  || manual_transition) pointelle_gonext(jQuery(this)); 
			return false;
		});
		'.$autoslide;
	}
	else{
		$pointellenavjs='function pointelle_manual_transition(){}';
	}
	
	$slider_html=$slider_html.'<script type="text/javascript">'.$fouc;
	
	if(!empty($pointelle_media_queries)){
			$slider_html.='jQuery(document).ready(function() {jQuery("head").append("<style type=\"text/css\">'. $pointelle_media_queries .'</style>");});';
	}
	
	$slider_html=$slider_html.'jQuery(document).ready(function(){
		jQuery("#'.$slider_handle.' .pointelle_slides").cycle({	timeout: '. $timeout.', speed: '. ( $pointelle_slider_curr['speed'] * 100).',	fx: "'.$pointelle_slider_curr['transition'].'",'.$nextprev_arrows.' slideExpr: "div.pointelle_slideri" });	var manual_transition=false;
		var $pointelle_wrapper = jQuery("#'.$slider_handle.'");var $pointelle_item = jQuery("#'.$slider_handle.' div.pointelle-slider-nav");var $slider_control = jQuery("#'.$slider_handle.'  .pointelle-slider-control");var $image_container = jQuery("#'.$slider_handle.' .pointelle_slideri");var ordernumber;var pause_scroll = false;$image_container.css("height","'.$pointelle_slider_curr['height'].'px");
		'.$js_slide_arrows.'
		$image_container.hover(	function () {jQuery(this).find("img").stop(true,true).fadeTo("fast", 0.7);pause_scroll= true;'.$texthoveron.'}, 
							 function () {jQuery(this).find("img").stop(true,true).fadeTo("fast", 1);pause_scroll= false;'. $texthoveroff.'});
		'.$pointellenavjs;
		
	if(!empty($pointelle_media_queries_after)){
		$slider_html.='jQuery(document).ready(function() {jQuery("head").append("<style type=\"text/css\">'. $pointelle_media_queries_after .'</style>");});';
	}
	$slider_html=$slider_html.'});	';
	
	if($pointelle_slider_curr['pphoto'] == '1') {
		wp_enqueue_script( 'jquery.prettyPhoto', pointelle_slider_plugin_url( 'js/jquery.prettyPhoto.js' ),
							array('jquery'), POINTELLE_SLIDER_VER, false);
		wp_enqueue_style( 'prettyPhoto_css', pointelle_slider_plugin_url( 'css/prettyPhoto.css' ),
				false, POINTELLE_SLIDER_VER, 'all');
		$lightbox_script='jQuery(document).ready(function(){
			jQuery("a[rel^=\'prettyPhoto\']").prettyPhoto({deeplinking: false,social_tools:false});
		});';
		//filter hook
		   $lightbox_script=apply_filters('pointelle_lightbox_inline',$lightbox_script);
		$html.=$lightbox_script;
	}	
	//action hook
	do_action('pointelle_global_script',$slider_handle,$pointelle_slider_curr);
	
	$slider_html=$slider_html.'</script> 
		<noscript><p><strong>'. $pointelle_slider['noscript'] .'</strong></p></noscript>
	<div id="'.$slider_handle.'" class="pointelle_slider pointelle_slider_'.$set.' pointelle_slider_set'.$set.'" '.$pointelle_slider_css['pointelle_slider'].'>
			'. $r_array[1].'
			'.$html_scroll_nav.'
	<div class="sldr_clearlt"></div><div class="sldr_clearrt"></div></div>';
	if($echo == '1')  {echo $slider_html; }
	else { return $slider_html; }
}

function pointelle_carousel_posts_on_slider($max_posts, $offset=0, $slider_id = '1',$out_echo = '1',$set='', $data=array() ) {
    global $pointelle_slider;
	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}
		
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.POINTELLE_SLIDER_TABLE;
	$post_table = $table_prefix."posts";
	$rand = $pointelle_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
	  $orderby = 'RAND()';
	}
	else {
	  $orderby = 'a.slide_order ASC, a.date DESC';
	}
	
	$posts = $wpdb->get_results("SELECT b.* FROM 
	                             $table_name a LEFT OUTER JOIN $post_table b 
								 ON a.post_id = b.ID 
								 WHERE (b.post_status = 'publish' OR (b.post_type='attachment' AND b.post_status = 'inherit')) AND a.slider_id = '$slider_id' 
	                             ORDER BY ".$orderby." LIMIT $offset, $max_posts", OBJECT);
	
	$r_array=pointelle_global_posts_processor( $posts, $pointelle_slider_curr, $out_echo,$set , $data );
	return $r_array;
}

function get_pointelle_slider($slider_id='',$set='',$offset=0, $data=array() ) {
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
	if(!empty($slider_id)){
		$slider_handle='pointelle_slider_'.$slider_id;
		$data['slider_handle']=$slider_handle;
		$r_array = pointelle_carousel_posts_on_slider($pointelle_slider_curr['no_posts'], $offset=0, $slider_id, '0', $set, $data); 
		get_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo='1',$data);
	} //end of not empty slider_id condition
}

//For displaying category specific posts in chronologically reverse order
function pointelle_carousel_posts_on_slider_category($max_posts='5', $catg_slug='', $offset=0, $out_echo = '1', $set='', $data=array() ) {
    global $pointelle_slider;
	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}

	global $wpdb, $table_prefix;
	
	if (!empty($catg_slug)) {
		$category = get_category_by_slug($catg_slug); 
		$slider_cat = $category->term_id;
	}
	else {
		$category = get_the_category();
		$slider_cat = $category[0]->cat_ID;
	}
	
	$rand = $pointelle_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
	  $orderby = '&orderby=rand';
	}
	else {
	  $orderby = '';
	}
	
	//extract the posts
	$posts = get_posts('numberposts='.$max_posts.'&offset='.$offset.'&category='.$slider_cat.$orderby);
	
	$r_array=pointelle_global_posts_processor( $posts, $pointelle_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_pointelle_slider_category($catg_slug='', $set='', $offset=0, $data=array() ) {
    global $pointelle_slider; 
 	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}
	$slider_handle='pointelle_slider_'.$catg_slug;
	$data['slider_handle']=$slider_handle;
    $r_array = pointelle_carousel_posts_on_slider_category($pointelle_slider_curr['no_posts'], $catg_slug, '0', '0', $set, $data); 
	get_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo='1',$data);
} 

//For displaying recent posts in chronologically reverse order
function pointelle_carousel_posts_on_slider_recent($max_posts='5', $offset=0, $out_echo = '1', $set='', $data=array() ) {
    global $pointelle_slider;
	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}
	//extract posts data
	$posts = get_posts('numberposts='.$max_posts.'&offset='.$offset);
	//randomize the slides
	$rand = $pointelle_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
	  shuffle($posts);
	}
	
	$r_array=pointelle_global_posts_processor( $posts, $pointelle_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_pointelle_slider_recent($set='', $offset=0, $data=array() ) {
	global $pointelle_slider; 
 	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}
	$slider_handle='pointelle_slider_recent';
	$r_array = pointelle_carousel_posts_on_slider_recent($pointelle_slider_curr['no_posts'], '0', '0', $set, $data);
	get_global_pointelle_slider($slider_handle,$r_array,$pointelle_slider_curr,$set,$echo='1',$data);
}
 
require_once (dirname (__FILE__) . '/shortcodes_1.php');
require_once (dirname (__FILE__) . '/widgets_1.php');

function pointelle_slider_enqueue_scripts() {
	wp_enqueue_script( 'jquery.cycle', pointelle_slider_plugin_url( 'js/jquery.cycle.js' ),
		array('jquery'), POINTELLE_SLIDER_VER, false);
}

add_action( 'init', 'pointelle_slider_enqueue_scripts' );

function pointelle_slider_enqueue_styles() {	
  global $post, $pointelle_slider, $wp_registered_widgets,$wp_widget_factory;
  if(is_singular()) {
	 $pointelle_slider_style = get_post_meta($post->ID,'_pointelle_slider_style',true);
	 if((is_active_widget(false, false, 'pointelle_sslider_wid', true) or isset($pointelle_slider['shortcode']) ) and (!isset($pointelle_slider_style) or empty($pointelle_slider_style))){
	   $pointelle_slider_style='default';
	 }
	 if (!isset($pointelle_slider_style) or empty($pointelle_slider_style) ) {
	     wp_enqueue_style( 'pointelle_slider_headcss', pointelle_slider_plugin_url( 'css/skins/'.$pointelle_slider['stylesheet'].'/style.css' ),
		false, POINTELLE_SLIDER_VER, 'all');
	 }
     else {
	     wp_enqueue_style( 'pointelle_slider_headcss', pointelle_slider_plugin_url( 'css/skins/'.$pointelle_slider_style.'/style.css' ),
		false, POINTELLE_SLIDER_VER, 'all');
	}
  }
  else {
     $pointelle_slider_style = $pointelle_slider['stylesheet'];
	wp_enqueue_style( 'pointelle_slider_headcss', pointelle_slider_plugin_url( 'css/skins/'.$pointelle_slider_style.'/style.css' ),
		false, POINTELLE_SLIDER_VER, 'all');
  }
}
add_action( 'wp', 'pointelle_slider_enqueue_styles' );

//admin settings
function pointelle_slider_admin_scripts() {
global $pointelle_slider;
  if ( is_admin() ){ // admin actions
  // Settings page only
	if ( isset($_GET['page']) && ('pointelle-slider-admin' == $_GET['page'] or 'pointelle-slider-settings' == $_GET['page'] )  ) {
	wp_register_script('jquery', false, false, false, false);
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'pointelle_slider_admin_js', pointelle_slider_plugin_url( 'js/admin.js' ),
		array('jquery'), POINTELLE_SLIDER_VER, false);
	wp_enqueue_style( 'pointelle_slider_admin_css', pointelle_slider_plugin_url( 'css/admin.css' ),
		false, POINTELLE_SLIDER_VER, 'all');
	wp_enqueue_script( 'jquery.cycle', pointelle_slider_plugin_url( 'js/jquery.cycle.js' ),
		array('jquery'), POINTELLE_SLIDER_VER, false);
	wp_enqueue_style( 'pointelle_slider_admin_head_css', pointelle_slider_plugin_url( 'css/skins/'.$pointelle_slider['stylesheet'].'/style.css' ),
		false, POINTELLE_SLIDER_VER, 'all');
	}
  }
}

add_action( 'admin_init', 'pointelle_slider_admin_scripts' );

function pointelle_slider_admin_head() {
global $pointelle_slider;
if ( is_admin() ){ // admin actions
   
  // Sliders page only
     if ( isset($_GET['page']) && 'pointelle-slider-admin' == $_GET['page'] ) {
	  $sliders = pointelle_ss_get_sliders(); 
	?>
		<script type="text/javascript">
            // <![CDATA[
        jQuery(document).ready(function() {
                jQuery(function() {
				 jQuery("#slider_tabs").tabs({fx: { opacity: "toggle", duration: 300}}).addClass( "ui-tabs-vertical-left ui-helper-clearfix" );jQuery( "#slider_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
				<?php foreach($sliders as $slider){?>
                    jQuery("#sslider_sortable_<?php echo $slider['slider_id'];?>").sortable();
                    jQuery("#sslider_sortable_<?php echo $slider['slider_id'];?>").disableSelection();
			    <?php } ?>
                });
        });
        function confirmRemove()
        {
            var agree=confirm("This will remove selected Posts/Pages from Slider.");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmRemoveAll()
        {
            var agree=confirm("Remove all Posts/Pages from Pointelle Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmSliderDelete()
        {
            var agree=confirm("Delete this Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function slider_checkform ( form )
        {
          if (form.new_slider_name.value == "") {
            alert( "Please enter the New Slider name." );
            form.new_slider_name.focus();
            return false ;
          }
          return true ;
        }
        </script>
<?php
   } //Sliders page only
      // Settings page only
  if ( isset($_GET['page']) && 'pointelle-slider-settings' == $_GET['page']  ) {
		wp_print_scripts( 'farbtastic' );
		wp_print_styles( 'farbtastic' );
?>
	<script type="text/javascript">
		// <![CDATA[
	jQuery(document).ready(function() {
			//for tabs
			jQuery("#slider_tabs").tabs({fx: { opacity: "toggle", duration: 300}}).addClass( "ui-tabs-vertical-left ui-helper-clearfix" );jQuery( "#slider_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
			//for colorpicker
			jQuery('#colorbox_1').farbtastic('#color_value_1');
			jQuery('#color_picker_1').click(function () {
			   if (jQuery('#colorbox_1').css('display') == "block") {
				  jQuery('#colorbox_1').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_1').fadeIn("slow"); }
			});
			var colorpick_1 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_1 == true) {
					return; }
					jQuery('#colorbox_1').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_1 = false;
			});
	//for second color box
			jQuery('#colorbox_2').farbtastic('#color_value_2');
			jQuery('#color_picker_2').click(function () {
			   if (jQuery('#colorbox_2').css('display') == "block") {
				  jQuery('#colorbox_2').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_2').fadeIn("slow"); }
			});
			var colorpick_2 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_2 == true) {
					return; }
					jQuery('#colorbox_2').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_2 = false;
			});
	//for third color box
			jQuery('#colorbox_3').farbtastic('#color_value_3');
			jQuery('#color_picker_3').click(function () {
			   if (jQuery('#colorbox_3').css('display') == "block") {
				  jQuery('#colorbox_3').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_3').fadeIn("slow"); }
			});
			var colorpick_3 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_3 == true) {
					return; }
					jQuery('#colorbox_3').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_3 = false;
			});
	//for fourth color box
			jQuery('#colorbox_4').farbtastic('#color_value_4');
			jQuery('#color_picker_4').click(function () {
			   if (jQuery('#colorbox_4').css('display') == "block") {
				  jQuery('#colorbox_4').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_4').fadeIn("slow"); }
			});
			var colorpick_4 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_4 == true) {
					return; }
					jQuery('#colorbox_4').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_4 = false;
			});
	//for fifth color box
			jQuery('#colorbox_5').farbtastic('#color_value_5');
			jQuery('#color_picker_5').click(function () {
			   if (jQuery('#colorbox_5').css('display') == "block") {
				  jQuery('#colorbox_5').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_5').fadeIn("slow"); }
			});
			var colorpick_5 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_5 == true) {
					return; }
					jQuery('#colorbox_5').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_5 = false;
			});
	//for sixth color box
			jQuery('#colorbox_6').farbtastic('#color_value_6');
			jQuery('#color_picker_6').click(function () {
			   if (jQuery('#colorbox_6').css('display') == "block") {
				  jQuery('#colorbox_6').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_6').fadeIn("slow"); }
			});
			var colorpick_6 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_6 == true) {
					return; }
					jQuery('#colorbox_6').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_6 = false;
			});
	//for seventh color box
			jQuery('#colorbox_7').farbtastic('#color_value_7');
			jQuery('#color_picker_7').click(function () {
			   if (jQuery('#colorbox_7').css('display') == "block") {
				  jQuery('#colorbox_7').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_7').fadeIn("slow"); }
			});
			var colorpick_7 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_7 == true) {
					return; }
					jQuery('#colorbox_7').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_7 = false;
			});
	//for eighth color box
			jQuery('#colorbox_8').farbtastic('#color_value_8');
			jQuery('#color_picker_8').click(function () {
			   if (jQuery('#colorbox_8').css('display') == "block") {
				  jQuery('#colorbox_8').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_8').fadeIn("slow"); }
			});
			var colorpick_8 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_8 == true) {
					return; }
					jQuery('#colorbox_8').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_8 = false;
			});
	//for ninth color box
			jQuery('#colorbox_9').farbtastic('#color_value_9');
			jQuery('#color_picker_9').click(function () {
			   if (jQuery('#colorbox_9').css('display') == "block") {
				  jQuery('#colorbox_9').fadeOut("slow"); }
			   else {
				  jQuery('#colorbox_9').fadeIn("slow"); }
			});
			var colorpick_9 = false;
			jQuery(document).mousedown(function(){
				if (colorpick_9 == true) {
					return; }
					jQuery('#colorbox_9').fadeOut("slow");
			});
			jQuery(document).mouseup(function(){
				colorpick_9 = false;
			});
	});
	function confirmSettingsCreate()
			{
				var agree=confirm("Create New Settings Set??");
				if (agree)
				return true ;
				else
				return false ;
	}
	function confirmSettingsDelete()
			{
				var agree=confirm("Delete this Settings Set??");
				if (agree)
				return true ;
				else
				return false ;
	}
	</script>
	<style type="text/css">
	.color-picker-wrap {
			position: absolute;
			display: none; 
			background: #fff;
			border: 3px solid #ccc;
			padding: 3px;
			z-index: 1000;
		}
	</style>
	<?php
   } //for pointelle slider option page  
 }//only for admin
}
add_action('admin_head', 'pointelle_slider_admin_head');

function pointelle_get_inline_css($set='',$echo='0'){
    global $pointelle_slider;
	$pointelle_slider_options='pointelle_slider_options'.$set;
    $pointelle_slider_curr=get_option($pointelle_slider_options);
	if(!isset($pointelle_slider_curr) or !is_array($pointelle_slider_curr) or empty($pointelle_slider_curr)){$pointelle_slider_curr=$pointelle_slider;$set='';}
	
	global $post;
	if(is_singular()) {	$pointelle_slider_style = get_post_meta($post->ID,'_pointelle_slider_style',true);}
	if((is_singular() and ($pointelle_slider_style == 'default' or empty($pointelle_slider_style) or !$pointelle_slider_style)) or (!is_singular() and $pointelle_slider['stylesheet'] == 'default')  )	{ $default=true;	}
	else{ $default=false;}
	
	$pointelle_slider_css=array();
	if($default){
		$style_start= ($echo=='0') ? 'style="':'';
		$style_end= ($echo=='0') ? '"':'';
		//pointelle_slider
		$width='';
		if(isset($pointelle_slider_curr['width']) and $pointelle_slider_curr['width']!=0) {
		    $width='width:'. $pointelle_slider_curr['width'].'px;';
		}		
		$pointelle_slider_css['pointelle_slider'] = $style_start.$width.'height:'. $pointelle_slider_curr['height'].'px;'.$style_end;

		//pointelle_slides	
		if( $pointelle_slider_curr['navpos']=='0' ) {$slidespos = 'right:0;left:inherit;' ;
			$pointelle_slider_css['pointelle_slides'] = $style_start.'width:'.$pointelle_slider_curr['img_width'].'px;'.$slidespos.$style_end;}
		else{
			$pointelle_slider_css['pointelle_slides'] = $style_start.'width:'.$pointelle_slider_curr['img_width'].'px;'.$style_end;
		}
		
		//pointelle_slider_control	
		if( $pointelle_slider_curr['navpos']=='0' ) $controlpos = 'left:0;right:inherit;' ;
		$pointelle_slider_css['pointelle_slider_control'] = $style_start.'width:'.$pointelle_slider_curr['nav_control_w'].'px; height: '. ( $pointelle_slider_curr['nav_control_h'] - 2 ).'px;border: 1px solid '.$pointelle_slider_curr['nav_brcolor'].'; '.$controlpos.$style_end;
		
		//pointelle_slider_nav_thumb
		$pointelle_slider_css['pointelle_slider_nav_thumb'] = $style_start.'border:'.$pointelle_slider_curr['nav_img_border'].'px solid '.$pointelle_slider_curr['nav_img_brcolor'].';width:'.$pointelle_slider_curr['nav_img_width'].'px;height:'.$pointelle_slider_curr['nav_img_height'].'px;'.$style_end;
		
		//pointelle_slider_nav
		$factor_h=0;
		if( $pointelle_slider_curr['nav_img_border'] > 0 )$factor_h=$pointelle_slider_curr['scroll_nav_posts'] - 2;
		if ($pointelle_slider_curr['nav_bg'] == '1') { $pointelle_slider_nav_bg = "transparent";} else { $pointelle_slider_nav_bg = $pointelle_slider_curr['nav_bg_color']; }
		$pointelle_slider_css['pointelle_slider_nav'] = $style_start.'height: '.( ( $pointelle_slider_curr['nav_control_h'] / $pointelle_slider_curr['scroll_nav_posts'] ) - $factor_h - 20 ).'px;background-color:'.$pointelle_slider_nav_bg.'; border-bottom:'.$pointelle_slider_curr['nav_img_border'].'px solid '.$pointelle_slider_curr['nav_brcolor'].$style_end;
		
		//pointelle_slider_nav_h2
		if ($pointelle_slider_curr['nav_title_fstyle'] == "bold" or $pointelle_slider_curr['nav_title_fstyle'] == "bold italic" ){$nav_title_font_weight = "bold";} else { $nav_title_font_weight = "normal"; }
		if ($pointelle_slider_curr['nav_title_fstyle'] == "italic" or $pointelle_slider_curr['nav_title_fstyle'] == "bold italic" ){$nav_title_font_style = "italic";} else {$nav_title_font_style = "normal";}
		if($pointelle_slider_curr['disable_thumbs'] != '1')	$nav_img_width =  ( $pointelle_slider_curr['nav_img_width'] + 55 );
		else $nav_img_width =  30;
		$pointelle_slider_css['pointelle_slider_nav_h2'] = $style_start.'width:'.($pointelle_slider_curr['nav_control_w'] - $nav_img_width ) .'px;font-family:'.$pointelle_slider_curr['nav_title_font'].', Arial, Helvetica, sans-serif; font-weight:'.$nav_title_font_weight.';font-style:'.$nav_title_font_style.'; font-size: '.$pointelle_slider_curr['nav_title_fsize'].'px; color: '.$pointelle_slider_curr['nav_title_fcolor'].';'.$style_end;
		
		//pointelle_meta
	    if ($pointelle_slider_curr['meta_title_fstyle'] == "bold" or $pointelle_slider_curr['meta_title_fstyle'] == "bold italic" ){$meta_title_font_weight = "bold";} else { $meta_title_font_weight = "normal"; }
		if ($pointelle_slider_curr['meta_title_fstyle'] == "italic" or $pointelle_slider_curr['meta_title_fstyle'] == "bold italic" ){$meta_title_font_style = "italic";} else {$meta_title_font_style = "normal";}
		$pointelle_slider_css['pointelle_meta'] = $style_start.'width:'.($pointelle_slider_curr['nav_control_w'] - $nav_img_width ) .'px;font-family:'.$pointelle_slider_curr['meta_title_font'].', Arial, Helvetica, sans-serif; font-weight:'.$meta_title_font_weight.';font-style:'.$meta_title_font_style.'; font-size: '.$pointelle_slider_curr['meta_title_fsize'].'px; color: '.$pointelle_slider_curr['meta_title_fcolor'].';border-top:1px solid '.$pointelle_slider_curr['nav_brcolor'].';'.$style_end;
		 
	//pointelle_slideri
	   	if ($pointelle_slider_curr['bg'] == '1') { $pointelle_slideri_bg = "transparent";} else { $pointelle_slideri_bg = $pointelle_slider_curr['bg_color']; }
		$pointelle_slider_css['pointelle_slideri']=$style_start.'background-color:'.$pointelle_slideri_bg.';border:'.$pointelle_slider_curr['border'].'px solid '.$pointelle_slider_curr['brcolor'].';height:'. $pointelle_slider_curr['height'].'px;'.$style_end;
		
		//pointelle_slider_h4
		if ($pointelle_slider_curr['ptitle_fstyle'] == "bold" or $pointelle_slider_curr['ptitle_fstyle'] == "bold italic" ){$ptitle_fweight = "bold";} else {$ptitle_fweight = "normal";}
		if ($pointelle_slider_curr['ptitle_fstyle'] == "italic" or $pointelle_slider_curr['ptitle_fstyle'] == "bold italic"){$ptitle_fstyle = "italic";} else {$ptitle_fstyle = "normal";}
		$pointelle_slider_css['pointelle_slider_h4']=$style_start.'clear:none;line-height:'. ($pointelle_slider_curr['ptitle_fsize'] + 3) .'px;font-family:'. $pointelle_slider_curr['ptitle_font'].', Arial, Helvetica, sans-serif;font-size:'.$pointelle_slider_curr['ptitle_fsize'].'px;font-weight:'.$ptitle_fweight.';font-style:'.$ptitle_fstyle.';color:'.$pointelle_slider_curr['ptitle_fcolor'].';margin:0px 0 5px 0;'.$style_end;
		
	//pointelle_slider_h4_a
		$pointelle_slider_css['pointelle_slider_h4_a']=$style_start.'color:'.$pointelle_slider_curr['ptitle_fcolor'].';'.$style_end;
	
	//pointelle_excerpt_p
		if ($pointelle_slider_curr['content_fstyle'] == "bold" or $pointelle_slider_curr['content_fstyle'] == "bold italic" ){$content_fweight= "bold";} else {$content_fweight= "normal";}
		if ($pointelle_slider_curr['content_fstyle']=="italic" or $pointelle_slider_curr['content_fstyle'] == "bold italic"){$content_fstyle= "italic";} else {$content_fstyle= "normal";}
		$pointelle_slider_css['pointelle_excerpt_p']=$style_start.'font-family:'.$pointelle_slider_curr['content_font'].', Arial, Helvetica, sans-serif;font-size:'.$pointelle_slider_curr['content_fsize'].'px;font-weight:'.$content_fweight.';font-style:'.$content_fstyle.';color:'. $pointelle_slider_curr['content_fcolor'].';'.$style_end;
		
	//pointelle_slider_thumbnail
		$pointelle_slider_css['pointelle_slider_thumbnail']=$style_start.'height:'.$pointelle_slider_curr['img_height'].'px;border:'.$pointelle_slider_curr['img_border'].'px solid '.$pointelle_slider_curr['img_brcolor'].';width:'.$pointelle_slider_curr['img_width'].'px;margin:0;padding:0;'.$style_end;
	
	//pointelle_slider_p_more
		$pointelle_slider_css['pointelle_slider_p_more']=$style_start.'color:'.$pointelle_slider_curr['ptitle_fcolor'].';font-family:'.$pointelle_slider_curr['content_font'].';font-size:'.$pointelle_slider_curr['content_fsize'].'px;'.$style_end;
	
	//pointelle_nav_arrows
		$navarrowpos='';
		if( $pointelle_slider_curr['navpos']=='0' ) $navarrowpos = 'left:0;right:inherit;' ;
		$pointelle_slider_css['pointelle_nav_arrows']=$style_start.$navarrowpos.$style_end;	
	}
	$pointelle_slider_css=apply_filters('pointelle_inline_css',$pointelle_slider_css,$pointelle_slider_curr,$default);
	return $pointelle_slider_css;
}
function pointelle_slider_css() {
global $pointelle_slider;
$css=$pointelle_slider['css'];
if($css and !empty($css)){
?>
 <style type="text/css"><?php echo $css;?></style>
<?php
}
}
add_action('wp_head', 'pointelle_slider_css');
add_action('admin_head', 'pointelle_slider_css');

function pointelle_custom_scripts(){
}
add_action('wp_head', 'pointelle_custom_scripts');
add_action('admin_head', 'pointelle_custom_scripts');
?>