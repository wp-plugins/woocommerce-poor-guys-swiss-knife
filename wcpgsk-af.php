<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * Execute actions and filters
 * Checks for standard configured actions and filters and attaches these actions and filters
 *
 */
global $wcpgsk_options;

add_filter( 'woocommerce_add_to_cart_validation', 'wcpgsk_maxitems_in_cart', 10, 3 );
add_filter( 'woocommerce_update_cart_validation', 'wcpgsk_minitems_in_cart', 1, 4 );
add_filter( 'woocommerce_is_sold_individually', 'wcpgsk_check_qty_config', 10, 2 );
add_filter( 'woocommerce_quantity_input_max', 'wcpgsk_qtyselector_max', 10, 2 );
add_filter( 'woocommerce_quantity_input_min', 'wcpgsk_qtyselector_min', 10, 2 );
add_filter( 'woocommerce_quantity_input_args', 'wcpgsk_qty_input_args', 10, 2 );
add_filter( 'single_add_to_cart_text', 'wcpgsk_single_cart_button_text', 10, 2 );
add_filter( 'add_to_cart_text', 'wcpgsk_cart_button_text', 10, 1 );
add_action( 'woocommerce_after_checkout_form','wcpgsk_after_checkout_form', 10, 1 );

add_action( 'woocommerce_after_cart_item_quantity_update', 'wcpgsk_after_cart_item_quantity_update', 10, 2 );
add_filter( 'woocommerce_add_cart_item', 'wcpgsk_add_cart_item', 10, 2 );

				
if ( !function_exists('wcpgsk_add_cart_item') ) {
function wcpgsk_add_cart_item( $cart_item_data, $cart_item_key ) {
	global $woocommerce;
	$options = get_option( 'wcpgsk_settings' );
	
	$product_id = $cart_item_data['product_id'];
	$variation_id = $cart_item_data['variation_id'];
	$product = get_product($product_id);
	$quantity = $cart_item_data['quantity'];
	
	$maxqty = isset($options['cart']['maxqty_' . $product->product_type]) ? $options['cart']['maxqty_' . $product->product_type] : 0;
	$minqty = isset($options['cart']['minqty_' . $product->product_type]) ? $options['cart']['minqty_' . $product->product_type] : 0;
	
	if ($minqty > 0 && $quantity < $minqty) :
		$cart_item_data['quantity'] = $minqty;
		$woocommerce->add_message( sprintf( __( 'You have to buy a minimum quantity. We have set the required minimum of %s as quantity for you.', WCPGSK_DOMAIN ), $minqty ) );
		$woocommerce->set_messages();		
	elseif ($maxqty > 0 && $quantity > $maxqty) :
		$cart_item_data['quantity'] = $maxqty;
		$woocommerce->add_message( sprintf(__( 'You cannot buy more than the allowed maximum quantity. We have set the allowed maximum of %s as quantity for you.', WCPGSK_DOMAIN ), $maxqty ) );
		$woocommerce->set_messages();
	endif;

	
	return $cart_item_data;
}
}
				
if ( !function_exists('wcpgsk_after_cart_item_quantity_update') ) {
function wcpgsk_after_cart_item_quantity_update( $cart_item_key, $quantity ) {
	global $woocommerce;
	$options = get_option( 'wcpgsk_settings' );
	$product_id = $woocommerce->cart->cart_contents[$cart_item_key]['product_id'];
	$variation_id = $woocommerce->cart->cart_contents[$cart_item_key]['variation_id'];
	$product = get_product($product_id);
	
	$maxqty = isset($options['cart']['maxqty_' . $product->product_type]) ? $options['cart']['maxqty_' . $product->product_type] : 0;
	$minqty = isset($options['cart']['minqty_' . $product->product_type]) ? $options['cart']['minqty_' . $product->product_type] : 0;
	if ($minqty > 0 && $quantity < $minqty) :
		$woocommerce->cart->cart_contents[$cart_item_key]['quantity'] = $minqty;
		$woocommerce->add_message( sprintf( __( 'You have to buy a minimum quantity. We have set the required minimum of %s as quantity for you.', WCPGSK_DOMAIN ), $minqty ) );
		$woocommerce->set_messages();
		
	elseif ($maxqty > 0 && $quantity > $maxqty) :
		$woocommerce->cart->cart_contents[$cart_item_key]['quantity'] = $maxqty;
		$woocommerce->add_message( sprintf(__( 'You cannot buy more than the allowed maximum quantity. We have set the allowed maximum of %s as quantity for you.', WCPGSK_DOMAIN ), $maxqty ) );
		$woocommerce->set_messages();
	endif;
}
}


