<?php

function product_selector_get_post_count( $slug ) {
	if( empty( $slug ) )
		return;

	$args = array(
		'post_type' => 'ps-questions',
		'tax_query' => array(
			array(
				'taxonomy' => 'selector-category',
				'field'    => 'slug',
				'terms'    => $slug,
			),
		),
    'posts_per_page' => -1,
    'post_status ' 		=> 'publish',
	);

	// The Query
	$the_query = new WP_Query( $args );

	// The Loop
  if ( $the_query->have_posts() )
    $count = 0;
    foreach ( $the_query->posts as $post ){
      if($post->post_status == 'publish'){
        $count++;
      }
    }
    return $count;
		//return $the_query->post_count;

	return false;
}


/**
 * Create custom PHP string parser
 * http://php.net/manual/en/function.parse-str.php#76792
 */
function product_selector_proper_parse_str( $str ) {
  # result array
  $arr = array();

  # split on outer delimiter
  $pairs = explode('&', $str);

  # loop through each pair
  foreach ($pairs as $i) {
    # split into name and value
    list($name,$value) = explode('=', $i, 2);
    
    # if name already exists
    if( isset($arr[$name]) ) {
      # stick multiple values into an array
      if( is_array($arr[$name]) ) {
        $arr[$name][] = $value;
      }
      else {
        $arr[$name] = array($arr[$name], $value);
      }
    }
    # otherwise, simply stick it in a scalar
    else {
      $arr[$name] = $value;
    }
  }

  # return result array
  return $arr;
}