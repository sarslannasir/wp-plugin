<?php

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'product_selector_meta_boxes_setup' );
add_action( 'load-post-new.php', 'product_selector_meta_boxes_setup' );


/* Meta box setup function. */
function product_selector_meta_boxes_setup() {
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'wpie_add_post_meta_boxes' );
	
	/* Save post meta on the 'save_post' hook. */
	add_action( 'save_post', 'product_selector_save_meta_box_data', 10, 2 );
}


/* Create one or more meta boxes to be displayed on the post editor screen. */
function wpie_add_post_meta_boxes() {

	add_meta_box(
		'product_selector_question_settings',
		__( 'Question Settings', 'wp-product-selector' ),
		'product_selector_question_meta_box_callback',
		'ps-questions'
	);

	add_meta_box(
		'product_selector_product_settings',
		__( 'Product Settings', 'wp-product-selector' ),
		'product_selector_product_meta_box_callback',
		'ps-product'
	);

}


/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function product_selector_question_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'product_selector_question_save_nonce', '_cmb_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */

	/*
	?>
		<input type="text" name="fields[questions][0][answer]" /> <br/>
		<input type="text" name="fields[questions][0][product][0]" /> <br/>
		<input type="text" name="fields[questions][0][product][1]" /> <br/>
		
		<br/>
		
		<input type="text" name="fields[questions][1][answer]" /> <br/>
		<input type="text" name="fields[questions][1][product][0]" /> <br/>
		<input type="text" name="fields[questions][1][product][1]" /> <br/>
	<?php
	*/

	$fields = unserialize( get_post_meta( $post->ID, 'fields', true ) );
	/*echo '<pre>';
		print_r( $fields );
	echo '</pre>';*/


	$args = array(
		'numberposts' => -1,
		'post_type'   => 'product' //'ps-product'
	);
	
	$products = get_posts( $args );
	?>
	
	<div class="product-selector-wrap">

		<!-- Answer Item -->
		<div class="question-item">
			<p>
   				<label>Answers: </label>
   				<a href="#" class="add-question"><i class="dashicons dashicons-plus-alt"></i></a>
   			</p>
			<div class="question-child-clone">
		   		<p>
		   			<input type="text" name="" class="field_question" /> <a class="remove answer-remove--btn" href="#" onclick="jQuery(this).parents('.question-child').slideUp(function(){ jQuery(this).remove() }); return false"><i class="dashicons dashicons-dismiss"></i></a>
		   		</p>
		   		
				<!-- Product Item -->
				<div class="product-item ml20 mb20">
					
					<p>
		   				<label>Associated Products: </label>
		   				<a href="#" class="add-product"><i class="dashicons dashicons-plus-alt"></i></a>
		   			</p>
					<div class="product-child-clone">
			   			<select name="" class="field_product">
			   				<option value="">&mdash; Select Product &mdash;</option>
			   				<?php
			   					if( $products ) {
			   						foreach ( $products as $product ) :
			   							?>
			   								<option value="<?php echo esc_attr( $product->ID ); ?>"><?php echo esc_html( $product->post_title ); ?></option>
			   							<?php
			   						endforeach;
			   						wp_reset_postdata();
			   					}
			   				?>
			   			</select>

			   			<a class="remove product-remove--btn" href="#" onclick="jQuery(this).parent().slideUp(function(){ jQuery(this).remove() }); return false"><i class="dashicons dashicons-dismiss"></i></a>
					</div>

				</div>
			</div>

			<?php
				if( $fields ) {
					foreach ( $fields['questions'] as $question_key => $questions ) {

						?>
							<div class="question-child" data-field_index="<?php echo esc_attr( $question_key ); ?>">
								<p>
									<input type="text" name="fields[questions][<?php echo esc_attr( $question_key ); ?>][answer]" value="<?php echo esc_attr( $questions['answer'] ); ?>" class="field_question" /> <a class="remove answer-remove--btn" href="#" onclick="jQuery(this).parents('.question-child').slideUp(function(){ jQuery(this).remove() }); return false"><i class="dashicons dashicons-dismiss"></i></a>
								</p>

								<div class="product-item ml20 mb20">
									<p>
										<label>Associated Products: </label>
										<a href="#" class="add-product"><i class="dashicons dashicons-plus-alt"></i></a>
									</p>
									<div class="product-child-clone">
										
							   			<select name="" class="field_product">
							   				<option value="">&mdash; Select Product &mdash;</option>
							   				<?php
							   					if( $products ) {
							   						foreach ( $products as $product ) :
							   							?>
							   								<option value="<?php echo esc_attr( $product->ID ); ?>"><?php echo esc_html( $product->post_title ); ?></option>
							   							<?php
							   						endforeach;
							   						wp_reset_postdata();
							   					}
							   				?>
							   			</select>

							   			<a class="remove product-remove--btn" href="#" onclick="jQuery(this).parent().slideUp(function(){ jQuery(this).remove() }); return false"><i class="dashicons dashicons-dismiss"></i></a>
									</div>
								
									<?php
										if( is_array( $questions['product'] ) ) {
											foreach ( $questions['product'] as $key => $value ) {
												?>
													<div class="product-child">
											   			<select name="fields[questions][<?php echo esc_attr( $question_key ); ?>][product][<?php echo esc_attr( $key ); ?>]" class="field_product">
											   				<option value="">&mdash; Select Product &mdash;</option>
											   				<?php
											   					if( $products ) {
											   						foreach ( $products as $product ) :
											   							?>
											   								<option value="<?php echo esc_attr( $product->ID ); ?>" <?php selected( $value, $product->ID ); ?>><?php echo esc_html( $product->post_title ); ?></option>
											   							<?php
											   						endforeach;
											   						wp_reset_postdata();
											   					}
											   				?>
											   			</select>

											   			<a class="remove product-remove--btn" href="#" onclick="jQuery(this).parent().slideUp(function(){ jQuery(this).remove() }); return false"><i class="dashicons dashicons-dismiss"></i></a>
													</div>
												<?php
											}
										}
									?>
								</div>
							</div>
						<?php
					
					}
				}
			?>
		</div>

	</div>
		
	<?php
}


