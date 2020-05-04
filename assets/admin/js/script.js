/**
 * Product selector Javascript
 * @author: Ryan Sutana
 *          contact@sutanaryan.com
 *          http://www.sutanaryan.com/
 */


jQuery(document).ready(function($){

	// jQuery-Plugin "relCopy"
	// http://www.andresvidal.com/labs/relcopy.html#source
	
	// var toggle_button				= '<a class="toggle" href="#" onclick="jQuery(this).next().toggle(); return false"><i class="dashicons dashicons-sort"></i></a>',
	// question_child			= $('.question-child').html(),
	// question_child_child		= '<div class="question-child"> <div><label>Answers </label>'+ remove_button + question_child +'</div></div>';
	// product_child			= $('.product-child').html(),
	// product_child_child		= '<div class="product-child">'+ product_child + remove_button + '</div>';
	

	// Append product item
	$(this).on('click', '.add-question', function(e){
		var data_field_index	= Math.floor((Math.random() * 9999999999)),
			old_field_index		= data_field_index;

		// Make sure we don't have duplicate name
		if( old_field_index == data_field_index )
			data_field_index = data_field_index + 1;

		var question_child			= $(this).closest('.question-item').children('.question-child-clone').clone();
			
			question_child.find(".field_question").attr('name', 'fields[questions]['+ data_field_index +'][answer]');
			question_child.attr({'class':'question-child', 'data-field_index': data_field_index});

		$(this).closest(".question-item").append( question_child );		
		
		e.preventDefault();
	});
	
	// Append product item
	$(this).on('click', '.add-product', function(e){
		var data_field_index	= Math.floor((Math.random() * 9999999999)),
			old_field_index		= data_field_index;

		// Make sure we don't have duplicate name
		if( old_field_index == data_field_index )
			data_field_index = data_field_index + 1;

		var count_question_child 		= $(this).closest('.question-child').attr('data-field_index'), // ('.question-item').children
			count_product_child 		= $(this).closest('.product-item').children('.product-child').length,

			product_child				= $(this).closest('.product-item').children('.product-child-clone').clone();
			product_child.find(".field_product").attr('name', 'fields[questions]['+ count_question_child +'][product]['+ data_field_index +']');
			product_child.attr({'class':'product-child'});

		$(this).closest(".product-item").append( product_child );

		e.preventDefault();
	});

});