if ( !function_exists('wcpgsk_maxitems_in_cart') ) {
function wcpgsk_maxitems_in_cart( $valid, $product_id, $quantity ) {
	global $woocommerce;
	$valid = true;
	$options = get_option( 'wcpgsk_settings' );
	
	$cartItems = sizeof( $woocommerce->cart->cart_contents );
	$allowed = isset($options['cart']['maxitemscart']) && $options['cart']['maxitemscart'] != 0 ? $options['cart']['maxitemscart'] : 0;
	
	//check cart items count and diminish if more than one variation for a product exists
	if ( $allowed > 0 && isset($options['cart']['variationscountasproduct']) && $options['cart']['variationscountasproduct'] == 0) {	
		$varproducts = array();
		foreach($woocommerce->cart->cart_contents as $i => $values) {
			$key = $values['product_id'];
			//@TODO: Check layout of the question and answer
			if (isset($values[$key]) && isset($values['variation_id']) && $values[$key] != $values['variation_id']) {
				if (isset($varproducts[$key])) $varproducts[$key] = 1;
				else $varproducts[$key] = 0;
			}
		}
		if (!empty($varproducts)) $cartItems = $cartItems - array_sum($varproducts);
	}
	
	if ( $allowed > 0 && $cartItems >= $allowed ) {
 		// Sets error message.
		$woocommerce->add_message( sprintf( __( 'You have reached the maximum amount of %s items allowed for your cart!', WCPGSK_DOMAIN ), $allowed ) );
		$woocommerce->set_messages();
		$valid = false;
		$cart_url = $woocommerce->cart->get_cart_url();
		$woocommerce->add_message( __('Remove products from the cart', WCPGSK_DOMAIN) . ': <a href="' . $cart_url . '">' . __('Cart', WCPGSK_DOMAIN) . '</a>');
		$woocommerce->set_messages();
	}
	return $valid;
}
}


if ( !function_exists('wcpgsk_minitems_in_cart') ) {
/**
* Validate product quantity on cart update.
*/
function wcpgsk_minitems_in_cart( $valid, $cart_item_key, $values, $quantity ) {
	global $woocommerce;
	$valid = true;
	$options = get_option( 'wcpgsk_settings' );

	//$cartItems = $woocommerce->cart->get_cart_contents_count(); //counts quantities as well and not only items
	$cartItems = sizeof( $woocommerce->cart->cart_contents );
	$allowed = isset($options['cart']['minitemscart']) && $options['cart']['minitemscart'] != 0 ? $options['cart']['minitemscart'] : 0;
	
	//check cart items count and diminish if more than one variation for a product exists
	if ($allowed > 1 && isset($options['cart']['variationscountasproduct']) && $options['cart']['variationscountasproduct'] == 0) {	
		$varproducts = array();
		foreach($woocommerce->cart->cart_contents as $i => $values) {
			$key = $values['product_id'];
			//@TODO: Check layout of the question and answer			
			if (isset($values[$key]) && isset($values['variation_id']) && $values[$key] != $values['variation_id']) {
				if (isset($varproducts[$key])) $varproducts[$key] = 1;
				else $varproducts[$key] = 0;
			}
		}
		if (!empty($varproducts)) $cartItems = $cartItems - array_sum($varproducts);
	}
	
	if ($allowed > 1 && $allowed > $cartItems ) {
 		// Sets error message.
		$woocommerce->add_message( sprintf( __( 'You still have not reached the minimum amount of %s items required for your cart!', WCPGSK_DOMAIN ), $allowed ) );
		$woocommerce->set_messages();
		$valid = false;
		
		$shop_page_id = woocommerce_get_page_id( 'shop' );
		//$shop_page_url = get_permalink(icl_object_id($shop_page_id, 'page', false));
		$shop_page_url = get_permalink($shop_page_id);
		$woocommerce->add_message( __('Select more products from the shop', WCPGSK_DOMAIN) . ': <a href="' . $shop_page_url . '">' . __('Shop', WCPGSK_DOMAIN) . '</a>');
		$woocommerce->set_messages();
		
	}
	return $valid;
}
}