/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function product_selector_product_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'product_selector_question_save_nonce', '_cmb_meta_box_nonce' );


	$fields = unserialize( get_post_meta( $post->ID, 'fields', true ) );
	?>
	
	<div class="product-selector-product-wrap">

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="product_url">Product URL</label>
				</th>
				<td>
					<input type="text" name="fields[product_url]" id="product_url" class="regular-text" value="<?php echo esc_url( $fields['product_url'] ); ?>" />
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="nofollow">Nofollow</label>
				</th>
				<td>
					<input type="checkbox" name="fields[nofollow]" id="nofollow" class="regular-text" value="1" <?php checked( $fields['nofollow'], 1 ); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="target_blank">Open in new window</label>
				</th>
				<td>
					<input type="checkbox" name="fields[new_window]" id="target_blank" class="regular-text" value="1" <?php checked( $fields['new_window'], 1 ); ?> />
				</td>
			</tr>
		</table>

	</div>

	<?php

}



/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function product_selector_save_meta_box_data( $post_id, $post ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['_cmb_meta_box_nonce'] ) )
		return;

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['_cmb_meta_box_nonce'], 'product_selector_question_save_nonce' ) )
		return;

	 // Check if not an autosave.
    if ( wp_is_post_autosave( $post_id ) )
        return;

    // If this is just a revision, don't send the email.
	if ( wp_is_post_revision( $post_id ) )
		return;

	// Check if user has permissions to save data.
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;

    // Check if we're on slider post-type
    $post_type = get_post_type( $post_id );


    // If this isn't a 'ps-questions' and 'ps-product' post, don't update it.
    if ( "ps-questions" == $post_type || "ps-product" == $post_type ) :

		/* OK, it's safe for us to save the data now. */

		// Sanitize user input.
		$fields = stripslashes_deep( $_POST['fields'] );
		
		// Update the meta field in the database.
		if( is_array( $fields ) )
			$fields = serialize( $fields );
		else
			$fields = $fields;

		update_post_meta( $post_id, 'fields', $fields );
		
	endif;
}


add_action( 'wp_footer', 'product_selector_dialog_box' );
/**
 * Add dialog box to footer area
 */
function product_selector_dialog_box() {
	ob_start();
	global $post;

	$ps_shortcode 	= new WP_Product_Selector_Shortcodes();
	$pattern 		= get_shortcode_regex();

	if( has_shortcode( $post->post_content, 'selectorbutton') ) {
		?>
		<div class="product-selector-fade">
			<div class="product-selector-modal animated">
				
				<div class="container">
					<div class="row">
						<div class="col-md-12">

							<a href="#" id="close_product_selector">Close</a>

							<?php
								if( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
									&& array_key_exists( 2, $matches ) ) :

									$converted_str  = str_replace(" ", "&", trim( $matches[3][0] ) );
									$params 		= product_selector_proper_parse_str( $converted_str );
									$slug 			= substr( $params['selector'], 1, -1 );

									$has_selector = $ps_shortcode->_selector_posts( $slug, false );
									if( $has_selector )
										echo $has_selector;

								endif;
							?>


						</div>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

	echo ob_get_clean();
}



add_action('admin_menu', 'product_selector_instruction_submenu_page', 100);
/**
 * Add new instruction page menu
 */ 
function product_selector_instruction_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=ps-questions',
        __( 'Instructions', 'wp-product-selector' ),
        __( 'Instructions', 'wp-product-selector' ),
        'manage_options',
        'product-selector-instruction-submenu-page',
        'product_selector_instruction_submenu_page_callback' );
}
 
function product_selector_instruction_submenu_page_callback() {

	ob_start();
	?>
    <div class="wrap"><div id="icon-tools" class="icon32"></div>
        <h2><?php echo __( 'WP Product Selector Demo and How-to Guide', 'wp-product-selector' ); ?></h2>

        <div class="mt40"><iframe width="560" height="315" src="https://www.youtube.com/embed/OAz0Bg0otsw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
   	</div>
   	<?php

   	echo ob_get_clean();
}


add_action( 'do_meta_boxes', 'product_selector_wpdocs_remove_plugin_metaboxes', 10, 3 );
 
/**
 * Remove Editorial Flow meta box for users that cannot delete pages 
 */
function product_selector_wpdocs_remove_plugin_metaboxes( $post_type, $context, $post ){
	global $wp_meta_boxes;

	/*echo '<pre>';
		print_r( $wp_meta_boxes );
	echo '</pre>';*/

	// If this isn't a 'ps-questions' and 'ps-product' post, remove Yoast metabox
    if ( "ps-questions" == $post_type || "ps-product" == $post_type ) :
    	remove_meta_box( 'wpseo_meta', $post_type, $context ); // Remove Edit Flow Editorial Metadata
    endif;
}