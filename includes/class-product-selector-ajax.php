<?php

add_action( 'wp_ajax_nopriv_selector', 'product_selector_callback' );
add_action( 'wp_ajax_selector', 'product_selector_callback' );

function product_selector_callback() {

	$error = '';
	
	/*
	 * This will holds the number of product selected
	 * array(
	 *	'product1_ID' => 2,
	 *	'product2_ID' => 1,
	 * )
	 *
	 */
	$products 	= array();

	$answers 	= sanitize_post( $_POST['fields'] );
	$slug 		= sanitize_text_field( $_POST['slug'] );
	
	$answer 	= explode( ',', $answers );
	$post_count = product_selector_get_post_count( $slug );

	// Check if user completed all the step(s)
	// If not warn them and complete the steps first
	//if( count( $answer ) == $post_count ) {
		foreach( $answer as $selected_answer ) {
			// Extract and get "question" ID and selected "answer" key
			$questions 		= explode( ':', $selected_answer );

			$question_id 	= $questions[0];
			$answer_key 	= $questions[1];

			// Get custom meta 'fields' values
			$fields 	= unserialize( get_post_meta( $question_id, 'fields', true ) );
			
			// Get products on selected answer
			$product 	= $fields['questions'][$answer_key]['product'];
			if( $product ) {
				foreach ( $product as $product_key ) {
					
					// Count how many occurrence of the product
					if( array_key_exists( $product_key, $products ) ) {
						$old_value = $products[$product_key];
						
						// Add new value to the existing product key
						$products[$product_key] = $old_value + 1;
					} else {
						// Add new key to the products memory and a value of 1
						$products[$product_key] = 1;
					}

				}
			}
		}

		// Sort products and display the most selected products
		arsort( $products );

		$i = 0;
		foreach ( $products as $key => $value ) {

			// Display product and percentage value
			$args = array(
				'post_type'   		=> 'product', //'ps-product'
				'p'	  				=> $key,
				'posts_per_page' 	=> 1,

				// Make our query blazing speed by disabling unnecessary query
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'cache_results'          => false
			);
			
			$the_query = new WP_Query( $args );

			// The Loop
			if ( $the_query->have_posts() ) {
				
				while ( $the_query->have_posts() ) { $the_query->the_post();

					$fields 		= unserialize( get_post_meta( get_the_ID(), 'fields', true ) );
					$product_url 	= $fields['product_url'] ? $fields['product_url'] : get_permalink();
					$target 		= $fields['new_window'] ? 'target="_blank"' : '';
					$nofollow 		= $fields['nofollow'] ? 'rel="nofollow"' : '';
					?>
						<div class="product-item">
							<div class="product-item-percentage">
								<?php
									$percentage_total 	= $products[get_the_ID()] * 100;
									$product_percentage = $percentage_total / $post_count;
								?>
								<span><?php echo esc_html( ceil( $product_percentage ) ); ?>% match</span>
							</div>

							<?php the_post_thumbnail( 'medium' ); ?>

							<h3 class="product-title">
								<a href="<?php echo esc_url( $product_url ); ?>" <?php echo $target . ' ' . $nofollow; ?>><?php echo esc_html( get_the_title() ); ?></a>
							</h3>
							<div class="product-excerpt">
								
								<p>
									<a href="<?php echo esc_url( $product_url ); ?>" class="product-link" <?php echo $target . ' ' . $nofollow; ?>>View Details</a>
								</p>
							</div>
						</div>
					<?php
				}
				
				// Restore original Post Data
				wp_reset_postdata();
			} else {
				// no posts found
			}

			if(get_option('product_selector_number_of_recommendations')){
				$recommendations = esc_html( get_option('product_selector_number_of_recommendations') );
			}else{
				$recommendations = 3;
			}

			if (++$i == $recommendations) break;

		} // End foreach
	//}

	if( $error )
		echo '<p class="error">'. $error .'</p>';

	// return proper result
	die();

}