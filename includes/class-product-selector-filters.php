<?php

add_filter('manage_edit-selector-category_columns', 'product_selector_add_question_place_columns');
/**
 * Add new taxonomy custom column
 */
function product_selector_add_question_place_columns( $columns ) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'header_icon' => '',
        
        'slug' => __('Slug'),
        'posts' => __('Questions'),
        'shortcode' => __('<span style="padding: 10px 0;display: block;">Shortcodes</span>')
        );
    //$columns['shortcode'] = 'Shortcodes';
    return $columns;
}


add_filter( 'manage_selector-category_custom_column', 'product_selector_add_question_place_column_content', 10, 3 );
/**
 * Include shortcode to term row
 */
function product_selector_add_question_place_column_content( $content, $column_name, $term_id ) {
    $term = get_term( $term_id, 'selector-category');
    
    switch ( $column_name) {
        case 'shortcode':
            //do your stuff here with $term or $term_id
            $content = '<p><strong>Embedded Selector</strong><br><code>[selectors slug="'. $term->slug .'"]</code></p>
            			<p><strong>Link Activated Selector</strong><br><code>[selectorbutton text="Click here to try our product selector" selector="'. $term->slug .'"]</code></p>';
        break;
        default:
            break;
    }
    return $content;
}