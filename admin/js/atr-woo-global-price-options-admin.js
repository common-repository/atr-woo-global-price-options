(function ($) {
	'use strict';
	$(document).ready(function () {
		// Before we change the html structure we add identifier to the original settings tables in order to keep the tabs working.
		// Also changed the class-atr-woo-global-price-options-settings.php js accordingly
		$('table.form-table:eq( 0 )').addClass('main-section-wrap section-tab-content');
		$('table.form-table:gt( 0 )').addClass('other-section-wrap section-tab-content');
		// Tab 1 (Main) Remove default table elements wrapping the fields
		$('.gpo-settings-fields-wrap table.form-table.main-section-wrap tr').wrap('<div class="tr-content"></div>').contents().unwrap();
		$( ".gpo-settings-fields-wrap table.form-table.main-section-wrap th:contains('Option title')" ).wrapInner("<div class='th-content gpo-settings-th-title-label'></div>");
		$( ".gpo-settings-fields-wrap table.form-table.main-section-wrap th:contains('Option price')" ).wrapInner("<div class='th-content gpo-settings-th-price-label'></div>");
		$( ".gpo-settings-fields-wrap table.form-table.main-section-wrap th:contains('Price list display')" ).parents('.tr-content').addClass('list-control-label-wrapper');
		$( ".gpo-settings-fields-wrap table.form-table.main-section-wrap th:contains('Price list display')" ).wrapInner("<div class='th-content gpo-settings-th-list-control-label'></div>");

		
		$('.th-content').unwrap();
		
		
		
		//$('.gpo-settings-fields-wrap table.form-table td').wrapInner("<div class='td-content'></div>");
		$('.gpo-settings-fields-wrap table.form-table.main-section-wrap td').find( '.gpo-settings-title' ).parents('td').wrapInner("<div class='td-content td-content-ttl'></div>");
		$('.gpo-settings-fields-wrap table.form-table.main-section-wrap td').find( '.gpo-settings-price' ).parents('td').wrapInner("<div class='td-content td-content-price'></div>");
		
		
		$('.td-content').unwrap();
		$('.gpo-settings-fields-wrap table.form-table.main-section-wrap').children().wrapAll("<div class='gpo-settings-option-inner-wrap section-tab-content' />");
		$('.gpo-settings-fields-wrap table.form-table.main-section-wrap tbody').children().unwrap();
		$('.gpo-settings-fields-wrap table.form-table.main-section-wrap').children().unwrap();

		$('#gpo-settings-option-count-p_0_fields_count').parents('.tr-content').addClass('p-0-fields-count-wrap');

		$('div[id^="gpo-settings-title-p_0_ttl_"]').parents('.tr-content').addClass('p-0-fields-ttl-wrap gpo-settings-price-option-row-half');
		$('div[id^="gpo-settings-price-p_0_price_"]').parents('.tr-content').addClass('p-0-fields-price-wrap gpo-settings-price-option-row-half');

		//Odd even BG for rows
		var $els = $('.gpo-settings-price-option-row-half');
		for (var i = 0; i < $els.length; i += 2) {
			$els.slice(i, i + 2).wrapAll('<div class="gpo-settings-price-option-row ' + (i > 0 && i % 2 == 0 && i % 4 != 0 ? 'offsetRow' : '') + '"></div>')
		}

		$(".gpo-settings-option-inner-wrap").append('<a href="javascript:void(0);" class="add_button" title="Add price option"><img alt="Add price option" src="/wp-content/plugins/atr-woo-global-price-options/public/css/add_option.png"/><span>Add price option</span></a>');
		$('#gpo-settings-price-p_0_price_1 .remove_button').css('display','none');
		var maxField = 10; //Input fields increment limitation
		var wrapper = $('.gpo-settings-option-inner-wrap');

		$('.add_button').click(function () { //Once add button is clickeadd_buttond
			$('#gpo-settings-price-p_0_price_1 .remove_button').css('display','inline-block');
			$( '.gpo-settings-price-option-row:last' ).clone().find("input:text").val("").end().insertBefore( $(this) );// Add field html

			rearrange_inputs_ids(wrapper);

		});
		$(wrapper).on('click', '.remove_button', function (e) { //Once remove button is clicked
			e.preventDefault();
			$(this).parents('div.gpo-settings-price-option-row').remove(); //Remove field html
			rearrange_inputs_ids(wrapper);

		});
		
		/* Categories treeview */
		jQuery(function($){
			var $ul = $('ul.gpo-cat-list');
			var $ul_li = $('ul.gpo-cat-list li');
			$ul.find('li[parent-id]').each(function () {
				$ul.find('li[parent-id=' + $(this).attr('li-id') + ']').wrapAll('<ul class="gpo-cat-list-child-ul" />').parent().appendTo(this)
			});
			$ul_li.each(function () {
				if($(this).children("ul").length) {
				   $( $(this) ).prepend( '<span class="gpo-cat-list-child-sign">+</span>' );
				}
				else{
					$( $(this) ).prepend( '<span class="gpo-cat-list-child-empty"></span>' );
				}
			});
			$('.gpo-cat-list-child-sign').click(function() {
				var curr_ul = $(this).parent().children('ul');
				$(curr_ul).toggle('slow', function() {
					$(this).toggleClass('expanded');
				});
				$(this).text(function(i, text){
				  return text === '+' ? '-' : '+';
				})			  
			});	
			
			$('.atr-expand-all-cats').click(function() {
				var all_ul = $('ul.gpo-cat-list li ul');
				var all_sign = $('ul.gpo-cat-list span.gpo-cat-list-child-sign');
				$(all_ul).show(500);	
				$(all_sign).html('-');					
			});
			$('.atr-close_all-cats').click(function() {
				var all_ul = $('ul.gpo-cat-list li ul');
				var all_sign = $('ul.gpo-cat-list span.gpo-cat-list-child-sign');
				$(all_ul).hide(500);	
				$(all_sign).html('+');				
			});	
			
			/* Check/Uncheck all categories */
			$('.atr-cats-select-actions.atr-check-all-cats').click(function() {
				$('input.categories-select-chkbox').prop('checked', true);
			});

			$('.atr-cats-select-actions.atr-uncheck-cats').click(function() {
				$('input.categories-select-chkbox').prop('checked', false);
			});				
		})


	
	});
	
	/* Categories serch */
	$(function(){ // this will be called when the DOM is ready
	  $('#atrCatSearchInput').keyup(function() {
		var value = $(this).val().toLowerCase();
		$("ul.gpo-cat-list li").filter(function() {
		  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
				var all_ul = $('ul.gpo-cat-list li ul');
				var all_sign = $('ul.gpo-cat-list span.gpo-cat-list-child-sign');
				$(all_ul).show(500);	
				$(all_sign).html('-');			  
		});
	  });
	});	
	
	/* Rearrange the price options IDs after change */
	function rearrange_inputs_ids(wrpper_element) {
		var $price_option_row = $(wrpper_element).children('.gpo-settings-price-option-row');
		var n = $( $price_option_row ).length;
		$.each($price_option_row, function (i, val) {			
			var row_mumber = i + 1;			
				$('.th-content.gpo-settings-th-title-label .gpo-option-title-index', this).text( row_mumber.toString());
				$('.th-content.gpo-settings-th-price-label .gpo-option-price-index', this).text( row_mumber.toString());			
				$('.td-content.td-content-ttl .gpo-settings-title', this).attr('id','gpo-settings-title-p_0_ttl_' + row_mumber.toString());
				$('.td-content.td-content-price .gpo-settings-price', this).attr('id','gpo-settings-price-p_0_price_' + row_mumber.toString());
				$('.td-content .gpo-settings-title input', this).attr('id','p_0_ttl_' + row_mumber.toString());
				$('.td-content input', this).attr('name','atr-woo-global-price-options[p_0_ttl_' + row_mumber.toString() + ']');
				$('.td-content.td-content-ttl label', this).attr('for','p_0_ttl_' + row_mumber.toString());				
				$('.td-content.td-content-price .gpo-settings-price input', this).attr('id','p_0_price_' + row_mumber.toString());
				$('.td-content.td-content-price .gpo-settings-price input', this).attr('name','atr-woo-global-price-options[p_0_price_' + row_mumber.toString() + ']');
				$('.td-content.td-content-price label', this).attr('for','p_0_price_' + row_mumber.toString());	
		});
		if ( n === 1 ){
			$('#gpo-settings-price-p_0_price_1 .remove_button').css('display','none');
		}

	}

})(jQuery);