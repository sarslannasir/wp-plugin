<?php
function product_selector_theme_option_page() {
    ob_start();
    ?>
        <div class="wrap">
            <h1><?php echo __( 'Settings', 'wp-product-selector' ) ?></h1>
            <form method="post" action="options.php">
                <?php
                // display settings field on theme-option page
                settings_fields("product-selector-theme-options");
                // display all sections for theme-options page
                do_settings_sections("theme-options");
                submit_button();
                ?>
            </form>
        </div>
    <?php 
    echo ob_get_clean(); 
}

function product_selector_add_theme_menu_item() {
    add_submenu_page(
        'edit.php?post_type=ps-questions', 
        __( 'Settings', 'wp-product-selector' ),
        __( 'Settings', 'wp-product-selector' ),
        'manage_options', 
        'product-selector-settings-page', 
        'product_selector_theme_option_page');
}
add_action("admin_menu", "product_selector_add_theme_menu_item");


function product_selector_recommendations(){
    ?>
    <input type="number" name="product_selector_number_of_recommendations" id="product_selector_number_of_recommendations" value="<?php if(get_option('product_selector_number_of_recommendations')) { echo esc_attr( get_option('product_selector_number_of_recommendations') ); }else{ echo "3"; } ?>" />
    <?php
}

function product_selector_colorpicker(){
	wp_enqueue_style( 'wp-color-picker');
    wp_enqueue_script( 'wp-color-picker');
    
    wp_enqueue_script( 'product-selector-color', PLUGIN_SELECTOR_JS_DIR . '/color-settings.js', array( 'jquery' ) );
}
add_action('admin_enqueue_scripts', 'product_selector_colorpicker');

function product_selector_color_settings_1(){
    ?>
    <input type="text" name="product_selector_color_1" id="product_selector_color_1" value="<?php if(get_option('product_selector_color_1')) { echo esc_attr( get_option('product_selector_color_1') ); }else{ echo "#1d5ba4"; } ?>" />
    <?php
}

function product_selector_color_settings_2(){
    ?>
    <input type="text" name="product_selector_color_2" id="product_selector_color_2" value="<?php if(get_option('product_selector_color_2')) { echo esc_attr( get_option('product_selector_color_2') ); }else{ echo "#3e90f1"; } ?>" />
    <?php
}

function product_selector_color_settings_3(){
    ?>
    <input type="text" name="product_selector_color_3" id="product_selector_color_3" value="<?php if(get_option('product_selector_color_3')) { echo esc_attr( get_option('product_selector_color_3') ); }else{ echo "#24a250"; } ?>" />
    <?php
}

function product_selector_test_theme_settings(){
    add_option('first_field_option',1);// add theme option to database
    add_settings_section( 'first_section', '', '','theme-options');
    register_setting( 'product-selector-theme-options', 'first_field_option');

    //
    add_settings_field('number_recommendations', 'Number of recommendations', 'product_selector_recommendations', 'theme-options', 'first_section');
    register_setting( 'product-selector-theme-options', 'product_selector_number_of_recommendations');

    add_settings_field('color_1', 'Progress Bar Indicator Color', 'product_selector_color_settings_1', 'theme-options', 'first_section');
    register_setting( 'product-selector-theme-options', 'product_selector_color_1');

    // add_settings_field('color_2', 'Progress Bar Indicator Color 2', 'product_selector_color_settings_2', 'theme-options', 'first_section');
    // register_setting( 'product-selector-theme-options', 'product_selector_color_2');

    add_settings_field('color_3', 'Button Color', 'product_selector_color_settings_3', 'theme-options', 'first_section');
    register_setting( 'product-selector-theme-options', 'product_selector_color_3');
}
add_action('admin_init','product_selector_test_theme_settings');





function product_selector_apply_color() {

    if( get_option('product_selector_color_1') ){
      $color_1  =   esc_html( get_option('product_selector_color_1') );
    }else{
      $color_1  =  '#1d5ba4';
    }

    if( get_option('product_selector_color_3') ){
      $color_3  =   esc_html( get_option('product_selector_color_3') );
    }else{
      $color_3  =  '#24a250';
    }

    $custom_css = "
    .questionnaire-indicator:after,
    .questionnaire-indicator .dot,
    .questionnaire .tab-title a.active > .dot,
    .dot i,
    .questionnaire-indicator li:before,
    .questionnaire-indicator li:after{
        background-color: {$color_1};
    }
    .questionnaire-indicator .dot{
        border: 10px solid {$color_1};
    }
    .questionnaire .tab-content span label,
    .product-link{
        background-color: {$color_3};
    }
    ";
    
	wp_enqueue_style( 'product-selector-style', PLUGIN_SELECTOR_CSS_DIR . '/style.css', false, '1.0', 'all' );
    wp_add_inline_style( 'product-selector-style', $custom_css );
}

add_action( 'wp_enqueue_scripts', 'product_selector_apply_color', 999 );