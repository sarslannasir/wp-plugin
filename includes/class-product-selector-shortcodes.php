<?php

/**
 * WP_Product_Selector_Shortcodes class.
 *
 * @class 		WP_Product_Selector_Shortcodes
 * @version		1.0
 * @author 		Ryan Sutana
 */
 
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
 
// Check if class already exist
if( ! class_exists('WP_Product_Selector_Shortcodes')) :
	
class WP_Product_Selector_Shortcodes {
	
	/**
	 * Init shortcodes
	 */
	public function __construct() {
		// Define shortcodes
		$shortcodes = array(
			'selector' 			=> __CLASS__ . '::selector',
			'selectors' 		=> __CLASS__ . '::selectors',
			'selectorbutton' 	=> __CLASS__ . '::selector_button',
		);
		
		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "product_selector_{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}
	

	public static function selector( $atts ) {
		extract( shortcode_atts( array(
			'slug' => '',
		), $atts ) );
		
		ob_start();
		
		$slug	= isset( $_GET['slug'] ) ? sanitize_text_field( $_GET['slug'] ) : $slug;
		?>
			<div class="selectors">
				<?php
					self::_selector_posts( $slug );
				?>
			</div>
		<?php
		
		return ob_get_clean();
	}


	public static function selectors( $atts ) {
		extract( shortcode_atts( array(
			'slug' => '',
		), $atts ) );
		
		ob_start();
		
		$term_id	= isset( $_GET['term-id'] ) ? sanitize_text_field( $_GET['term-id'] ) : '';
		?>
			<div class="selectors">
				<?php
					self::_selector_posts( $slug );
				?>
			</div>
		<?php
		
		return ob_get_clean();
	}


	/**
 	 * Selector button content
	 */
	public static function selector_button( $atts ) {
		$params = shortcode_atts( array(
			'hosting' 	=> '',
			'text' 		=> 'Click here to try our product selector',
		), $atts );
		
		ob_start();
		
		?>
			<p>
				<a href="#" id="open_product_selector"><?php echo esc_html( $params['text'] ); ?></a>
			</p>
		<?php
		
		return ob_get_clean();
	}


	/**
 	 * Selector content
	 */
	public function _selector_posts( $slug, $echo = true ) {

		ob_start();

		// Get term by name ''news'' in Tags taxonomy.
		$term_name = get_term_by( 'slug', $slug, 'selector-category' );
		?>
			<h3 class="selector-title"><?php echo esc_html( $term_name->name ); ?></h3>
			<div class="selector-description"><?php echo wpautop( esc_html( $term_name->description ) ); ?></div>
		<?php

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

			// Make our query blazing speed
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false
		);

		// The Query
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
			
			$padding 	= floor( ( 100 / ( $the_query->post_count ) ) ) - 8;
			$style 		= '';// 'style="padding-right: '. $padding .'%;"';
			
			?>
			
			<div class="questionnaire">
				<input type="hidden" name="slug" id="slug" value="<?php echo esc_attr( $slug ); ?>" />

				<ul class="questionnaire-indicator tab-title">
					<?php
						
						for( $i = 1; $i <= $the_query->post_count; $i++ ) {
							?>
								<li class="indicator-item" <?php echo $style; ?>>
									<a data-tab="tab-<?php echo $i; ?>" <?php echo $i == 1 ? 'class="active"' : ''; ?>>
										<span class="dot">
											<i></i>
										</span>
									</a>
								</li>
							<?php
						}
					?>
					<li class="indicator-item last" <?php echo $style; ?>>
						<a data-tab="tab-<?php echo esc_attr( $the_query->post_count + 1 ); ?>"><span class="dot"><i></i></span></a>
					</li>
				</ul>

				<ul class="questionnaire-content tab-content">
					<?php
						$i = 1;
						while ( $the_query->have_posts() ) { $the_query->the_post();

							$fields = unserialize( get_post_meta( get_the_ID(), 'fields', true ) );
							?>
								
								<li data-tab="tab-<?php echo $i; ?>" class="questionnaire-item <?php echo $i == 1 ? 'active' : ''; ?>">
									<?php the_title('<h4 class="questionnaire-title">', '</h4>'); ?>

									<?php
										if( $fields ) {
											foreach ( $fields['questions'] as $question_key => $questions ) {
												?>
													<span>
														<label><input type="radio" name="fields[<?php the_ID(); ?>][]" value="<?php echo get_the_ID(). ':'. $question_key; ?>" /><?php echo esc_html( $questions['answer'] ); ?></label>
													</span>
												<?php
											}
										}
										
										// Hide back button on first item
										if( 1 != $i ) {
											?>
											<div class="product-selector-bottom-links">
												<a href="javascript:void(0); return false;" class="questionnaire-item__btn">Back</a>
											</div>
											<?php
										}
									?>
								</li>
								
							<?php

							$i++;
						}
					?>

					<li data-tab="tab-<?php echo esc_attr( $the_query->post_count + 1 ); ?>" class="questionnaire-item last">
						<div class="selector-result">
							<p class="result-message">
								Based on your selections, these are the best options for you...
							</p>
							
							<div class="products"></div>
						</div>

						<a href="javascript:void(0); return false;" class="questionnaire-item__btn">Back</a>
					</li>
				</ul>

			</div>
			<?php

			/* Restore original Post Data */
			wp_reset_postdata();
		} else {
			?>
				Please check your <strong>selector</strong> parameter and see if it does exist.
			<?php
		}

		$content = ob_get_clean();

		if( $echo )
			echo $content;
		else
			return $content;
				
	}
	
}

return new WP_Product_Selector_Shortcodes();
	
endif;
// end if checking class WP_Product_Selector_Shortcodes() not exist