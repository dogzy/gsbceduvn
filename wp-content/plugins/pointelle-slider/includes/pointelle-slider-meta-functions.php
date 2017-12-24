<?php
function pointelle_get_slide_author($post_obj,$args){
 	$defaults = array(
		'field' => 'display_name',
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

    $author_id=$post_obj->post_author;
    return get_the_author_meta( $field , $author_id );
}
function pointelle_get_slide_category_name($post_obj,$args){
 	$defaults = array(
		'show' => 'first',
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

    $post_id=$post_obj->ID;
	$categories= get_the_category( $post_id );
    $cat_arr=array();
	foreach($categories as $category){
	  $cat_arr[]=$category->cat_name;
	}
	if($show=='all'){
       $category_string = implode(', ',$cat_arr);
	}
	else{
	   $category_string = $cat_arr[0];
	}
    return $category_string;
}
function pointelle_get_slide_pub_date($post_obj,$args){
	$defaults = array(
		'format' => 'M j,Y',
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	$pubdate=$post_obj->post_date ;
	$pubdate=strtotime($pubdate);
	return date( $format , $pubdate );
}
function pointelle_get_slide_comments_number($post_obj,$args){
 	$defaults = array(
		'zero' => false,
		'one' => false, 
		'more' => false,
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

    $post_id=$post_obj->ID;

	$number = get_comments_number( $post_id );
	
	if ( $number > 1 )
			$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments') : $more);
	elseif ( $number == 0 )
			$output = ( false === $zero ) ? __('No Comments') : $zero;
	else // must be one
			$output = ( false === $one ) ? __('1 Comment') : $one;
	
	return $output;
}
?>