if ( !function_exists('wcpgsk_check_qty_config') ) {
function wcpgsk_check_qty_config( $return, $product ) {
	global $woocommerce;
	$options = get_option( 'wcpgsk_settings' );
	$switch = false;
	
    switch ($product->product_type) {
		case 'variation' :
			if( isset($options['cart']['variationproductnoqty']) && $options['cart']['variationproductnoqty'] == 1)
				$switch = true;
			break;
		case 'variable' :
			if(isset($options['cart']['variableproductnoqty']) && $options['cart']['variableproductnoqty'] == 1)
				$switch = true;
			break;
		case 'grouped' :
			 if( isset($options['cart']['groupedproductnoqty']) && $options['cart']['groupedproductnoqty'] == 1)
				$switch = true;
			break;
		case 'external' :
			if( isset($options['cart']['externalproductnoqty']) && $options['cart']['externalproductnoqty'] == 1)
				$switch = true;
			break;
		default :
			if( isset($options['cart']['simpleproductnoqty']) && $options['cart']['simpleproductnoqty'] == 1)
				$switch = true;
			break;
	}
	return $switch;
}
}

if ( !function_exists('wcpgsk_qtyselector_max') ) {
function wcpgsk_qtyselector_max( $whatever, $product ) {
	global $wcpgsk_session;
	$options = get_option( 'wcpgsk_settings' );
	$maxqty = isset($options['cart']['maxqty_' . $product->product_type]) && $options['cart']['maxqty_' . $product->product_type] != 0 ? $options['cart']['maxqty_' . $product->product_type] : '';
	if ($maxqty == '' && isset($wcpgsk_session->qtyargs['max_value']) ) {
		$maxqty = $wcpgsk_session->qtyargs['max_value'];
	}
	return $maxqty;
}
}

if ( !function_exists('wcpgsk_qtyselector_min') ) {
function wcpgsk_qtyselector_min( $whatever, $product ) {
	global $wcpgsk_session;
	$options = get_option( 'wcpgsk_settings' );
	$minqty = isset($options['cart']['minqty_' . $product->product_type]) && $options['cart']['minqty_' . $product->product_type] != 0 ? $options['cart']['minqty_' . $product->product_type] : '';
	if ($minqty == '' && isset($wcpgsk_session->qtyargs['min_value']) ) {
		$minqty = $wcpgsk_session->qtyargs['min_value'];
	}
	return $minqty;
}
}

if ( !function_exists('wcpgsk_qty_input_args') ) {
function wcpgsk_qty_input_args($args, $product) {
	global $wcpgsk_session;
	$wcpgsk_session->qtyargs = $args;
	return $args;
}
}

 /**
 * Redirect to fast checkout
 */
if ( !function_exists('wcpgsk_add_to_checkout_redirect') ) {
function wcpgsk_add_to_checkout_redirect() {	
	return get_permalink( woocommerce_get_page_id( 'checkout' ) );
}
}
 /**
 * Redirect to cart
 */
if ( !function_exists('wcpgsk_add_to_cart_redirect') ) {
function wcpgsk_add_to_cart_redirect() {	
	return get_permalink( woocommerce_get_page_id( 'cart' ) );
}
}


if ( isset($wcpgsk_options['process']['fastcart']) && $wcpgsk_options['process']['fastcart'] == 1 && $wcpgsk_options['process']['fastcheckout'] == 0) {
	update_option('woocommerce_cart_redirect_after_add', 'yes');
	//update_option('woocommerce_enable_ajax_add_to_cart', 'no');
}
elseif ( isset($wcpgsk_options['process']['fastcheckout']) && $wcpgsk_options['process']['fastcheckout'] == 1 && $wcpgsk_options['process']['fastcart'] == 0) {
	update_option('woocommerce_cart_redirect_after_add', 'no');
	//update_option('woocommerce_enable_ajax_add_to_cart', 'no'); //@TODO: check if this option is correct like this
	add_filter('add_to_cart_redirect', 'wcpgsk_add_to_checkout_redirect', 99); //late execution, to assure that we overwrite WooCommerce Setting
}


if ( !function_exists('wcpgsk_cart_button_text') ) {
/**
 * Personalize Add to Cart Button
 */
function wcpgsk_cart_button_text($label) {
	$options = get_option( 'wcpgsk_settings' );
	$cart_btn_text = ((!empty($options['process']['fastcheckoutbtn'])) ? __($options['process']['fastcheckoutbtn'], WCPGSK_DOMAIN) : $label);
	if ($cart_btn_text && $cart_btn_text != '')
		return apply_filters(__('wcpgsk_cart_button_text', WCPGSK_DOMAIN), $cart_btn_text);
	else return apply_filters('wcpgsk_cart_button_text', __('Add to Cart', WCPGSK_DOMAIN));
}
}

