<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * WooCommercePoorGuysSwissKnife Main Class
 *
 * @class 		WCPGSK_Main
 * @version		1.1
 * @package		WooCommerce-Poor-Guys-Swiss-Knife/Classes
 * @category	Class
 * @author 		Uli Hake
 */
 
if ( ! class_exists ( 'WCPGSK_Main' ) ) {

    class WCPGSK_Main {
		private $dir;
		private $assets_dir;
		private $assets_url;
		public $version;
		private $file;
		
		/**
		 * Constructor function.
		 *
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function __construct( $file ) {
			global $wcpgsk_about;
			$this->dir = dirname( $file );
			$this->file = $file;
			$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
			$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
			$this->load_plugin_textdomain();		

			add_action( 'init', array( $this, 'load_localisation' ), 0 );
			// Run this on activation.
			register_activation_hook( $this->file, array( $this, 'activation' ) );
			if ( is_admin() ) : // admin actions
				// Hook into admin_init first
				add_action( 'admin_init', array($this, 'wcpgsk_register_setting') );
				add_filter( 'plugin_action_links_'. plugin_basename($this->file), array($this, 'wcpgsk_admin_plugin_actions'), -10 );
				add_action( 'admin_menu', array($this, 'wcpgsk_admin_menu') );				
				$wcpgsk_about = new WCPGSK_About( $this->file );
			endif;
			
			//billing and shipping filters
			add_filter( 'woocommerce_billing_fields' , array($this, 'add_billing_custom_fields'), 10, 1 );
			add_filter( 'woocommerce_shipping_fields' , array($this, 'add_shipping_custom_fields'), 10, 1 );


			add_filter('woocommerce_admin_billing_fields', array($this, 'wcpgsk_admin_billing_fields'), 10, 1);
			add_filter('woocommerce_admin_shipping_fields', array($this, 'wcpgsk_admin_shipping_fields'), 10, 1);

			add_filter( 'woocommerce_checkout_fields' , array($this, 'wcpgsk_checkout_fields_billing'), 10, 1 );
			add_filter( 'woocommerce_checkout_fields' , array($this, 'wcpgsk_checkout_fields_shipping'), 10, 1 );

			add_action( 'woocommerce_checkout_process', array($this, 'wcpgsk_checkout_process') );

			add_filter( 'woocommerce_load_order_data', array($this, 'wcpgsk_load_order_data'), 5,  1);
			add_action( 'woocommerce_checkout_init', array($this, 'wcpgsk_checkout_init'), 10, 1 );
			
			//@TODO: We could offer an option to configure the billing and shipping formatted address
			
			add_action( 'woocommerce_email_after_order_table', array($this, 'wcpgsk_email_after_order_table') );// $order, false, true );
			add_action( 'woocommerce_order_details_after_order_table', array($this, 'wcpgsk_order_details_after_order_table'), 10, 1 );
			
		}

		/**
		 * Check for minimum and maximum items in cart and if they fulfill the settings.
		 * and raise error and message if rules are not fulfilled, otherwise clear messages
		 *
		 * @access public
		 * @param mixed $checkout
		 * @since 1.1.0
		 * @return void
		 */		
		public function wcpgsk_checkout_init($checkout) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			
			$cartItems = sizeof( $woocommerce->cart->cart_contents );
			$allowed = $options['cart']['maxitemscart'];
			
			//check cart items count and diminish if more than one variation for a product exists
			if ($options['cart']['variationscountasproduct'] == 0) {	
				$varproducts = array();
				foreach($woocommerce->cart->cart_contents as $i => $values) {
					$key = $values['product_id'];
					if (isset($values[$key]) && isset($values[$variation_id]) && $values[$key] != $values['variation_id']) {
						if (isset($varproducts[$key])) $varproducts[$key] = 1;
						else $varproducts[$key] = 0;
					}
				}
				if (!empty($varproducts)) $cartItems = $cartItems - array_sum($varproducts);
			}
			
			if ( $cartItems > $allowed ) :
				$woocommerce->clear_messages();
				// Sets error message.
				$woocommerce->add_error( sprintf( __( 'You have reached the maximum amount of %s items allowed for your cart!', WCPGSK_DOMAIN ), $allowed ) );
				$woocommerce->set_messages();
				$cart_url = $woocommerce->cart->get_cart_url();
				$woocommerce->add_message( __('Remove products from the cart', WCPGSK_DOMAIN) . ': <a href="' . $cart_url . '">' . __('Cart', WCPGSK_DOMAIN) . '</a>');
				$woocommerce->set_messages();
				//wp_redirect( get_permalink( woocommerce_get_page_id( 'cart' ) ) );
				//exit;				
			else :
				$allowed = $options['cart']['minitemscart'];

				//check cart items count and diminish if more than one variation for a product exists
				if ($options['cart']['variationscountasproduct'] == 0) {	
					$varproducts = array();
					foreach($woocommerce->cart->cart_contents as $i => $values) {
						$key = $values['product_id'];
						if (isset($values[$key]) && isset($values['variation_id']) && $values[$key] != $values['variation_id']) {
							if (isset($varproducts[$key])) $varproducts[$key] = 1;
							else $varproducts[$key] = 0;
						}
					}
					if (!empty($varproducts)) $cartItems = $cartItems - array_sum($varproducts);
				}
				
				if ($allowed > 1 && $allowed > $cartItems ) :
					// Sets error message.
					$woocommerce->clear_messages();

					$woocommerce->add_error( sprintf( __( 'You still have not reached the minimum amount of %s items required for your cart!', WCPGSK_DOMAIN ), $allowed )  );
					$woocommerce->set_messages();
					$valid = false;
					
					$shop_page_id = woocommerce_get_page_id( 'shop' );
					//$shop_page_url = get_permalink(icl_object_id($shop_page_id, 'page', false));
					$shop_page_url = get_permalink($shop_page_id);
					$woocommerce->add_message( __('Select more products from the shop', WCPGSK_DOMAIN) . ': <a href="' . $shop_page_url . '">' . __('Shop', WCPGSK_DOMAIN) . '</a>');
					$woocommerce->set_messages();
					//wp_redirect( get_permalink( woocommerce_get_page_id( 'shop' ) ) );
					//exit;								
				else :
					$woocommerce->clear_messages();			
				endif;
			endif;
		}
		
		/**
		 * Register settings.
		 * Establish settings if no default settings are available for some resason
		 *
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */		
		public function wcpgsk_register_setting() {	
			register_setting( 'wcpgsk_options', 'wcpgsk_settings', array($this, 'wcpgsk_options_validate') );
			//check if we have initial settings, if not store default settings
			global $wcpgsk_options;
			$this->register_plugin_version();
			$wcpgsk_options = get_option( 'wcpgsk_settings' );
			if ( empty($wcpgsk_options) ) :
				$this->wcpgsk_initial_settings();
			endif;
		}

		public function wcpgsk_load_order_data($meta) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$metas = array();
			$checkout_fields = array_merge($woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'billing_' ), $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' ));
			//$meta = array_merge($meta, $checkout_fields);
			foreach ($checkout_fields as $key => $field) : 
				if ( isset($options['woofields']['billing'][$key]['custom_' . $key]) && $options['woofields']['billing'][$key]['custom_' . $key] && !isset($meta[$key]) ) :
					$meta[$key] = '';
				elseif ( isset($options['woofields']['shipping'][$key]['custom_' . $key]) && $options['woofields']['shipping'][$key]['custom_' . $key] && !isset($meta[$key]) ) : 
					$meta[$key] = '';
				endif;				
			endforeach;

			return apply_filters( 'wcpgsk_load_order_data', $meta );	
		}
		
		/**
		 * Display billing and shipping form data captured.
		 *
		 * @access public
		 * @since 1.3.0
		 * @output html
		 */		
		public function wcpgsk_email_after_order_table($order) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$billing_fields = $this->wcpgsk_additional_data($order, 'billing');
			$shipping_fields = $this->wcpgsk_additional_data($order, 'shipping');
			?>
			<table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">

				<tr>
					<?php 
					if ( isset($billing_fields) && !empty($billing_fields) ) : 
					?>
					<td valign="top" width="50%">

						<h3><?php _e( 'Additional billing data', 'woocommerce' ); ?></h3>

						<p>
						<?php 
							$billing_fields = $this->wcpgsk_additional_data($order, 'billing');
							if ( isset($billing_fields) && !empty($billing_fields) ) :
								foreach ($billing_fields as $key => $field) :
									echo $field['label'] . ": " . $field['captured'] . '<br />';
								endforeach;
							endif;
						?>
						</p>

					</td>

					<?php 
					endif;
					if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && isset($shipping_fields) && !empty($shipping_fields) ) : 
					?>

					<td valign="top" width="50%">

						<h3><?php _e( 'Additional shipping data', 'woocommerce' ); ?></h3>

						<p>
						<?php 
							foreach ($shipping_fields as $key => $field) :
								echo $field['label'] . ": " . $field['captured'] . '<br />';
							endforeach;
						?>
						</p>

					</td>

					<?php endif; ?>

				</tr>

			</table>
			<?php
		}
		
		/**
		 * Display billing and shipping form data captured.
		 *
		 * @access public
		 * @since 1.3.0
		 * @output html
		 */		
		public function wcpgsk_order_details_after_order_table($order) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$billing_fields = $this->wcpgsk_additional_data($order, 'billing');
			$shipping_fields = $this->wcpgsk_additional_data($order, 'shipping');
			?>
			<table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">

				<tr>
					<?php 
					if ( isset($billing_fields) && !empty($billing_fields) ) : 
					?>
					<td valign="top" width="50%">

						<h3><?php _e( 'Additional billing data', 'woocommerce' ); ?></h3>

						<p>
						<?php 
							$billing_fields = $this->wcpgsk_additional_data($order, 'billing');
							if ( isset($billing_fields) && !empty($billing_fields) ) :
								foreach ($billing_fields as $key => $field) :
									echo $field['label'] . ": " . $field['captured'] . '<br />';
								endforeach;
							endif;
						?>
						</p>

					</td>

					<?php 
					endif;
					if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && isset($shipping_fields) && !empty($shipping_fields) ) : 
					?>

					<td valign="top" width="50%">

						<h3><?php _e( 'Additional shipping data', 'woocommerce' ); ?></h3>

						<p>
						<?php 
							foreach ($shipping_fields as $key => $field) :
								echo $field['label'] . ": " . $field['captured'] . '<br />';
							endforeach;
						?>
						</p>

					</td>

					<?php endif; ?>

				</tr>

			</table>
			<?php
		}
		
		/**
		 * Update our formatted address.
		 *
		 * @access public
		 * @since 1.3.0
		 * @output Settings page
		 */		
		public function wcpgsk_formatted_address_replacements($formatted, $args) {
			return $formatted;	
		}
		
		/**
		 * Update our order billing address.
		 *
		 * @access public
		 * @since 1.3.0
		 * @output Settings page
		 */		
		public function wcpgsk_additional_data($order, $fortype) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$captured_fields = array();
			$field_order = 1;	
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), $fortype . '_' );
			foreach ($checkout_fields as $key => $field) :
				$checkout_fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
				$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
				$checkout_fields[$key]['showorder'] = ((isset($options['woofields']['showorder_' . $key])) ? $options['woofields']['showorder_' . $key] : 0);
				if (!isset($configField['label'])) $configField['label'] = __($checkout_fields[$key]['label'], WCPGSK_DOMAIN);				
				$field_order++;
			endforeach;

			uasort($checkout_fields, array($this, "compareFieldOrder"));						

			foreach ($checkout_fields as $key => $field) : 
				//$fieldLabel = $field['displaylabel'];
				$fieldkey = str_replace('billing_', '', $key);
				if ($key != 'billing_email_validator' && $field['showorder'] == 1 && $key != 'billing_phone' && $key != 'billing_email') :
					if ($options['woofields']['billing'][$key]['custom_' . $key]) :
						$configField['label'] = __($checkout_fields[$key]['label'], WCPGSK_DOMAIN);
						$configField['captured'] = $order->$key;
						$captured_fields[$fieldkey] = $configField;
					endif;
				endif;
			endforeach;
			return $captured_fields;	
		}

		/**
		 * Update our order shipping address.
		 *
		 * @access public
		 * @since 1.3.0
		 * @output Settings page
		 */		
		public function wcpgsk_order_formatted_shipping_address($address, $order) {
			return $address;
		}
		
		/**
		 * Our Admin Settings Page.
		 *
		 * @access public
		 * @since 1.1.0
		 * @output Settings page
		 */		
		public function wcpgsk__options_page() {
			global $woocommerce, $wcpgsk_options, $wcpgsk_name;
			//must check that the user has the required capability 
			if (!current_user_can('manage_options'))
			{
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			$hidden_field_name = 'wcpgsk_submit_hidden';
			// read options values
			$options = get_option( 'wcpgsk_settings' );
			if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
				// Save the posted value in the database
				update_option( 'wcpgsk_settings', $options );
			?>
				<div class="updated"><p><strong><?php _e('Settings saved.', WCPGSK_DOMAIN); ?></strong></p></div>
			<?php 
			}
			
			// Now display the settings editing screen
			//get some reused labels
			 
			$placeWide = __('Wide',WCPGSK_DOMAIN);
			$placeFirst = __('First',WCPGSK_DOMAIN);
			$placeLast = __('Last',WCPGSK_DOMAIN);
			$defchecked = __('Default: checked', WCPGSK_DOMAIN);
			$defunchecked = __('Default: unchecked', WCPGSK_DOMAIN);
			
			
			$wcpgsk_options['process']['fastcheckoutbtn'] = isset($wcpgsk_options['process']['fastcheckoutbtn']) ? $wcpgsk_options['process']['fastcheckoutbtn'] : '';
			echo '<div class="wrap">';
			// icon for settings
			echo '<div id="icon-themes" class="icon32"><br></div>';
			// header
			$wcpgsk_name = apply_filters('wcpgsk_plus_name', $wcpgsk_name);
			echo "<h2>" . __( $wcpgsk_name, WCPGSK_DOMAIN ) . "</h2>";
			// settings form 
			?>
			<form name="form" method="post" action="options.php" id="frm1">
				<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
					<?php
						settings_fields( 'wcpgsk_options' );
						$options = get_option( 'wcpgsk_settings' );
						
					?>
				<div id="wcpgsk_accordion">
					<?php do_action( 'wcpgsk_settings_page_zero', $options ); ?>

					<h3 class="wcpgsk_acc_header"><?php echo __('Shop Settings',WCPGSK_DOMAIN); ?></h3>
					<div>
						<table class="widefat" border="1" >
						<thead>
							<tr>
								<th><?php _e('Settings Name', WCPGSK_DOMAIN);  ?></th>
								<th><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
								<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
							</tr>
						</thead>
						<tbody>
							<?php do_action( 'wcpgsk_settings_shop_before', $options ); ?>
							<tr>
								<td width="25%"><?php _e( 'Add to Cart Button Label', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input name="wcpgsk_settings[process][fastcheckoutbtn]" id="wcpgsk_fastcheckout_btn" value="<?php echo $options['process']['fastcheckoutbtn'] ?>" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('Define the label for the Add to Cart button.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td width="25%"><?php _e( 'Enable Fast Cart', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input class="checkbox" name="wcpgsk_settings[process][fastcart]" id="wcpgsk_fastcart" value="0" type="hidden">
									<input class="checkbox" name="wcpgsk_settings[process][fastcart]" id="wcpgsk_fastcart" value="1" type="checkbox" <?php if (  1 == ($options['process']['fastcart'])) echo "checked='checked'"; ?> type="checkbox">
								</td>
								<td>
									<span class="description"><?php _e('This option takes customers to cart after adding an item. Do not activate both, Fast Cart and Fast Checkout...', WCPGSK_DOMAIN); ?></span>
								</td>
								
							</tr>
							<tr>
								<td width="25%"><?php _e( 'Enable Fast Checkout', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input class="checkbox" name="wcpgsk_settings[process][fastcheckout]" id="wcpgsk_fastcheckout" value="0" type="hidden">
									<input class="checkbox" name="wcpgsk_settings[process][fastcheckout]" id="wcpgsk_fastcheckout" value="1" type="checkbox" <?php if (  1 == ($options['process']['fastcheckout'])) echo "checked='checked'"; ?> type="checkbox">
								</td>
								<td>
									<span class="description"><?php _e('This option takes customers to checkout after adding an item. Do not activate both, Fast Cart and Fast Checkout...', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td width="25%"><?php _e( 'Enable Payment Gateways Configuration', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input class="checkbox" name="wcpgsk_settings[process][paymentgateways]" id="wcpgsk_paymentgateways" value="0" type="hidden">
									<input class="checkbox" name="wcpgsk_settings[process][paymentgateways]" id="wcpgsk_paymentgateways" value="1" type="checkbox" <?php if (  1 == ($options['process']['paymentgateways'])) echo "checked='checked'"; ?> type="checkbox">
								</td>
								<td>
									<span class="description"><?php _e('This option allows you to configure the available payment gateways for each product.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<?php do_action( 'wcpgsk_settings_shop_after', $options ); ?>
						</tbody>
						</table>
						<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
					
					</div>
					
					<?php do_action( 'wcpgsk_settings_page_two', $options ); ?>

					<h3 class="wcpgsk_acc_header"><?php echo __('Cart Settings',WCPGSK_DOMAIN); ?></h3>
					<div>
						<table class="widefat" border="1" >
						<thead>
							<tr>
								<th><?php _e('Settings Name', WCPGSK_DOMAIN);  ?></th>
								<th width="50%"><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
								<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
							</tr>
						</thead>
						<tbody>
							<?php do_action( 'wcpgsk_settings_cart_before', $options ); ?>
							<tr>
								<td><?php _e('Minimum cart items', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][minitemscart]" type="text" value="<?php if (!empty($options['cart']['minitemscart'])) echo esc_attr( $options['cart']['minitemscart'] ); ?>" size="2" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php esc_attr(_e('You can specify the minimum of items allowed in woocommerce customer carts for wholesale purposes. If you leave this blank 1 product will be the default limit. Please be aware that you have to set the maximum to the same or a higher limit. This value will be automatically adjusted to assure store operations.', WCPGSK_DOMAIN)); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Maximum cart items', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][maxitemscart]" type="text" value="<?php if (!empty($options['cart']['maxitemscart'])) echo esc_attr( $options['cart']['maxitemscart'] ); ?>" size="2" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('You can specify the maximum of items allowed in woocommerce customer carts. If you leave this blank 3 products will be the default limit.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Treat variation items like individual (different) product items when counting items in cart', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][variationscountasproduct]" type="hidden" value="0" />
									<input name="wcpgsk_settings[cart][variationscountasproduct]" type="checkbox" value="1" <?php if (  1 == ($options['cart']['variationscountasproduct'])) echo "checked='checked'"; ?> />
								</td>
								<td>
									<span class="description"><?php _e('If you want to allow users to buy the maximum of variations allowed, even if the the product maximum is reached, do not check this option. Minimum handling takes this into account, too.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Switch off quantity input', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<?php
									$noquantity = __('No quantity input for', WCPGSK_DOMAIN);
									$quantity = __('Quantity input for', WCPGSK_DOMAIN);
									?>
									<select name="wcpgsk_settings[cart][variationproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['variationproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity . ' ' . __('Product Variation', WCPGSK_DOMAIN);?></option>	
										<option value="0" <?php if ($options['cart']['variationproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('Product Variation', WCPGSK_DOMAIN);?></option>
									</select><br />
									<select name="wcpgsk_settings[cart][variableproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['variableproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity . ' ' . __('Variable Product', WCPGSK_DOMAIN);?></option>	
										<option value="0" <?php if ($options['cart']['variableproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('Variable Product', WCPGSK_DOMAIN);?></option>
									</select><br />
									<select name="wcpgsk_settings[cart][groupedproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['groupedproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity  . ' ' . __('Grouped Product', WCPGSK_DOMAIN);?></option>	
										<option value="0" <?php if ($options['cart']['groupedproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('Grouped Product', WCPGSK_DOMAIN);?></option>
									</select><br />
									<select name="wcpgsk_settings[cart][externalproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['externalproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity . ' ' . __('External Product', WCPGSK_DOMAIN);?></option>
										<option value="0" <?php if ($options['cart']['externalproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('External Product', WCPGSK_DOMAIN);?></option>
									</select><br />
									
									<select name="wcpgsk_settings[cart][simpleproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['simpleproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity . ' ' . __('Simple Product', WCPGSK_DOMAIN);?></option>	
										<option value="0" <?php if ($options['cart']['simpleproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('Simple Product', WCPGSK_DOMAIN);?></option>
									</select>
								</td>
								<td>
									<span class="description"><?php _e('Switch off the quantity input field and set quantity automatically to one if a customer adds a product or product variation to his/her cart.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
						<?php
							$product_types = array('variation' => __('Set the minimum and maximum quantities allowed for each item that a customer can add to his/her cart', WCPGSK_DOMAIN),
								'variable' => __('Please, you should not confuse product quantity and the allowed maximum and minimum of individual items in a cart.', WCPGSK_DOMAIN),
								'grouped' => __('The min and max quantity values only have effect if you enable quantity input for a given product type.', WCPGSK_DOMAIN),
								'external' => __('To squizze out more of WooCommerce you may want to upgrade to WooCommerce Rich Guys Swiss Knife :-).', WCPGSK_DOMAIN),
								'simple' => __('Individual items can be personalized by Woocommerce Rich Guys Swiss Knife during checkout.', WCPGSK_DOMAIN));
							foreach($product_types as $type => $descr) :
						?>
							<tr>
								<td><?php _e('Min/Max quantity <strong>' . $type . ' products', WCPGSK_DOMAIN); ?></strong>:</td>
								<td>
									<input name="wcpgsk_settings[cart][minqty_<?php echo $type ; ?>]" type="text" value="<?php if (!empty($options['cart']['minqty_' . $type])) echo esc_attr( $options['cart']['minqty_' . $type] ); ?>" size="2" class="wcpgsk_textfield_short" /> |
									<input name="wcpgsk_settings[cart][maxqty_<?php echo $type ; ?>]" type="text" value="<?php if (!empty($options['cart']['maxqty_' . $type])) echo esc_attr( $options['cart']['maxqty_' . $type] ) ; ?>" size="2" class="wcpgsk_textfield_short" />
								</td>
								<td>
									<span class="description"><?php echo $descr ; ?></span>
								</td>
							</tr>
							
						<?php	
							endforeach;
						?>
						<?php do_action( 'wcpgsk_settings_cart_after', $options ); ?>

						</tbody>
						</table>
						<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
					</div>
					<?php do_action( 'wcpgsk_settings_page_four', $options ); ?>
						<h3 class="wcpgsk_acc_header"><?php echo __('Checkout Settings',WCPGSK_DOMAIN); ?></h3>
						<div>
						<table class="widefat" border="1" >
						<thead>
							<tr>
								<th><?php _e('Settings Name', WCPGSK_DOMAIN);  ?></th>
								<th><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
								<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
							</tr>
						</thead>
						<tbody>
							<?php do_action( 'wcpgsk_settings_checkout_before', $options ); ?>
							<tr>
								<td><?php _e('Min date offset for date fields', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][mindate]" type="text" value="<?php if (!empty($options['checkoutform']['mindate'])) echo esc_attr( $options['checkoutform']['mindate'] ); ?>" size="2" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('For date fields you can specify a minimum offset in number of days.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Max date offset for date fields', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][maxdate]" type="text" value="<?php if (!empty($options['checkoutform']['maxdate'])) echo esc_attr( $options['checkoutform']['maxdate'] ); ?>" size="3" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('For date fields you can specify a maximum offset in number of days.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
						
							<tr>
								<td><?php _e('Add billing email validator', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][billingemailvalidator]" type="hidden" value="0" />
									<input name="wcpgsk_settings[checkoutform][billingemailvalidator]" type="checkbox" value="1" <?php if (  1 == ($options['checkoutform']['billingemailvalidator'])) echo "checked='checked'"; ?> />
								</td>
								<td>
									<span class="description"><?php _e('If you want to oblige the user to input his/her email a second time and to assure that the email is valid, activate. This field will be added automatically.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<?php do_action('wcpgsk_settings_checkout_after', $options); ?>

						</tbody>
						</table>
						<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
					</div>
					<?php
					do_action( 'wcpgsk_settings_page_six', $options );
					$checkoutforms = array('billing' => 'Billing', 'shipping' => 'Shipping');
					$checkoutforms = apply_filters( 'wcpgsk_checkoutforms', $checkoutforms );
					foreach($checkoutforms as $section => $title) :
					?>
						<h3 class="wcpgsk_acc_header"><?php echo __('Woocommerce Checkout ' . $title . ' Section',WCPGSK_DOMAIN); ?></h3>
						<div>
							<table class="wcpgsk_fieldtable widefat" id="wcpgsk_<?php echo $section ;?>_table" border="1" >
							<thead>
								<tr>
									<th class="wcpgsk_replace"><?php _e('Order', WCPGSK_DOMAIN);  ?></th>
									<th><?php _e('Field Name', WCPGSK_DOMAIN);  ?></th>
									<th><?php _e('Remove Field', WCPGSK_DOMAIN);  ?></th>		
									<th><?php _e('Required Field', WCPGSK_DOMAIN);  ?></th>
									<th><?php _e('Show in Order', WCPGSK_DOMAIN);  ?></th>
									<th class="wcpgsk_replace"><?php _e('Label', WCPGSK_DOMAIN);  ?></th>
									<th class="wcpgsk_replace"><?php _e('Placeholder', WCPGSK_DOMAIN);  ?></th>
									<th class="wcpgsk_replace"><?php _e('Display', WCPGSK_DOMAIN);  ?></th>
									<th class="wcpgsk_replace"><?php _e('Type', WCPGSK_DOMAIN);  ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td></td>
									<td><input type="checkbox" class="select_removes" for="removes_<?php echo $section ;?>" id="select_remove_<?php echo $section ;?>" value="1" /> <?php _e('Select All', WCPGSK_DOMAIN);  ?></td>
									<td><input type="checkbox" class="select_required" for="required_<?php echo $section ;?>" id="select_required_<?php echo $section ;?>" value="1" /> <?php _e('Select All', WCPGSK_DOMAIN);  ?></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<?php 
								if ($section == 'order') :
									$checkout_fields = array();
									$checkout_fields = apply_filters( 'wcpgsk_order_checkout_fields', $checkout_fields, $options );
								elseif ($section == 'shared') :
									$checkout_fields = array();
									$checkout_fields = apply_filters( 'wcpgsk_shared_checkout_fields', $checkout_fields, $options );
								else:
									$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), $section . '_' );
									$field_order = 1;
									foreach ($checkout_fields as $key => $field) :
										
										$checkout_fields[$key]['placeholder'] = isset($checkout_fields[$key]['placeholder']) ? $checkout_fields[$key]['placeholder'] : '';
										$checkout_fields[$key]['label'] = isset($checkout_fields[$key]['label']) ? $checkout_fields[$key]['label'] : '';
										$checkout_fields[$key]['required'] = isset($checkout_fields[$key]['required']) ? : 0;
										
										$checkout_fields[$key]['fieldkey'] = $key;
										$checkout_fields[$key]['displaylabel'] = isset($options['woofields']['label_' . $key]) && !empty($field['label']) ? __($field['label'], WCPGSK_DOMAIN) : $key;
										$checkout_fields[$key]['order'] = ((isset($options['woofields']['order_' . $key]) && !empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
										$checkout_fields[$key]['placeholder'] = ((isset($options['woofields']['placeholder_' . $key]) && !empty($options['woofields']['placeholder_' . $key])) ? $options['woofields']['placeholder_' . $key] : $checkout_fields[$key]['placeholder']);
										$checkout_fields[$key]['label'] = ((isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key])) ? $options['woofields']['label_' . $key] : $checkout_fields[$key]['label']);
										//before required defreq
										$checkout_fields[$key]['defreq'] = ((isset($checkout_fields[$key]['required']) && $checkout_fields[$key]['required'] == 1) ? $defchecked : $defunchecked);
										$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
										$checkout_fields[$key]['showorder'] = ((isset($options['woofields']['showorder_' . $key])) ? $options['woofields']['showorder_' . $key] : 0);
										$checkout_fields[$key]['type'] = ((isset($options['woofields']['type_' . $key]) && !empty($options['woofields']['type_' . $key])) ? $options['woofields']['type_' . $key] : ((!empty($checkout_fields[$key]['type'])) ? $checkout_fields[$key]['type'] : 'text') );
										
										$checkout_fields[$key]['classsel'] = ((isset($options['woofields']['class_' . $key]) && !empty($options['woofields']['class_' . $key])) ? $options['woofields']['class_' . $key] : ((isset($checkout_fields[$key]['class'])) ? $checkout_fields[$key]['class'][0] : 'form-row-wide') );
										$checkout_fields[$key]['settings'] = ((isset($options['woofields']['settings_' . $key]) && !empty($options['woofields']['settings_' . $key])) ? $options['woofields']['settings_' . $key] : '' );
										$field_order++;
									endforeach;
								endif;
								$checkout_fields = apply_filters( 'wcpgsk_checkout_fields', $checkout_fields, $options );
								uasort( $checkout_fields, array($this, "compareFieldOrder") );						
								$field_order = 1;
								
								foreach ($checkout_fields as $key => $field) : 
									$fieldLabel = $field['displaylabel'];
									$options['woofields'][$section][$key]['custom_' . $key] = isset($options['woofields'][$section][$key]['custom_' . $key]) ? $options['woofields'][$section][$key]['custom_' . $key] : '';
								?>
									<tr class="wcpgsk_order_row">
										<td class="wcpgsk_order_col"><span class="ui-icon ui-icon-arrow-4"></span><span class="wcpgsk_order_span"><?php echo $field['order']; ?></span><input type="hidden"  class="wcpgsk_order_input" name="wcpgsk_settings[woofields][order_<?php echo $field['fieldkey']; ?>]" value="<?php echo $field['order']; ?>" /></td>
										<td>
											<?php
												if ($options['woofields'][$section][$key]['custom_' . $key] == $key && $key != 'order_comments') :
											?>
												<input name="wcpgsk_settings[woofields][<?php echo $section ;?>][<?php echo $key; ?>][custom_<?php echo $key; ?>]" type="hidden" value="<?php echo $key; ?>" />
												<button class="wcpgsk_remove_field" for="wcpgsk_<?php echo $section ;?>_table" name="wcpgsk_settings[woofields][<?php echo $section ;?>][<?php echo $key; ?>][fieldname_<?php echo $key; ?>]"><?php _e('X',WCPGSK_DOMAIN) ; ?></button> <?php echo $key; ?>
											<?php
												else :
													if ($section == 'order' && $key == 'order_comments') :
													?>
														<input name="wcpgsk_settings[woofields][<?php echo $section ;?>][<?php echo $key; ?>][custom_<?php echo $key; ?>]" type="hidden" value="<?php echo $key; ?>" />
													<?php
													endif;
													echo $fieldLabel; 
												endif;
											?>
										</td>
										<td><input name="wcpgsk_settings[woofields][remove_<?php echo $field['fieldkey']; ?>]" type="hidden" value="0" />
											<input name="wcpgsk_settings[woofields][remove_<?php echo $field['fieldkey']; ?>]" type="checkbox" class="removes_<?php echo $section ;?>" value="1" 
											<?php if (  1 == ($options['woofields']['remove_' . $field['fieldkey']]) ) echo 'checked="checked"'; ?>   /></td>
										<td><input name="wcpgsk_settings[woofields][required_<?php echo $field['fieldkey']; ?>]" type="hidden" value="0" />
											<input name="wcpgsk_settings[woofields][required_<?php echo $field['fieldkey']; ?>]" type="checkbox" class="required_<?php echo $section ;?>" value="1" <?php if (  1 == $field['required'] ) echo "checked='checked'"; ?> />
											<small> <?php echo $field['defreq']; ?></small>
										</td>
										<td><input name="wcpgsk_settings[woofields][showorder_<?php echo $field['fieldkey']; ?>]" type="hidden" value="0" />
											<input name="wcpgsk_settings[woofields][showorder_<?php echo $field['fieldkey']; ?>]" type="checkbox" class="showorder_<?php echo $section ;?>" value="1" <?php if (  1 == $field['showorder'] ) echo "checked='checked'"; ?> />
										</td>
										<td><input type="text" name="wcpgsk_settings[woofields][label_<?php echo $field['fieldkey']; ?>]" class="wcpgsk_textfield" 
											value="<?php echo esc_attr( $field['label'] ); ?>" /></td>
										<td><input type="text" name="wcpgsk_settings[woofields][placeholder_<?php echo $field['fieldkey']; ?>]" class="wcpgsk_textfield" 
											value="<?php echo esc_attr( $field['placeholder'] ); ?>" /></td>
										<td>
											<select name="wcpgsk_settings[woofields][class_<?php echo $field['fieldkey']; ?>]">
												<option value="form-row-wide" 
												<?php if (  'form-row-wide' == ($field['classsel']) ) echo 'selected="selected"'; ?>><?php echo $placeWide; ?></option>
												<option value="form-row-first" 
												<?php if (  'form-row-first' == ($field['classsel']) ) echo 'selected="selected"'; ?>><?php echo $placeFirst; ?></option>
												<option value="form-row-last" 
												<?php if (  'form-row-last' == ($field['classsel']) ) echo 'selected="selected"'; ?>><?php echo $placeLast; ?></option>
											</select>
										</td>
										<td class="wcpgsk_functions_col">
											<?php
												if ($options['woofields'][$section][$key]['custom_' . $key] == $key && $key != 'order_comments') :
											?>
												<button class="wcpgsk_configure_field" table="wcpgsk_<?php echo $section ;?>_table" for="<?php echo $key ; ?>" type="<?php echo $field['type'] ; ?>" name="wcpgsk_settings[woofields][button_<?php echo $key ; ?>]"><?php echo $field['type'] ; ?></button>
												<input name="wcpgsk_settings[woofields][type_<?php echo $key; ?>]" type="hidden" value="<?php echo $field['type'] ; ?>" />
												<input name="wcpgsk_settings[woofields][settings_<?php echo $key; ?>]" type="hidden" value="<?php echo $field['settings'] ; ?>" />
											<?php
												else :
													echo $field['type']; 
												endif;
											?>
										</td>
									</tr>
								<?php 
									$field_order++;
								endforeach; 
								$custom = 'nn2id';
								$newField = __('New Field', WCPGSK_DOMAIN);
								?>
								
								<tr valign="top" class="wcpgsk_add_field_row" id="wcpgsk_add_<?php echo $section ;?>_field_row">
										<td class="wcpgsk_order_col"><span class="ui-icon ui-icon-arrow-4"></span><span class="wcpgsk_order_span"><?php echo $custom; ?></span><input type="hidden"  class="wcpgsk_order_input" convert="wcpgsk_settings[woofields][order_<?php echo $custom ; ?>]" value="<?php echo $custom; ?>" /></td>
										<td class="wcpgsk_fieldname_col">
											<input convert="wcpgsk_settings[woofields][<?php echo $section ;?>][<?php echo $custom; ?>][custom_<?php echo $custom; ?>]" type="hidden" value="<?php echo $custom; ?>" />
											
											<button class="wcpgsk_remove_field" for="wcpgsk_<?php echo $section ;?>_table" convert="wcpgsk_settings[woofields][fieldname_<?php echo $custom; ?>]"><?php _e('X',WCPGSK_DOMAIN) ; ?></button> <span convert="wcpgsk_settings[woofields][ident_<?php echo $custom; ?>]"><?php echo $custom; ?></span>
										</td>
										<td><input convert="wcpgsk_settings[woofields][remove_<?php echo $custom; ?>]" type="hidden" value="0" />
											<input convert="wcpgsk_settings[woofields][remove_<?php echo $custom; ?>]" type="checkbox" class="removes_<?php echo $section ;?>" value="1" /></td>
										<td><input convert="wcpgsk_settings[woofields][required_<?php echo $custom; ?>]" type="hidden" value="0" />
											<input convert="wcpgsk_settings[woofields][required_<?php echo $custom; ?>]" type="checkbox" class="required_<?php echo $section ;?>" value="1" />
										</td>
										<td><input convert="wcpgsk_settings[woofields][showorder_<?php echo $custom; ?>]" type="hidden" value="0" />
											<input convert="wcpgsk_settings[woofields][showorder_<?php echo $custom; ?>]" type="checkbox" class="showorder_<?php echo $section ;?>" value="1" />
										</td>
										<td><input type="text" convert="wcpgsk_settings[woofields][label_<?php echo $custom; ?>]" class="wcpgsk_textfield" value="<?php echo $newField; ?>" /></td>
										<td><input type="text" convert="wcpgsk_settings[woofields][placeholder_<?php echo $custom; ?>]" class="wcpgsk_textfield" value="<?php echo $newField; ?>" /></td>
										<td>
											<select convert="wcpgsk_settings[woofields][class_<?php echo $custom; ?>]">
												<option value="form-row-wide"><?php echo $placeWide ?></option>
												<option value="form-row-first"><?php echo $placeFirst ?></option>
												<option value="form-row-last" ><?php echo $placeLast ?></option>
											</select>
										</td>
										<td class="wcpgsk_functions_col">
											<button convert="wcpgsk_settings[woofields][button_<?php echo $custom; ?>]"></button>
											<input convert="wcpgsk_settings[woofields][type_<?php echo $custom; ?>]" type="hidden" value="" />
											<input convert="wcpgsk_settings[woofields][settings_<?php echo $custom; ?>]" type="hidden" value="" />
										</td>
								   </tr>
							</tbody>
							</table>
							Select type: <select id="wcpgsk_<?php echo $section ;?>_table_type">
								<option value="text">text</option>
								<option value="textarea">textarea</option>
								<option value="date">date</option>
								<option value="time" >time</option>
								<option value="number" >number</option>
								<option value="select" >select</option>
							</select>

							Identifier New Field: <input type="text" id="wcpgsk_<?php echo $section ;?>_table_fieldid" value="" maxlength="25" size="12" /> <a href="javascript:;" class="add_custom_field button-primary" id="add_custom_<?php echo $section ;?>_btn" for="wcpgsk_<?php echo $section ;?>_table" placeholder="<?php echo $section ;?>"><?php _e( 'New ' . $title . ' Field' , WCPGSK_DOMAIN ); ?></a>
							<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
						</div>
					
					<?php
					endforeach;
					do_action( 'wcpgsk_settings_page_eight', $options );
					do_action( 'wcpgsk_settings_page_about', $options );
					?>

				</div>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" /></p>
			</form>
			
			<div id="wcpgsk_error_dialog" title="WC Poor Guys Swiss Knife Error">
				<p id="wcpgsk_error_message"></p>
			</div>
			
			<?php
				do_action( 'wcpgsk_settings_page_dialogs_one', $options );
				$validateTip = __('Required form fields are marked with *.', WCPGSK_DOMAIN);
				
			?>
			<div id="wcpgsk_dialog_form_container" title="<?php _e('Configure your custom field', WCPGSK_DOMAIN) ; ?>">

			</div>

			<div id="wcpgsk_dialog_form_select" title="Configure Select Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_select" accept-charset="utf-8">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option field_option_choices">
				<td class="label">
					<label><?php _e('Options', WCPGSK_DOMAIN) ; ?>*</label>
					<p><?php _e('Enter each option on a new line.', WCPGSK_DOMAIN) ; ?>
					<br /><?php _e('You can specify value and label for each option like this:', WCPGSK_DOMAIN) ; ?>
					</p>
					<p><strong>jazz : Charles Mingus<br>blues : John Lee Hooker</strong></p>
				</td>
				<td>
					<textarea rows="6" for="wcpgsk_add_options" class="textarea field_option-choices" autocomplete="off" defaultValue="" placeholder=""></textarea>	
				</td>
			</tr>
			<tr class="field_option field_option_select">
				<td class="label">
					<label><?php _e('Default Value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Specify default values, one per line.', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<textarea rows="3" for="wcpgsk_add_selected" class="textarea" defaultValue="" placeholder=""></textarea>	
				</td>
			</tr>
			<tr class="field_option field_option_select">
				<td class="label">
					<label><?php _e('Allow Null?', WCPGSK_DOMAIN) ; ?></label>
				</td>
				<td>
					<ul class="wcpgsk-radio-list radio horizontal"><li><label><input id="wcpgsk_add_allow_null_1" for="wcpgsk_add_allow_null" value="1" type="radio"><?php _e('Yes', WCPGSK_DOMAIN) ; ?></label></li><li><label><input id="wcpgsk_add_allow_null_0" for="wcpgsk_add_allow_null" value="0" checked="&quot;checked&quot;" data-checked="&quot;checked&quot;" type="radio"><?php _e('No', WCPGSK_DOMAIN) ; ?></label></li></ul>	
				</td>
			</tr>
			<tr class="field_option field_option_select">
				<td class="label">
					<label><?php _e('Select multiple values?', WCPGSK_DOMAIN) ; ?></label>
				</td>
				<td>
					<ul class="wcpgsk-radio-list radio horizontal"><li><label><input id="wcpgsk_add_multiple_1" for="wcpgsk_add_multiple" value="1" type="radio"><?php _e('Yes', WCPGSK_DOMAIN) ; ?></label></li><li><label><input id="wcpgsk_add_multiple_0" for="wcpgsk_add_multiple" value="0" checked="&quot;checked&quot;" data-checked="&quot;checked&quot;" type="radio"><?php _e('No', WCPGSK_DOMAIN) ; ?></label></li></ul>	
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Presentation', WCPGSK_DOMAIN) ; ?></label>
					
				</td>
				<td>
					<select for="wcpgsk_add_presentation">
						<option value="select"><?php _e('As select list', WCPGSK_DOMAIN) ; ?></option>
						<option value="radio"><?php _e('As radio buttons', WCPGSK_DOMAIN) ; ?></option>
						<option value="checkbox"><?php _e('As checkboxes', WCPGSK_DOMAIN) ; ?></option>
					</select>
				</td>
			</tr>


			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_text" title="Configure Text Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_text">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Maximum characters', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_maxlength" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Size', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_size" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Validation', WCPGSK_DOMAIN) ; ?></label>
					
				</td>
				<td>
					<select for="wcpgsk_add_validate">
						<option value="none"><?php _e('No validation', WCPGSK_DOMAIN) ; ?></option>
						<option value="email"><?php _e('Email validation', WCPGSK_DOMAIN) ; ?></option>				
						<option value="date"><?php _e('Date', WCPGSK_DOMAIN) ; ?></option>
						<option value="time"><?php _e('Time', WCPGSK_DOMAIN) ; ?></option>
						<option value="password"><?php _e('Password', WCPGSK_DOMAIN) ; ?></option>
						<option value="number"><?php _e('Number', WCPGSK_DOMAIN) ; ?></option>
						<option value="integer"><?php _e('Integer', WCPGSK_DOMAIN) ; ?></option>
						<option value="float"><?php _e('Float', WCPGSK_DOMAIN) ; ?></option>
						<option value="custom1"><?php _e('Custom1', WCPGSK_DOMAIN) ; ?></option>
						<option value="custom2"><?php _e('Custom2', WCPGSK_DOMAIN) ; ?></option>
						<option value="custom3"><?php _e('Custom3', WCPGSK_DOMAIN) ; ?></option>
					</select>
				</td>
			</tr>
			<tr class="field_option field_option_select">
				<td class="label">
					<label><?php _e('Add repeat input for validation, e.g. email or password fields?', WCPGSK_DOMAIN) ; ?></label>
				</td>
				<td>
					<ul class="wcpgsk-radio-list radio horizontal"><li><label><input id="wcpgsk_add_repeat_field_0" for="wcpgsk_add_repeat_field" value="0" type="radio" checked="&quot;checked&quot;" data-checked="&quot;checked&quot;" ><?php _e('No', WCPGSK_DOMAIN) ; ?></label></li><li><label><input id="wcpgsk_add_repeat_field_1" for="wcpgsk_add_repeat_field" value="1" type="radio"><?php _e('Yes', WCPGSK_DOMAIN) ; ?></label></li></ul>	
				</td>
			</tr>

			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_textarea" title="Configure Textarea Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_textarea">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Textarea rows', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_rows" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Textarea cols', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_cols" value="" />
				</td>
			</tr>
			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_date" title="Configure Date Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_date">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minimum offset in days', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_mindays" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Maximum offset in days', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_maxdays" value="" />
				</td>
			</tr>
			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_time" title="Configure Time Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_time">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minimum hour', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_minhour" value="" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Maximum hour', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_maxhour" value="" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Hour steps', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_hoursteps" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minute steps', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_minutesteps" value="" />
				</td>
			</tr>
			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_number" title="Configure Number Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_number">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Default value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_value" value="" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Default upper range value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_rangemax" value="" />
				</td>
			</tr>


			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minimum value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_minvalue" value="" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Maximum value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_maxvalue" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Number step', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_numstep" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Presentation', WCPGSK_DOMAIN) ; ?></label>
					
				</td>
				<td>
					<select for="wcpgsk_add_numpres">
						<option value="false"><?php _e('Default', WCPGSK_DOMAIN) ; ?></option>
						<option value="true"><?php _e('Range with minimum and maximum', WCPGSK_DOMAIN) ; ?></option>
						<option value="min"><?php _e('Range with minimum', WCPGSK_DOMAIN) ; ?></option>
						<option value="max"><?php _e('Range with maximum', WCPGSK_DOMAIN) ; ?></option>
					</select>
				</td>
			</tr>
			</table>
			</form>

			</div>
			
			
			<?php
			echo '</div>';
			
			
		}
		
		/**
		 * Helper function to order array.
		 *
		 * @access public
		 * @param array $a
		 * @param array $b
		 * @since 1.1.0
		 * @return $input (validated)
		 */		
		public function compareFieldOrder($a, $b) {
			if ($a['order'] == $b['order']) {
				return 0;
			}
			return ($a['order'] < $b['order']) ? -1 : 1;
		}		
		
		/**
		 * Our Validation for submitted Settings Page.
		 *
		 * @access public
		 * @since 1.1.0
		 * @return $input (validated)
		 */		
		public function wcpgsk_options_validate( $input ) {
			global $woocommerce;
			//$wcpgsk_options = get_option( 'wcpgsk_settings' );
			
			if (empty($input['cart']['minitemscart']) || !ctype_digit($input['cart']['minitemscart'])) $input['cart']['minitemscart'] = 1;
			if (empty($input['cart']['maxitemscart']) || !ctype_digit($input['cart']['maxitemscart'])) $input['cart']['maxitemscart'] = 3;

			$mincart = $input['cart']['minitemscart'];
			$maxcart = $input['cart']['maxitemscart'];
			if ($mincart > $maxcart) $input['cart']['minitemscart'] = 1;
			
			//@todo:could be a string, but not vital
			if (empty($input['checkoutform']['mindate']) || !ctype_digit($input['checkoutform']['mindate'])) $input['checkoutform']['mindate'] = 2;
			if (empty($input['checkoutform']['maxdate']) || !ctype_digit($input['checkoutform']['maxdate'])) $input['checkoutform']['maxdate'] = 365;
			
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'billing_' );
			$field_order = 1;	
			foreach ($checkout_fields as $key => $field) : 
				if (empty($input['woofields']['order_' . $key]) || !ctype_digit($input['woofields']['order_' . $key])) $input['woofields']['order_' . $key] = $field_order;
				$field_order++;
			endforeach;

			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' );
			$field_order = 1;	
			foreach ($checkout_fields as $key => $field) : 
				if (empty($input['woofields']['order_' . $key]) || !ctype_digit($input['woofields']['order_' . $key])) $input['woofields']['order_' . $key] = $field_order;
				$field_order++;
			endforeach;

			if ( isset( $input['process']['fastcart'] ) && $input['process']['fastcart'] == 1 && $input['process']['fastcheckout'] == 1) $input['process']['fastcheckout'] = 0;
			
			$product_types = array('variation', 'variable', 'grouped', 'external', 'simple');
			foreach($product_types as $type) :
				if (empty($input['cart']['maxqty_' . $type]) || !ctype_digit($input['cart']['maxqty_' . $type])) $input['cart']['maxqty_' . $type] = 1;
				if (empty($input['cart']['minqty_' . $type]) || !ctype_digit($input['cart']['minqty_' . $type])) $input['cart']['minqty_' . $type] = 1;
				//assure consistent settings
				//if ($input['cart']['maxqty_' . $type] == 0) $input['cart']['minqty_' . $type] = 0;
				//if ($input['cart']['minqty_' . $type] == 0) $input['cart']['maxqty_' . $type] = 0;		
				if ($input['cart']['minqty_' . $type] > $input['cart']['maxqty_' . $type]) $input['cart']['minqty_' . $type] = 1;
				//set quantity input field visibility to none for given type
				if ($input['cart']['maxqty_' . $type] == 1) $input['cart'][$type . 'productnoqty'] = 1;
			endforeach;
			$input = apply_filters('wcpgsk_validate_settings', $input);

			return $input;
		}
		
		/**
		 * Our filter to add billing fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		function add_billing_custom_fields( $fields ) {
			$options = get_option( 'wcpgsk_settings' );
			$options['woofields']['label_billing_email_validator'] = !empty($options['woofields']['label_billing_email_validator']) ? $options['woofields']['label_billing_email_validator'] : '';
			$options['woofields']['placehoder_billing_email_validator'] = !empty($options['woofields']['placehoder_billing_email_validator']) ? $options['woofields']['placehoder_billing_email_validator'] : '';
			$options['woofields']['required_billing_email_validator'] = isset($options['woofields']['required_billing_email_validator']) ? $options['woofields']['required_billing_email_validator'] : 1;
			if ($options['checkoutform']['billingemailvalidator'] == 1) {
				$fields['billing_email_validator'] = array(
					'type'				=> 'text',
					'label' 			=> __( $options['woofields']['label_billing_email_validator'], WCPGSK_DOMAIN ),
					'placeholder' 		=> __( $options['woofields']['placehoder_billing_email_validator'], WCPGSK_DOMAIN ),
					'required' 			=> (($options['woofields']['required_billing_email_validator'] == 1) ? true : false),
					//not necessary... 'validate'			=> 'email'
				);
			}
			
			if (isset($options['woofields']['billing']) && is_array($options['woofields']['billing'])) {
				foreach($options['woofields']['billing'] as $customkey => $customconfig) {
					//$fieldrepeater = null;
					$fields[$customkey] = $this->createCustomStandardField($customkey, 'billing', $options['woofields']['type_' . $customkey]);
				}
			}
			return $fields;
		}

		/**
		 * Our filter to add shipping fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		function add_shipping_custom_fields( $fields ) {
			$options = get_option( 'wcpgsk_settings' );
			
			if (isset($options['woofields']['shipping']) && is_array($options['woofields']['shipping'])) {
				foreach($options['woofields']['shipping'] as $customkey => $customconfig) {
					$fields[$customkey] = $this->createCustomStandardField($customkey, 'shipping', $options['woofields']['type_' . $customkey]);
				}
			}
			return $fields;
		}
		
		/**
		 * Our filter for billing fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		public function wcpgsk_admin_billing_fields($fields) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$billing_fields = array();
			$field_order = 1;	
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'billing_' );
			$defchecked = __('Default: checked', WCPGSK_DOMAIN);
			$defunchecked = __('Default: unchecked', WCPGSK_DOMAIN);

			foreach ($checkout_fields as $key => $field) :
				$checkout_fields[$key]['label'] = !empty($checkout_fields[$key]['label']) ? $checkout_fields[$key]['label'] : '';
				$checkout_fields[$key]['placeholder'] = !empty($checkout_fields[$key]['placeholder']) ? $checkout_fields[$key]['placeholder'] : '';
				$checkout_fields[$key]['fieldkey'] = $key;
				$checkout_fields[$key]['displaylabel'] = !empty($field['label']) ? __($field['label'], WCPGSK_DOMAIN) : $key;
				$checkout_fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
				$checkout_fields[$key]['placeholder'] = ((!empty($options['woofields']['placeholder_' . $key])) ? $options['woofields']['placeholder_' . $key] : $checkout_fields[$key]['placeholder']);
				$checkout_fields[$key]['label'] = ((!empty($options['woofields']['label_' . $key])) ? $options['woofields']['label_' . $key] : $checkout_fields[$key]['label']);
				//before required defreq
				$checkout_fields[$key]['defreq'] = ((isset($checkout_fields[$key]['required']) && $checkout_fields[$key]['required'] == 1) ? $defchecked : $defunchecked);
				$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
				$checkout_fields[$key]['showorder'] = ((isset($options['woofields']['showorder_' . $key])) ? $options['woofields']['showorder_' . $key] : 0);
				$checkout_fields[$key]['type'] = ((!empty($options['woofields']['type_' . $key])) ? $options['woofields']['type_' . $key] : ((!empty($checkout_fields[$key]['type'])) ? $checkout_fields[$key]['type'] : 'text') );
				$checkout_fields[$key]['classsel'] = ((!empty($options['woofields']['class_' . $key])) ? $options['woofields']['class_' . $key] : ((isset($checkout_fields[$key]['class'])) ? $checkout_fields[$key]['class'][0] : 'form-row-wide') );
				$checkout_fields[$key]['settings'] = ((!empty($options['woofields']['settings_' . $key])) ? $options['woofields']['settings_' . $key] : '' );
				$field_order++;
			endforeach;

			uasort($checkout_fields, array($this, "compareFieldOrder"));						

			foreach ($checkout_fields as $key => $field) : 
				//$fieldLabel = $field['displaylabel'];
				$fieldkey = str_replace('billing_', '', $key);
				if (isset($fields[$fieldkey])): 
					$billing_fields[$fieldkey] = $fields[$fieldkey];
				else:
					if ($key != 'billing_email_validator' && $field['showorder'] == 1) :
						if ($options['woofields']['billing'][$key]['custom_' . $key]) :
							$configField = $this->createCustomStandardField($key, 'billing', $options['woofields']['type_' . $key]);
							if (isset($configField['class'])) unset($configField['class']);
							if (isset($configField['clear'])) unset($configField['clear']);
							if (isset($configField['placeholder'])) unset($configField['placeholder']);
							if (isset($configField['required'])) unset($configField['required']);
							if (isset($configField['validate'])) unset($configField['validate']);
							if (isset($configField['custom_attributes'])) unset($configField['custom_attributes']);

							if (!isset($configField['label'])) $configField['label'] = __($checkout_fields[$key]['label'], WCPGSK_DOMAIN);
							
							//show select values as text in this case
							if ($configField['type'] == 'select') $configField['type'] = 'text';
							//textarea is not recognized by woocommerce in order billing address context
							if ($configField['type'] == 'textarea') $configField['type'] = 'text';
							if ($field['showorder'] == 1)
								$configField['show'] = true;
							else
								$configField['show'] = false;
								
							$billing_fields[$fieldkey] = $configField;
						endif;
					endif;
				endif;
				
			endforeach;
			return apply_filters( 'wcpgsk_admin_billing_fields', $billing_fields );	
		}

		/**
		 * Our filter for shipping fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		public function wcpgsk_admin_shipping_fields($fields) {
			global $woocommerce;
			$defchecked = __('Default: checked', WCPGSK_DOMAIN);
			$defunchecked = __('Default: unchecked', WCPGSK_DOMAIN);
			$options = get_option( 'wcpgsk_settings' );
			$shipping_fields = array();
			$field_order = 1;	
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' );
			$field_order = 1;

			foreach ($checkout_fields as $key => $field) :
				$checkout_fields[$key]['label'] = !empty($checkout_fields[$key]['label']) ? $checkout_fields[$key]['label'] : '';
				$checkout_fields[$key]['placeholder'] = !empty($checkout_fields[$key]['placeholder']) ? $checkout_fields[$key]['placeholder'] : '';

				$checkout_fields[$key]['fieldkey'] = $key;
				$checkout_fields[$key]['displaylabel'] = !empty($field['label']) ? __($field['label'], WCPGSK_DOMAIN) : $key;
				$checkout_fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
				$checkout_fields[$key]['placeholder'] = ((!empty($options['woofields']['placeholder_' . $key])) ? $options['woofields']['placeholder_' . $key] : $checkout_fields[$key]['placeholder']);
				$checkout_fields[$key]['label'] = ((!empty($options['woofields']['label_' . $key])) ? $options['woofields']['label_' . $key] : $checkout_fields[$key]['label']);
				//before required defreq
				$checkout_fields[$key]['defreq'] = ((isset($checkout_fields[$key]['required']) && $checkout_fields[$key]['required'] == 1) ? $defchecked : $defunchecked);
				$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
				$checkout_fields[$key]['showorder'] = ((isset($options['woofields']['showorder_' . $key])) ? $options['woofields']['showorder_' . $key] : 0);
				$checkout_fields[$key]['type'] = ((!empty($options['woofields']['type_' . $key])) ? $options['woofields']['type_' . $key] : ((!empty($checkout_fields[$key]['type'])) ? $checkout_fields[$key]['type'] : 'text') );
				
				$checkout_fields[$key]['classsel'] = ((!empty($options['woofields']['class_' . $key])) ? $options['woofields']['class_' . $key] : ((isset($checkout_fields[$key]['class'])) ? $checkout_fields[$key]['class'][0] : 'form-row-wide') );
				$checkout_fields[$key]['settings'] = ((!empty($options['woofields']['settings_' . $key])) ? $options['woofields']['settings_' . $key] : '' );
				$field_order++;
			endforeach;

			uasort($checkout_fields, array($this, "compareFieldOrder"));						

			foreach ($checkout_fields as $key => $field) : 
				//$fieldLabel = $field['displaylabel'];
				$fieldkey = str_replace('shipping_', '', $key);
				if (isset($fields[$fieldkey])): 
					$shipping_fields[$fieldkey] = $fields[$fieldkey];
				else:
					if ($key != 'shipping_email_validator') :
						if ($options['woofields']['shipping'][$key]['custom_' . $key]) :
							$configField = $this->createCustomStandardField($key, 'shipping', $options['woofields']['type_' . $key]);
							//unset(configField['placeholder']);
							if (isset($configField['class'])) unset($configField['class']);
							if (isset($configField['clear'])) unset($configField['clear']);
							if (isset($configField['placeholder'])) unset($configField['placeholder']);
							if (isset($configField['required'])) unset($configField['required']);
							if (isset($configField['validate'])) unset($configField['validate']);
							if (isset($configField['custom_attributes'])) unset($configField['custom_attributes']);

							if (!isset($configField['label'])) $configField['label'] = __($checkout_fields[$key]['label'], WCPGSK_DOMAIN);
							//show select values as text in this case
							if ($configField['type'] == 'select') $configField['type'] = 'text';
							//textarea is not recognized by woocommerce in order billing address context
							if ($configField['type'] == 'textarea') $configField['type'] = 'text';
							if ($field['showorder'] == 1)
								$configField['show'] = true;
							else
								$configField['show'] = false;
								
							
							$shipping_fields[$fieldkey] = $configField;
						endif;
					endif;
				endif;
				
			endforeach;
			return apply_filters( 'wcpgsk_admin_shipping_fields', $shipping_fields );	
		}

		/**
		 * Our filter for shipping fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		public function wcpgsk_checkout_fields_billing($fields) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			
			$field_order = 1;	
			
			$orderfields = array();
			$lastClass = array();
			foreach ($fields['billing'] as $key => $field) {
				if (isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) {
					unset($fields['billing'][$key]);
				}
				else {
				
					$orderfields[$key] = $fields['billing'][$key];

					$orderfields[$key]['label'] = !empty($options['woofields']['label_' . $key]) ? __($options['woofields']['label_' . $key], WCPGSK_DOMAIN) : '';
					//cosmetic stuff
					if (!empty($options['woofields']['class_' . $key])) {
						if (!empty($orderfields[$key]['class']) && is_array($orderfields[$key]['class']))
							$orderfields[$key]['class'][0] = $options['woofields']['class_' . $key];
						else
							$orderfields[$key]['class'] = array ($options['woofields']['class_' . $key]);
					}
					
					//set all our other data
					//woocommerce changed?
					//if ($options['woofields']['billing'][$key]['custom_' . $key])
					//	$orderfields[$key] = createCustomStandardField($key, 'billing', $options['woofields']['type_' . $key]);			
					if (isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] != 1) unset($orderfields[$key]['required']);
					//check if repeater field
				
					$orderfields[$key]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
					//set the order data

					
					if (empty($options['woofields']['order_' . $key]) || !ctype_digit($options['woofields']['order_' . $key])) {
						$orderfields[$key]['order'] = $field_order;
					}
					else {
						$orderfields[$key]['order'] = $options['woofields']['order_' . $key];
					}
					if ( isset($options['woofields']['settings_' . $key]) && $options['woofields']['settings_' . $key]) :
						$params = $this->explodeParameters($options['woofields']['settings_' . $key]);
						//if ( strpos($key, '_wcpgsk_repeater') !== false ) :
						//$testkey = str_replace('_wcpgsk_repeater', '', $key);
						//if ( !empty($options['woofields']['settings_' . $testkey]) ) :
						if ( is_array($params) && !empty($params) && isset($params['repeat_field']) && $params['repeat_field'] == '1' ) :
							$repkey = $key . '_wcpgsk_repeater';
							
							$orderfields[$repkey] = $this->createCustomStandardFieldClone($key, 'billing', $options['woofields']['type_' . $key]);

							//set all our other data
							//woocommerce changed?
							//if ($options['woofields']['billing'][$key]['custom_' . $key])
							//	$orderfields[$key] = createCustomStandardField($key, 'billing', $options['woofields']['type_' . $key]);			
							if (isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] != 1) unset($orderfields[$repkey]['required']);
							//check if repeater field
						
							$orderfields[$repkey]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
							//set the order data

							
							if (empty($options['woofields']['order_' . $key]) || !ctype_digit($options['woofields']['order_' . $key])) {
								$orderfields[$repkey]['order'] = $field_order + 0.5;
							}
							else {
								$orderfields[$repkey]['order'] = intval($options['woofields']['order_' . $key]) + 0.5;
							}
						endif;
					endif;
					unset($fields['billing'][$key]);
				}
				$field_order++;
			}
			//order the fields
			uasort($orderfields, array($this, "compareFieldOrder"));						
			
			//add the fields again
			foreach ($orderfields as $key => $field) {
				if ($key == 'order')
					unset($field['order']);
				else
					$fields['billing'][$key] = $field;
			}
			return $fields;
		}
		
		public function wcpgsk_checkout_fields_shipping($fields) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			
			$field_order = 1;	
			
			$orderfields = array();
			
			foreach ($fields['shipping'] as $key => $field) {
				if (isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) {
					unset($fields['shipping'][$key]);
				}
				else {
				
					$orderfields[$key] = $fields['shipping'][$key];

					$orderfields[$key]['label'] = !empty($options['woofields']['label_' . $key]) ? __($options['woofields']['label_' . $key], WCPGSK_DOMAIN) : '';
					//cosmetic stuff
					if (!empty($options['woofields']['class_' . $key])) {
						if (!empty($orderfields[$key]['class']) && is_array($orderfields[$key]['class']))
							$orderfields[$key]['class'][0] = $options['woofields']['class_' . $key];
						else
							$orderfields[$key]['class'] = array ($options['woofields']['class_' . $key]);
					}
					
					//set all our other data
					//woocommerce changed?
					//if ($options['woofields']['shipping'][$key]['custom_' . $key])
					//	$orderfields[$key] = createCustomStandardField($key, 'shipping', $options['woofields']['type_' . $key]);			
					if (isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] != 1) unset($orderfields[$key]['required']);
					//check if repeater field
				
					$orderfields[$key]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
					//set the order data

					
					if (empty($options['woofields']['order_' . $key]) || !ctype_digit($options['woofields']['order_' . $key])) {
						$orderfields[$key]['order'] = $field_order;
					}
					else {
						$orderfields[$key]['order'] = $options['woofields']['order_' . $key];
					}

					if ( isset($options['woofields']['settings_' . $key]) && $options['woofields']['settings_' . $key]) :
						$params = $this->explodeParameters($options['woofields']['settings_' . $key]);
						//if ( strpos($key, '_wcpgsk_repeater') !== false ) :
						//$testkey = str_replace('_wcpgsk_repeater', '', $key);
						//if ( !empty($options['woofields']['settings_' . $testkey]) ) :
						if ( is_array($params) && !empty($params) && isset($params['repeat_field']) && $params['repeat_field'] == '1' ) :
							$repkey = $key . '_wcpgsk_repeater';
							
							$orderfields[$repkey] = $this->createCustomStandardFieldClone($key, 'shipping', $options['woofields']['type_' . $key]);

							//set all our other data
							//woocommerce changed?
							//if ($options['woofields']['shipping'][$key]['custom_' . $key])
							//	$orderfields[$key] = createCustomStandardField($key, 'shipping', $options['woofields']['type_' . $key]);			
							if (isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] != 1) unset($orderfields[$repkey]['required']);
							//check if repeater field
						
							$orderfields[$repkey]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
							//set the order data

							
							if (empty($options['woofields']['order_' . $key]) || !ctype_digit($options['woofields']['order_' . $key])) {
								$orderfields[$repkey]['order'] = $field_order + 0.5;
							}
							else {
								$orderfields[$repkey]['order'] = intval($options['woofields']['order_' . $key]) + 0.5;
							}
						endif;
					endif;
					unset($fields['shipping'][$key]);
				}
				$field_order++;
			}
			//order the fields
			uasort($orderfields, array($this, "compareFieldOrder"));						
			
			//add the fields again
			foreach ($orderfields as $key => $field) {
				if ($key == 'order')
					unset($field['order']);
				else
					$fields['shipping'][$key] = $field;
			}
			return $fields;
		}
				
		public function wcpgsk_checkout_process() {
			global $woocommerce;
			global $wcpgsksession;
			$wcpgsksession->post = $_POST;
			$options = get_option( 'wcpgsk_settings' );
			
			if (isset($options['checkoutform']['billingemailvalidator']) && $options['checkoutform']['billingemailvalidator'] == 1) {
				if ($_POST[ 'billing_email' ] && $_POST[ 'billing_email_validator' ] && strtolower($_POST[ 'billing_email' ]) != strtolower($_POST[ 'billing_email_validator' ]))
					$woocommerce->add_error(  '<strong>' . __('Email addresses do not match', WCPGSK_DOMAIN) . ': ' . $_POST[ 'billing_email' ] . ' : ' . (empty($_POST[ 'billing_email_validator' ]) ? __('Missing validation email', WCPGSK_DOMAIN) : $_POST[ 'billing_email_validator' ]) . '</strong>');
				elseif ($_POST[ 'billing_email' ] && !$_POST[ 'billing_email_validator' ])
					$woocommerce->add_error(  '<strong>' . __('You have to supply a validation email for: ', WCPGSK_DOMAIN) . $_POST[ 'billing_email' ] . '</strong>');
			}
			
			$combine = array();
			foreach($_POST as $key => $val) {
				if ( strpos($key, '_wcpgsk_repeater') !== false ) :
					$testkey = str_replace('_wcpgsk_repeater', '', $key);
					if ( $_POST[$key] != $_POST[$testkey] ) :
						$woocommerce->add_error(  '<strong>' . sprintf(__('You have to validate the value <em style="color:red">%s</em> correctly! Please check your input.', WCPGSK_DOMAIN), $_POST[ $testkey ]) . '</strong>');
					
					endif;
					unset($_POST[$key]);
				
				elseif ( ( isset($options['woofields']['billing'][$key]['custom_' . $key]) && $options['woofields']['billing'][$key]['custom_' . $key] ) || ( isset( $options['woofields']['shipping'][$key]['custom_' . $key] ) && $options['woofields']['shipping'][$key]['custom_' . $key] ) ) :
					$combine[$key] = array();
					if (is_array($_POST[$key])) {
						foreach($_POST[$key] as $value){
							$combine[$key][] = esc_attr($value);
						}			
					}
					else $combine[$key][] = esc_attr($val);
				endif;
			}
			foreach($combine as $key => $val) {
				$_POST[$key] = implode('|', $val);
			}
		}
		
		
		public function createCustomStandardField($customkey, $context, $type) {
			$options = get_option( 'wcpgsk_settings' );
			$clear = false;
			$field = array();
			if (isset($options['woofields'][$context]) && is_array($options['woofields'][$context])) {
				$params = $this->explodeParameters($options['woofields']['settings_' . $customkey]);
				$custom_attributes = array();
				$seloptions = array();
				$selected = null;
				$clear = $options['woofields']['class_' . $customkey] == 'form-row-last' ? true : false;
				$validate = array();
				$display = '';
				$default = '';
				
				if (is_array($params) && !empty($params)) {
					foreach($params as $key => $value) {
						switch($key) {
							//does not make much sense as validation class is not really available in woocommerce
							//we put this as a parameter
							case 'validate':
								if ( $value && $value == 'password' ) :
									$type = 'password';
									$validate = array();
								else :
									$validate = array($value);
								endif;
								break;
							case 'options':
								
								foreach($value as $keyval => $option) {
									$seloptions[$keyval] = __($option, WCPGSK_DOMAIN);
								}
								break;
							case 'selected':
								if ( !empty($value) ) :
									foreach($value as $keyval => $option) {
										if (!empty($option) || $value == 0) $selected = $option;
									}
								endif;
								break;

							case 'multiple':
								if ($value == 1) $custom_attributes[$key] = 'multiple';
								break;

							case 'value':
								if (!empty($value) || $value == 0) $default = $value;
								break;
							
							case 'repeat_field':
								//not necessary here?
								break;
							default:
								if (!empty($value) || $value == 0)
									$custom_attributes[$key] = $value;
						}
					}
				}
				$options['woofields']['label_' . $customkey] = !empty($options['woofields']['label_' . $customkey]) ? $options['woofields']['label_' . $customkey] : '';
				$options['woofields']['placeholder_' . $customkey] = !empty($options['woofields']['placeholder_' . $customkey]) ? $options['woofields']['placeholder_' . $customkey] : '';
				$options['woofields']['required_' . $customkey] = isset($options['woofields']['required_' . $customkey]) && $options['woofields']['required_' . $customkey] == 1 ? 1 : 0; 
				switch($type) {
					case 'password':
						$custom_attributes['display'] = 'text';
						$field = array(
							'type'				=> 'password',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> (($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $options['woofields']['class_' . $customkey] ),
							'validate'			=> $validate,
							'clear'				=> $clear
						);
					break;

					case 'text':
						$custom_attributes['display'] = 'text';
						$field = array(
							'type'				=> 'text',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> (($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $options['woofields']['class_' . $customkey] ),
							'validate'			=> $validate,
							'clear'				=> $clear
						);
					break;
					
					case 'number':
						$custom_attributes['display'] = 'number';
						$field = array(
							'type'				=> 'text',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'default'			=> $default,
							'class' 			=> array( $options['woofields']['class_' . $customkey] ),
							'validate'			=> $validate,
							'clear'				=> $clear
						);
						break;
						
					case 'date':
						$custom_attributes['display'] = 'date';
						$field = array(
							'type'				=> 'text',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $options['woofields']['class_' . $customkey] ),
							'validate'			=> array('date'),
							'clear'				=> $clear
						);
						break;

						case 'time':
						$custom_attributes['display'] = 'time';

						$field = array(
							'type'				=> 'text',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $options['woofields']['class_' . $customkey] ),
							'validate'			=> array('time'),
							'clear'				=> $clear
						);
						break;

						case 'textarea':
						$custom_attributes['display'] = 'textarea';

						$field = array(
							'type'				=> 'textarea',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $options['woofields']['class_' . $customkey] ),
							'validate'			=> $validate,
							'clear'				=> $clear
						);
						break;

						case 'select':
						$custom_attributes['display'] = $display;
						$field = array(
							'type'				=> 'select',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'options' 			=> $seloptions,
							'default'			=> $selected,
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $options['woofields']['class_' . $customkey] ),
							'validate'			=> $validate,
							'clear'				=> $clear					
						);
						break;
				}
			}
			return $field;
		}

		public function createCustomStandardFieldClone($customkey, $context, $type) {
			$options = get_option( 'wcpgsk_settings' );
			$clear = false;
			$field = array();
			if (isset($options['woofields'][$context]) && is_array($options['woofields'][$context])) {
				$params = $this->explodeParameters($options['woofields']['settings_' . $customkey]);
				$custom_attributes = array();
				$seloptions = array();
				$selected = null;
				$clear = $options['woofields']['class_' . $customkey] == 'form-row-first' ? true : false;
				$validate = array();
				$display = '';
				$default = '';
				
				if (is_array($params) && !empty($params)) {
					foreach($params as $key => $value) {
						switch($key) {
							//does not make much sense as validation class is not really available in woocommerce
							//we put this as a parameter
							case 'validate':
								if ( $value && $value == 'password' ) :
									$type = 'password';
									$validate = array();
								else :
									$validate = array($value);
								endif;
								break;
							case 'options':
								
								foreach($value as $keyval => $option) {
									$seloptions[$keyval] = __($option, WCPGSK_DOMAIN);
								}
								break;
							case 'selected':
								if ( !empty($value) ) :
									foreach($value as $keyval => $option) {
										if (!empty($option) || $value == 0) $selected = $option;
									}
								endif;
								break;

							case 'multiple':
								if ($value == 1) $custom_attributes[$key] = 'multiple';
								break;

							case 'value':
								if (!empty($value) || $value == 0) $default = $value;
								break;
							
							default:
								if (!empty($value) || $value == 0)
									$custom_attributes[$key] = $value;
						}
					}
				}
				//$options['woofields']['label_' . $customkey] = !empty($options['woofields']['label_' . $customkey]) ? $options['woofields']['label_' . $customkey] : '';
				$options['woofields']['placeholder_' . $customkey] = !empty($options['woofields']['placeholder_' . $customkey]) ? $options['woofields']['placeholder_' . $customkey] : '';
				$options['woofields']['required_' . $customkey] = isset($options['woofields']['required_' . $customkey]) && $options['woofields']['required_' . $customkey] == 1 ? 1 : 0; 
				$clone_class = 'form-row-wide';
				if ($options['woofields']['class_' . $customkey] == 'form-row-first') $clone_class = 'form-row-last';
				$clone_label = __('Repeat value', WCPGSK_DOMAIN);
				
				switch($type) {
					case 'password':
						$custom_attributes['display'] = 'text';
						$field = array(
							'type'				=> 'password',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> (($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $clone_class ),
							'validate'			=> $validate,
							'clear'				=> $clear
						);
					break;
					case 'text':
						$custom_attributes['display'] = 'text';
						$field = array(
							'type'				=> 'text',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> (($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $clone_class ),
							'validate'			=> $validate,
							'clear'				=> $clear
						);
					break;
					
					case 'number':
						$custom_attributes['display'] = 'number';
						$field = array(
							'type'				=> 'text',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'default'			=> $default,
							'class' 			=> array( $clone_class ),
							'validate'			=> $validate,
							'clear'				=> $clear
						);
						break;
						
					case 'date':
						$custom_attributes['display'] = 'date';
						$field = array(
							'type'				=> 'text',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $clone_class ),
							'validate'			=> array('date'),
							'clear'				=> $clear
						);
						break;

						case 'time':
						$custom_attributes['display'] = 'time';

						$field = array(
							'type'				=> 'text',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $clone_class ),
							'validate'			=> array('time'),
							'clear'				=> $clear
						);
						break;

						case 'textarea':
						$custom_attributes['display'] = 'textarea';

						$field = array(
							'type'				=> 'textarea',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $clone_class ),
							'validate'			=> $validate,
							'clear'				=> $clear
						);
						break;

						case 'select':
						$custom_attributes['display'] = $display;
						$field = array(
							'type'				=> 'select',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'options' 			=> $seloptions,
							'default'			=> $selected,
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> array( $clone_class ),
							'validate'			=> $validate,
							'clear'				=> $clear					
						);
						break;
				}
			}
			return $field;
		}
		
		private function explodeParameters($settings) {
			$params = array();
			foreach (explode('&', $settings) as $chunk) {
				$param = explode("=", $chunk);

				if ($param) {
					$key =  str_replace('wcpgsk_add_',  '', urldecode($param[0]));
					if (!empty($key))
						$params[$key] = urldecode($param[1]);
					$new_choices = array();
					
					
					// explode choices from each line
					if( isset($params[$key]) && $params[$key] && (strpos($key, 'options') !== false || strpos($key, 'selected') !== false) )
					{
						// stripslashes ("")
						$params[$key] = stripslashes_deep($params[$key]);
					
						if(strpos($params[$key], "\n") !== false)
						{
							// found multiple lines, explode it
							$params[$key] = explode("\n", $params[$key]);
						}
						else
						{
							// no multiple lines! 
							$params[$key] = array($params[$key]);
						}
										
						// key => value
						foreach($params[$key] as $line)
						{
							if(strpos($line, ' : ') !== false)
							{
								$option = explode(' : ', $line);
								$new_choices[ trim($option[0]) ] = trim($option[1]);
							}
							else
							{
								$new_choices[ trim($line) ] = trim($line);
							}
						}
						// update options
						$params[$key] = $new_choices;
					}
				}
			}
			return $params;
		}

		private function explodeAttribute($param) {
			$params = array();

			if ($param) {
				
				if(strpos($param, "\n") !== false)
				{
					// found multiple lines, explode it
					$params[0] = explode("\n", $param);
				}
				else
				{
					// no multiple lines! 
					$params[0] = array($param);
				}
								
				// key => value
				foreach($params[0] as $line)
				{
					if(strpos($line, ' : ') !== false)
					{
						$option = explode(' : ', $line);
						$new_choices[ trim($option[0]) ] = trim($option[1]);
					}
					else
					{
						$new_choices[ trim($line) ] = trim($line);
					}
				}
				// update options
				$params[0] = $new_choices;
			}
			return $params;
		}
		
		
		/**
		 * Our admin menu and admin scripts
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function wcpgsk_admin_menu() {
			// Add a new submenu under Woocommerce:
			global $wcpgsk_name;
			$wcpgsk_name = apply_filters('wcpgsk_plus_name', $wcpgsk_name);
			add_submenu_page( 'woocommerce' , __( $wcpgsk_name, WCPGSK_DOMAIN ), __( $wcpgsk_name, WCPGSK_DOMAIN ), 'manage_options', WCPGSK_DOMAIN, array($this, 'wcpgsk__options_page') );
			add_action( 'admin_enqueue_scripts', array($this, 'wcpgsk_admin_scripts') );
		}
		
		/**
		 * Our admin scripts
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function wcpgsk_admin_scripts( $hook_suffix) {
			//echo( 'Hook suffix: ' . $hook_suffix);
			if ( $hook_suffix == 'woocommerce_page_wcpgsk' ) {

				if(!wp_script_is('jquery-ui-accordion', 'queue')){
						wp_enqueue_script('jquery-ui-accordion');
				}

				wp_enqueue_script( 'wcpgsk_admin', plugins_url( '/assets/js/wcpgsk_admin.js', $this->file ), '', '' );
				
				if(!wp_script_is('jquery-ui-sortable', 'queue')){
						wp_enqueue_script('jquery-ui-sortable');
				}
				if(!wp_script_is('jquery-ui-dialog', 'queue')){
						wp_enqueue_script('jquery-ui-dialog');
				}
				
				wp_register_script('accordion-js', plugins_url( '/assets/js/accordion.js', $this->file ), '', '', false);
				wp_register_style('accordion-styles', plugins_url( '/assets/css/accordion_styles.css', $this->file ), '', '');
				wp_register_style('wcpgsk-styles', plugins_url( '/assets/css/wcpgsk_styles.css', $this->file ), '', '');
		 
				wp_enqueue_script( 'accordion-js' );
				wp_enqueue_style( 'accordion-styles' );
				wp_enqueue_style( 'wcpgsk-styles' );
				// Include in admin_enqueue_scripts action hook
				wp_enqueue_media();
				wp_enqueue_script( 'custom-header' );		
				
			}
		}

		
		
		/**
		 * Run on activation.
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function activation () {
			$this->register_plugin_version();
			global $wcpgsk_options;
			//add_option( 'wcpgsk_settings', $wcpgsk_options );
			$this->wcpgsk_initial_settings();			
		} // End activation()

		/**
		 * Register the plugin's version.
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		private function register_plugin_version () {
			if ( $this->version != '' ) {
				update_option( WCPGSK_DOMAIN . '-version', $this->version );
			}
		} // End register_plugin_version()
		
		// Plugin links
		public function wcpgsk_admin_plugin_actions($links) {
			$wcpgsk_links = array(
				'<a href="admin.php?page=' . WCPGSK_DOMAIN . '">'.__('Settings').'</a>',
			);
			return array_merge( $wcpgsk_links, $links );
		}
		
		/**
		 * Initial settings for our plugin
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */		
		private function wcpgsk_initial_settings() {
			
			//@TODO check what we need in light version
			$defaults = array( 
				'wcpgsk_forms' => array( array(
					'label'  => __( 'Label', WCPGSK_DOMAIN ),
					'placeholder' => __( 'Placeholder', WCPGSK_DOMAIN ))),
				'cart' => array( 
					'minitemscart' => 1,
					'maxitemscart' => 3,
					'minvariationperproduct' => 1,
					'maxvariationperproduct' => 1,
					'maxqty_variation' => 0,
					'minqty_variation' => 0,
					'maxqty_variable' => 0,
					'minqty_variable' => 0,
					'maxqty_grouped' => 0,
					'minqty_grouped' => 0,
					'maxqty_external' => 0,
					'minqty_external' => 0,
					'maxqty_simple' => 0,
					'minqty_simple' => 0),
				'checkoutform' => array(
					'cartitemforms' => 1,
					'servicetitle' => __('Service data', WCPGSK_DOMAIN),
					'serviceformmerge' => 'woocommerce_before_order_notes',
					'sharedtitle' => __('Additional Information', WCPGSK_DOMAIN),
					'sharedformmerge' => 'woocommerce_after_checkout_billing_form',
					'tooltippersonalization' => '',
					'mindate' => 2,
					'maxdate' => 450,
					'enabletooltips' => 1,
					'enabletimesliders' => 1),
				'variations' => array(
					'extendattributes' => 1,
					'sortextendattributes' => 1),
				'process' => array(
					'fastcheckoutbtn' => '',
					'fastcart' => 0,
					'fastcheckout' => 0,
					'paymentgatways' => 0),
				);
			add_option( 'wcpgsk_settings', apply_filters( 'wcpgsk_defaults', $defaults ) );
		}
		
		/**
		 * Load the plugin's localisation file.
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function load_localisation () {
			load_plugin_textdomain( WCPGSK_DOMAIN, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
		} // End load_localisation()

		/**
		 * Load the plugin textdomain from the main WordPress "languages" folder.
		 * @since 1.1.0
		 * @return  void
		 */
		public function load_plugin_textdomain () {
			$domain = WCPGSK_DOMAIN;
			// The "plugin_locale" filter is also used in load_plugin_textdomain()
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
		} // End load_plugin_textdomain()
		
	}
}