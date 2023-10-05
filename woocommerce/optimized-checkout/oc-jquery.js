(function( $ ) {
	'use strict';

	// add placeholder text for woo fields
	$("#reg_email").attr("placeholder", "Email address");
	$("#user_login").attr("placeholder", "Username or email address");
	$("#username").attr("placeholder", "Username or email address");
	$("#password ").attr("placeholder", "Password");

	// edit account details form
	$("#account_first_name").attr("placeholder", "First name");
	$("#account_last_name ").attr("placeholder", "Last name");
	$("#account_display_name ").attr("placeholder", "Display name");
	$("#account_email").attr("placeholder", "Email address");
	$("#password_current").attr("placeholder", "Current password (leave blank to leave unchanged)");
	$("#password_1").attr("placeholder", "New password (leave blank to leave unchanged)");
	$("#password_2").attr("placeholder", "Confirm new password");

	$('a[href=#show-account]').click(function(e){
		e.preventDefault();

		$(document).find('.woocommerce-form-login.login').addClass('active');
	});

	$('#close-login-popup').click(function(e){
		e.preventDefault();

		$(document).find('.woocommerce-form-login.login').removeClass('active');
	});

	const setActive = (item, active) => {
		const itemParent = item.closest('.form-row');
		
		if (active) {
			itemParent.addClass('form-field--is-active')
		} else {
			itemParent.removeClass('form-field--is-active')
			item.val() === '' ? 
			  itemParent.removeClass('form-field--is-filled') : 
			  itemParent.addClass('form-field--is-filled')
		}
	}

	var items = $(document).find('.form-row input.input-text');
	
	$.each(items, function (index, item) {

		if($(this).val() != ''){
			setActive( $(this), false);
		}
	  	$(this).focus( () => setActive( $(this), true) );
	  	$(this).blur( () => setActive( $(this), false) );
	  	$(this).change( () => setActive( $(this), false) );
	  	$(this).keyup( () => setActive( $(this), false) );
	});

	$('.logged-in-user .saved-info a').click(function(e){
		e.preventDefault();
		$('body').removeClass('show-step-0');
		$('.step-0').hide();
	});

	function activeStep(targetStep, targetContent){
		$('.step-content').hide();

		$('.owc-checkout-steps-wrapper .owc-checkout-step a').removeClass('active');
		$('.owc-checkout-steps-wrapper .owc-checkout-step a'+targetStep).addClass('active');

		$(targetContent).fadeIn();
	}

	$('.owc-information').click(function(e){
		activeStep('.owc-information', '.step-1');
	});
	
	$('.owc-shipping').click(function(e){
		var error = false;
		[].forEach.call(
          document.querySelectorAll('.step-1.step-content .form-row.validate-required'),
          (el) => {

              let input = $(el).find('input');
              if(input.val() == ''){
                $(el).addClass('woocommerce-invalid woocommerce-invalid-required-field');
                input.addClass('woocommerce-invalid woocommerce-invalid-required-field');
                error = true;
              }
            }
          );

          if(error){
            return;
          }


		let contact_email 		= $('#billing_email').val();
		let shipping_company 	= $('#shipping_company').val();
		let shipping_address_1 	= $('#shipping_address_1').val();
		let shipping_address_2 	= $('#shipping_address_2').val();
		let shipping_city 		= $('#shipping_city').val();
		let shipping_state 		= $('#shipping_state').val();
		let shipping_postcode 	= $('#shipping_postcode').val();
		let ship_to_address 	= '';

		if( shipping_company != '' ){
			ship_to_address = shipping_company+ ', ';
		}

		ship_to_address = ship_to_address + shipping_address_1;

		if( shipping_address_2 != '' ){
			ship_to_address = ship_to_address + ', ' + shipping_address_2;
		}

		ship_to_address = ship_to_address + ', ' + shipping_city;
		
		if( shipping_state != '' ){
			ship_to_address = ship_to_address + ', ' + shipping_state;
		}

		ship_to_address = ship_to_address + ', ' + shipping_postcode;
		
		// add information to info-review box
		$('.information-review-box .contact-email').text( contact_email );
		$('.information-review-box .ship-to-address').text( ship_to_address );
		activeStep('.owc-shipping', '.step-2');
	});
	
	$('.owc-mobile-order-summary .summary-header').click(function(e){
		e.preventDefault();
		$('.owc-mobile-order-summary .owc-order-summary').slideToggle();
	});

	$('.owc-payment').click(function(e){
		e.preventDefault();
		
		let shipping_selection = '';
		if( $('.owc-shipping-methods-box.one-method-only').length > 0 ){
			shipping_selection = $('.owc-shipping-methods-box.one-method-only').find('label').text();
		} else {
			shipping_selection = $('.owc-shipping-methods-box #shipping_method li input:checked').next().text();	
		}
		
		$('.information-review-box .shipping-method').text( shipping_selection );

		activeStep('.owc-payment', '.step-3');
	});

	// Billing address selection
	$('input[name=billing_address_choice]').change(function(e){
		e.preventDefault();

		let billing_fields = $('.billing-fields');

		let $choice = $(this).val();
		if( $choice == 'same_billing'){
			billing_fields.slideUp();
		}
		if( $choice == 'different_billing'){
			billing_fields.slideDown();
		}
	});

	/**
	 * Shows new notices on the page.
	 *
	 * @param {Object} The Notice HTML Element in string or object form.
	 */
	var show_notice = function( html_element, $target ) {
		if ( ! $target ) {
			$target = $( '.woocommerce-notices-wrapper:first' ) ||
				$( '.cart-empty' ).closest( '.woocommerce' ) ||
				$( '.woocommerce-cart-form' );
		}
		$target.prepend( html_element );
	};

	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	var is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	var block = function( $node ) {
		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};
	

	// apply coupon
	$(document).on('click', '.apply_coupon_code',function(){
		let coupon_code = $(this).parent().find('input.input-text').val();
		let security 	= $(this).parent().find('input.coupon_nonce').val();
		var cart = this;
		if( coupon_code == '' ){
			return;
		}

		let $form = $(document).find('form.woocommerce-checkout');
		block( $form );

		$.ajax({
			type : "post",
			dataType : "json",
			url : '/?wc-ajax=apply_coupon',
			data : {security: security, coupon_code : coupon_code},
			dataType: 'html',
			success: function( response ) {
				$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
				show_notice( response, $( '.coupon_applied_msg' ) );
				$( document.body ).trigger( 'applied_coupon', [ coupon_code ] );
			},
			complete: function() {
				$( 'body' ).trigger( 'update_checkout' );
				unblock( $form );
			}
		});  



	});
})( jQuery );