if ( !function_exists('wcpgsk_single_cart_button_text') ) {
function wcpgsk_single_cart_button_text($label, $ptype) {
	$options = get_option( 'wcpgsk_settings' );
	$cart_btn_text = ((!empty($options['process']['fastcheckoutbtn'])) ? __($options['process']['fastcheckoutbtn'], WCPGSK_DOMAIN) : 'Add to Cart');
	if ($cart_btn_text && $cart_btn_text != '')
		return apply_filters(__('wcpgsk_cart_button_text', WCPGSK_DOMAIN), $cart_btn_text, $ptype);
	else return apply_filters('wcpgsk_cart_button_text', __('Add to Cart', WCPGSK_DOMAIN), $ptype);
}
}

//Payment gateways
$wcpgsk_options = get_option('wcpgsk_settings');
if ( isset($wcpgsk_options['process']['paymentgateways']) && 1 == ($wcpgsk_options['process']['paymentgateways'])) :
	require_once ABSPATH . WPINC . '/pluggable.php';;
	require_once dirname(dirname(__FILE__)).'/woocommerce/classes/class-wc-payment-gateways.php';
	require_once dirname(dirname(__FILE__)).'/woocommerce/classes/class-wc-cart.php';

	add_action( 'add_meta_boxes', 'wcpgsk_gateways_meta_box_add' );  
	if ( !function_exists('wcpgsk_gateways_meta_box_add') ) {
	function wcpgsk_gateways_meta_box_add()  
	{  
		add_meta_box( 'payments', 'Payment Gateways', 'wcpgsk_payments_form', 'product', 'side', 'core' ); 
	}
	}
	if ( !function_exists('wcpgsk_payments_form') ) {
	function wcpgsk_payments_form()  
	{
		global $post, $woo;
		$postPayments = get_metadata('post', $post->ID, 'payment_gateways', false) ;
		$woo = new WC_Payment_Gateways();
		$payments = $woo->get_available_payment_gateways();
		foreach($payments as $pay){
			$checked = '';
			if(in_array($pay->id, $postPayments)) $checked = ' checked="yes" ';
			?>  
				<input type="checkbox" <?php echo $checked; ?> value="<?php echo $pay->id; ?>" name="pays[]" id="payments" />
				<label for="payment_gateway_meta_box_text"><?php echo $pay->title; ?></label>  
				<br />  
			<?php 
		}      
	} 
	}
	add_action('save_post', 'wcpgsk_gateways_meta_box_save', 10, 2 );
	if ( !function_exists('wcpgsk_gateways_meta_box_save') ) {
	function wcpgsk_gateways_meta_box_save( $post_id )  
	{   
		if(isset($_POST['post_type']) && $_POST['post_type']=='product') :
		delete_post_meta($post_id, 'payment_gateways');	 
			if($_POST['pays']) :
				foreach($_POST['pays'] as $pay) :
					add_post_meta($post_id, 'payment_gateways', $pay); 					
				endforeach;
			endif;
		
		endif;
	}
	}
	if ( !function_exists('wcpgsk_restrict_payment_gateways') ) {
	function wcpgsk_restrict_payment_gateways( $restrict_gateways ) {
		global $woocommerce;
		$arrayKeys = array_keys($restrict_gateways);
		$items = isset($woocommerce->cart->cart_contents) ? $woocommerce->cart->cart_contents : array();
		$itemGateways = '';
		if($items)
			foreach($items as $item)
			$itemGateways[] = get_metadata('post', $item['product_id'], 'payment_gateways', false) ;
		if($itemGateways)
			foreach($itemGateways as $gateway)
			if(count($gateway)) :
				foreach($arrayKeys as $key) :
					if(!in_array($key, $gateway)) :
						unset($restrict_gateways[$key]);
					endif;
				endforeach;
			endif;
		return $restrict_gateways;
	}
	}
	add_filter( 'woocommerce_available_payment_gateways', 'wcpgsk_restrict_payment_gateways' );
endif;

if ( !function_exists('') ) {
function wcpgsk_after_checkout_form($checkout) {
	?>
	<div id="wcpgsk-dialog-validation-errors" title="<?php _e('Validation errors' , WCPGSK_DOMAIN); ?>">
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span><?php _e('Please check the fields marked with a red border. The values do not pass validation.' , WCPGSK_DOMAIN); ?></p>		
	</div>
	<?php
	$options = get_option( 'wcpgsk_settings' );

	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'jquery-ui-tabs' );
		
	wp_enqueue_script( 'jquery-ui-sliderAccess', plugins_url('/assets/js/jquery-ui-sliderAccess.js', __FILE__) , '', '', false);
	wp_enqueue_script( 'jquery-ui-timepicker-addon', plugins_url('/assets/js/jquery-ui-timepicker-addon.js', __FILE__) , '', '', false);

	wp_enqueue_script( 'wcpgsk-validate', plugins_url('/assets/js/wcpgsk-validate.js', __FILE__) , '', '', false);

	
	wp_enqueue_style( 'jquery-ui', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery-ui.css" , '', '', false);
	wp_enqueue_style( 'jquery-ui-timepicker-addon', plugins_url('/assets/css/jquery-ui-timepicker-addon.css', __FILE__) , '', '', false);

	echo '<script language="javascript">';

	echo 'jQuery(document).ready(function(){
			jQuery("#cartitemfields").tabs();
			var cT = "' . __('Close', WCPGSK_DOMAIN) . '";
			var pT = "' . __('<Prev', WCPGSK_DOMAIN) . '";
			var nT = "' . __('Next>', WCPGSK_DOMAIN) . '";
			var cTT = "' . __('Now', WCPGSK_DOMAIN) . '";
			var cTD = "' . __('Today', WCPGSK_DOMAIN) . '";
			
			var mN = ["' . __('January', WCPGSK_DOMAIN) . '", 
				"' . __('February', WCPGSK_DOMAIN) . '",
				"' . __('March', WCPGSK_DOMAIN) . '",
				"' . __('April', WCPGSK_DOMAIN) . '",
				"' . __('May', WCPGSK_DOMAIN) . '",
				"' . __('June', WCPGSK_DOMAIN) . '",
				"' . __('July', WCPGSK_DOMAIN) . '",
				"' . __('August', WCPGSK_DOMAIN) . '",
				"' . __('September', WCPGSK_DOMAIN) . '",
				"' . __('October', WCPGSK_DOMAIN) . '",
				"' . __('November', WCPGSK_DOMAIN) . '",
				"' . __('December', WCPGSK_DOMAIN) . '"];

			var mNS = ["' . __('Jan', WCPGSK_DOMAIN) . '", 
				"' . __('Feb', WCPGSK_DOMAIN) . '",
				"' . __('Mar', WCPGSK_DOMAIN) . '",
				"' . __('Apr', WCPGSK_DOMAIN) . '",
				"' . __('May', WCPGSK_DOMAIN) . '",
				"' . __('Jun', WCPGSK_DOMAIN) . '",
				"' . __('Jul', WCPGSK_DOMAIN) . '",
				"' . __('Aug', WCPGSK_DOMAIN) . '",
				"' . __('Sep', WCPGSK_DOMAIN) . '",
				"' . __('Oct', WCPGSK_DOMAIN) . '",
				"' . __('Nov', WCPGSK_DOMAIN) . '",
				"' . __('Dec', WCPGSK_DOMAIN) . '"];

			var dN = ["' . __('Sunday', WCPGSK_DOMAIN) . '", 
				"' . __('Monday', WCPGSK_DOMAIN) . '",
				"' . __('Tuesday', WCPGSK_DOMAIN) . '",
				"' . __('Wednesday', WCPGSK_DOMAIN) . '",
				"' . __('Thursday', WCPGSK_DOMAIN) . '",
				"' . __('Friday', WCPGSK_DOMAIN) . '",
				"' . __('Saturday', WCPGSK_DOMAIN) . '"];

			var dNS = ["' . __('Sun', WCPGSK_DOMAIN) . '", 
				"' . __('Mon', WCPGSK_DOMAIN) . '",
				"' . __('Tue', WCPGSK_DOMAIN) . '",
				"' . __('Wed', WCPGSK_DOMAIN) . '",
				"' . __('Thu', WCPGSK_DOMAIN) . '",
				"' . __('Fri', WCPGSK_DOMAIN) . '",
				"' . __('Sat', WCPGSK_DOMAIN) . '"];
				
			jQuery("input[display=\'date\']").each(function() {
				var minD = "' . $options['checkoutform']['mindate'] . '";
				var maxD = "' . $options['checkoutform']['maxdate'] . '";
				if (jQuery(this).attr("mindays")) minD = jQuery(this).attr("mindays");
				if (jQuery(this).attr("maxdays")) maxD = jQuery(this).attr("maxdays");
				
				jQuery(this).datepicker({
					dateFormat: "yy/mm/dd",
					minDate: minD,
					maxDate: maxD,
					dayNamesShort: dNS,
					dayNames: dN,
					monthNamesShort: mNS,
					monthNames: mN,				
					closeText: cT,
					prevText: pT,
					nextText: nT,
					currentText: cTD,
					firstDay: 1
				});		
			});

			jQuery("input[display=\'time\']").each(function() {
				var hMax = 23;
				var hMin = 0;
				if (jQuery(this).attr("maxhour")) hMax = parseInt(jQuery(this).attr("maxhour"));
				if (jQuery(this).attr("minhour")) hMin = parseInt(jQuery(this).attr("minhour"));
				
				jQuery(this).timepicker({
					timeFormat: "HH:mm",
					hourMax: hMax,
					hourMin: hMin,
					stepHour: parseInt(jQuery(this).attr("hoursteps")),
					stepMinute: parseInt(jQuery(this).attr("minutesteps")),
					addSliderAccess: true,
					sliderAccessArgs: { touchonly: false },
					timeText: "' . __('Time', WCPGSK_DOMAIN) . '",
					hourText: "' . __('Hour', WCPGSK_DOMAIN) . '",
					minuteText: "' . __('Minute', WCPGSK_DOMAIN) . '",
					currentText: cTT,
					closeText: cT,
					timeOnlyTitle: "' . __('Choose Time', WCPGSK_DOMAIN) . '"
				});		
			});

			jQuery("input[display=\'number\']").each(function() {
				var $this = this;
				jQuery(this).after("<div id=\'slider_" + jQuery(this).attr("id") + "\'></div>");

				if (jQuery($this).attr("numpres") == "true") {
					jQuery("#slider_" + jQuery($this).attr("id")).slider({
						range: true,
						min: parseInt(jQuery($this).attr("minvalue")),
						max: parseInt(jQuery($this).attr("maxvalue")),
						step: parseInt(jQuery($this).attr("numstep")),
						values: [ parseInt(jQuery($this).val()), parseInt(jQuery($this).attr("rangemax")) ],
						slide: function( event, ui ) {
							jQuery( $this ).val( ui.values[0] + " - " +  ui.values[1]);
						}		
					});
				}
				else {
					jQuery("#slider_" + jQuery($this).attr("id")).slider({
						range: jQuery($this).attr("numpres"),
						min: parseInt(jQuery($this).attr("minvalue")),
						max: parseInt(jQuery($this).attr("maxvalue")),
						step: parseInt(jQuery($this).attr("numstep")),
						value: parseInt(jQuery($this).val()),
						slide: function( event, ui ) {
							jQuery( $this ).val( ui.value );
						}		
					}).sliderAccess({ touchonly : false });
				}
			});
	
			jQuery("select[presentation=\'radio\']").each(function(i, select){
				var $select = jQuery(select);
				$select.find("option").each(function(j, option){
					var $option = jQuery(option);
					// Create a radio:
					var $radio = jQuery("<input type=\'radio\' />");
					// Set name and value:
					$radio.attr("name", $select.attr("name")).attr("value", $option.val()).attr("class", "radio").attr("style","width:10%");
					// Set checked if the option was selected
					if ($option.attr("selected")) $radio.attr("checked", "checked");
					//$radio.text($option.text());
					// Insert radio before select box:
					$select.before($radio);
					// Insert a label:
					$select.before(
					  jQuery("<span />").attr("for", $select.attr("name")).text($option.text())
					);
					// Insert a <br />:
					$select.before("<br/>");
				});
				$select.remove();
			});
		
			jQuery("select[presentation=\'checkbox\']").each(function(i, select){
				var $select = jQuery(select);
				$select.find("option").each(function(j, option){
					var $option = jQuery(option);
					// Create a radio:
					var $radio = jQuery("<input type=\'checkbox\' />");
					// Set name and value:
					$radio.attr("name", $select.attr("name") + "[" + j + "]").attr("value", $option.val()).attr("class", "checkbox").attr("style","width:10%");
					// Set checked if the option was selected
					if ($option.attr("selected")) $radio.attr("checked", "checked");
					//$radio.text($option.text());
					// Insert radio before select box:
					$select.before($radio);
					// Insert a label:
					$select.before(
					  jQuery("<span />").attr("for", $select.attr("name")).text($option.text())
					);
					$select.before("<br/>");
				});
				$select.remove();
			});
		});
	</script><!--unit test after checkout end-->';
}

}

