<?php

/**
* The public-facing functionality of the plugin.
*
* @link       http://www.atarimtr.com/
* @since      1.0.0
*
* @package    Atr_Woo_Gpo
* @subpackage Atr_Woo_Gpo/public
*/

/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and hooks for 
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    Atr_Woo_Gpo
* @subpackage Atr_Woo_Gpo/public
* @author     Yehuda Tiram <yehuda@atarimtr.co.il>
*/
class Atr_Woo_Gpo_Public {

	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $plugin_name    The ID of this plugin.
	*/
	private $plugin_name;

	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;
	
	/**
	* The categories affected by this plugin.
	*
	* @since    1.0.1
	* @access   private
	* @var      array    $version    The categories affected by this plugin.
	*/
	private $cats_to_act_on;	

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $plugin_name       The name of the plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->cats_to_act_on = $this->activate_price_options_for_categories();

		
	}
	
	public function activate_price_options_for_categories(){
		$arr_cats = [];
		$options = get_option($this->plugin_name);		
		if ( $options ) {
			foreach ($options as $key => $val) {
				if (is_array($val) && ($key == 'categories_checkboxes') && ( ! empty( $val )) ){
					foreach ($val as $key_inner => $val_inner) {
						$arr_cats[] = $val_inner; 
					}
				}
			}				
		}
		
		return $arr_cats;
	}

	public function make_product_purchasable( $purchasable, $product ){
		$options = get_option($this->plugin_name);
		if ( $options ) {
			if ( array_key_exists('make_product_empty_price_purchasable', $options) ){				
				if ($options['make_product_empty_price_purchasable'] == 'on'){
					//$cats_to_act_on = $this->activate_price_options_for_categories();
					if ( $product ){
						if (( has_term( $this->cats_to_act_on, 'product_cat', $product->get_id() ) ) && ( $this->cats_to_act_on ) ){
							$p_price = $product->get_price();
							if( empty($p_price) || $p_price == null || $p_price === '' ) $purchasable = true;										
						}			
					}					
				} 

			}
		}		

		return $purchasable;
	}	
	
	public function add_usage_field_single_product(  ) {
		global $product;
		//$cats_to_act_on = $this->activate_price_options_for_categories();
		if ( $product ){
			if( has_term( $this->cats_to_act_on, 'product_cat', $product->get_id() ) && ( $this->cats_to_act_on ) ){	
				if( $product->is_purchasable() ){
					if( has_term( $this->cats_to_act_on, 'product_cat', $product->get_id() ) && ( $this->cats_to_act_on ) ){
						
						$this->add_usage_field();
					}									
				}									
			}
		}
	}
	
	
	/*  
		Add a radio button field in woocommerce product page, so that customer can choose one of the price options.
		Add options to single product and change the price accordingly (in the cart, not in the page)
	*/
    public function add_usage_field() {
            $options = get_option($this->plugin_name);
			$price_options = $this->get_price_options_list();
			$separator_between_ttl_and_price = $options['separator_between_ttl_and_price'] ? $options['separator_between_ttl_and_price'] : ' - ';
			$separator_between_price_and_currency_symbol = $options['separator_between_price_and_currency_symbol'] ? $options['separator_between_price_and_currency_symbol'] : '';
            if ( $price_options ){
				echo apply_filters( 'atr_gpo_before_price_options', $this->before_price_options() );
				
				if ( $options ) {
					if ( array_key_exists('list_control', $options) ){				
						if ($options['list_control'] == 'ddl'){
							echo '<select name="uselist">';
										foreach ($price_options as $ttl => $price) {
											$translated_ttl = __( $ttl ,  'atr-woo-global-price-options'); // Use the plugin translation files. Add the English title and give it the appropriate translation for any language yuo use in your site.
											echo '<option value="' . $ttl . ';' . $price . '">' . $translated_ttl . $separator_between_ttl_and_price . $price . $separator_between_price_and_currency_symbol . get_woocommerce_currency_symbol() . '</option>';
										}
							echo '</select>';					
						} 
						else{ //  ( $options['list_control'] == 'radio' )
							$gpo_rb_price_options_list = '';
							foreach ($price_options as $ttl => $price) {
									$gpo_rb_price_options_list .= apply_filters( 'atr_gpo_before_price_options_radio_item', $this->before_price_options_item() );
									$translated_ttl = __( $ttl ,  'atr-woo-global-price-options'); // Use the plugin translation files. Add the English title and give it the appropriate translation for any language yuo use in your site.
									$gpo_rb_price_options_list .= '<input type="radio" name="uselist" value="' . $ttl . ';' . $price . '" id="' . $ttl . '" ><label for="' . $ttl . '">' . $translated_ttl . $separator_between_ttl_and_price .  $price . $separator_between_price_and_currency_symbol . get_woocommerce_currency_symbol() . '</label>';
									$gpo_rb_price_options_list .= apply_filters( 'atr_gpo_after_price_options_radio_item', $this->after_price_options_item() );
							}
							echo $gpo_rb_price_options_list; 							
						}

					}
					else{
						$gpo_rb_price_options_list = '';
						foreach ($price_options as $ttl => $price) {
								$gpo_rb_price_options_list .= apply_filters( 'atr_gpo_before_price_options_radio_item', $this->before_price_options_item() );
								$translated_ttl = __( $ttl ,  'atr-woo-global-price-options'); // Use the plugin translation files. Add the English title and give it the appropriate translation for any language yuo use in your site.
								$gpo_rb_price_options_list .= '<input type="radio" name="uselist" value="' . $ttl . ';' . $price . '" id="' . $ttl . '" ><label for="' . $ttl . '">' . $translated_ttl . $separator_between_ttl_and_price .  $price . $separator_between_price_and_currency_symbol . get_woocommerce_currency_symbol() . '</label>';
								$gpo_rb_price_options_list .= apply_filters( 'atr_gpo_after_price_options_radio_item', $this->after_price_options_item() );
						}
						echo $gpo_rb_price_options_list; 							
					}
				}				
				echo apply_filters( 'atr_gpo_after_price_options', $this->after_price_options() ); 
            }
            else{
                    if ( is_admin() ){
                            echo __('<p style="color:red;">ATR Woo GPO<br />You must set the prices list (Or deactivate ATR Woo GPO plugin). Please go to plugin settings and set you prices list first.<br /><a href="'. esc_url( get_admin_url(null, 'admin.php?page='.$this->plugin_name) ) .'">Go to settings</a></h3>', 'atr-woo-global-price-options');
                    }
                   
            }


    }

	public function before_price_options(){
		$price_options_head = '';
		$options = get_option($this->plugin_name);		
		if ( $options ) {
			if ( array_key_exists('before_price_options', $options) ){
				if ($options['before_price_options'] != ''){
					$price_options_head = '<div class="atr-price-options-wrap"><p class="gpo-before-price-options">' . $options['before_price_options'] . '</p>';
				}
				else{
					$price_options_head = '<div class="atr-price-options-wrap">';
				}
			}
			if ( array_key_exists('list_control', $options) ){
				if ($options['list_control'] != 'ddl'){
					$price_options_head .= '<ul class="atr-price-options-list">';
				}					
			}			
		}	
		return $price_options_head;		
	}

	public function before_price_options_item(){
		$price_option_before_item = '<li>';
		return $price_option_before_item;		
	}
	
	public function after_price_options_item(){
		$price_option_after_item = '</li>';
		return $price_option_after_item;			
	}	
	
	public function after_price_options(){
		$price_options_footer = '';
		$options = get_option($this->plugin_name);
		if ( $options ) {
			if ( array_key_exists('list_control', $options) ){
				if ($options['list_control'] != 'ddl'){
					$price_options_footer .= '</ul>';
				}					
			}			
			if ( array_key_exists('after_price_options', $options) ){
				if ($options['after_price_options'] != ''){
					$price_options_footer .= '<p class="gpo-after-price-options">' . $options['after_price_options'] . '</p></div>';
				}
				else{
					$price_options_footer .= '</div>';
				}
			}
		}		
		return $price_options_footer;			
	}		
	
	
	private function check_if_selected_option_in_options_list( $selected_p, $selected_ttl){
		$valid_price_options = $this->get_price_options_list();
		if (isset($valid_price_options[$selected_ttl]) && $valid_price_options[$selected_ttl] == $selected_p) { // option & Price are OK
			return true;
		}
		return false;				
	}
	
	/**
	* Display engraving text in the cart.
	*
	* @param array $item_data
	* @param array $cart_item
	*
	* @return array
	*/
	public function display_use_ttl_in_cart( $item_data, $cart_item ) {
		if ( empty( $cart_item['uselist_ttl'] ) ) {
			return $item_data;
		}
		
		$item_label = '';
		$options = get_option($this->plugin_name);
		if ( $options ) {
			if( $options['cart_item_option_label'] ){
				$item_label = __($options['cart_item_option_label'], 'atr-woo-global-price-options') ;
			}	
			else{
				$item_label = __('Your selection', 'atr-woo-global-price-options') ;
			}					
		}
		
		$item_data[] = array(
		'key'     => $item_label,
		'value'   => wc_clean( $cart_item['uselist_ttl'] ),
		'display' => '',
		);

		return $item_data;
	}

	/**
	 * Add  cart_item_option_label for product to order.
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string                $cart_item_key
	 * @param array                 $values
	 * @param WC_Order              $order
	 */
	public function add_cart_item_option_label_to_order_items( $item, $cart_item_key, $values, $order ) {
		if ( empty( $values['uselist_ttl'] ) ) {
			return;
		}
		$item_label = '';
		$options = get_option($this->plugin_name);
		if ( $options ) {
			if( $options['cart_item_option_label'] ){
				$item_label = __($options['cart_item_option_label'], 'atr-woo-global-price-options') ;
			}
			else{
				// If no item label defined in setting, show space. Otherwise it does not show the uselist_ttl at all.
				$item_label = '&nbsp;';
			}						
		}

		$item->add_meta_data( $item_label , $values['uselist_ttl']  );
	}	
	
	public function add_to_cart_item_validation( $true, $product_id ) {	

		if (( has_term( $this->cats_to_act_on, 'product_cat', $product_id ) ) && ( $this->cats_to_act_on ) ){
			$passed_cart_item_data = $this->add_cart_item_data();
			$passed = $passed_cart_item_data;			
			if ( $passed != false ){
				add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data'), 10, 3 );
			}
			else{
				if (empty( $_POST['uselist'])){
					wc_add_notice( __( 'Sorry, you did not select any option. Please select an option and then add to cart. (246)', 'atr-woo-global-price-options' ), 'error' );			
				}
				else{
					wc_add_notice( __( 'Sorry, we were unable to add this item to your cart. Please contact the site administrator (249)', 'atr-woo-global-price-options' ), 'error' );			
				}					
			}

			return $passed;												
		}
		else{
			return true;
		}

	}

	public function add_cart_item_data(  ) {
		if( $this->check_isset_post('uselist') ) {
			$selected_use = sanitize_text_field($_POST['uselist']);
			$item_price_ttl_arr = explode(';',$selected_use);	
			if ( count( $item_price_ttl_arr ) === 2 ){ // Check if there are only title & price in the post and nothing else
				if ( $item_price_ttl_arr[1] && $item_price_ttl_arr[0]  ){ 
					$cart_item_data['uselist_price'] = $item_price_ttl_arr[1];
					
					/* translators: Should be added to and treated in the plugin's .po file  In order to translate  */
					$cart_item_data['uselist_ttl'] = __( $item_price_ttl_arr[0], 'atr-woo-global-price-options' );	

					$check_uselist_price = $cart_item_data['uselist_price'];
					$check_uselist_ttl = $cart_item_data['uselist_ttl'];
					
					// Check that the selected price and title are identical to an option on the defined settings  
					$check_against_options_list = $this->check_if_selected_option_in_options_list( $check_uselist_price, $check_uselist_ttl );
					if ( $check_against_options_list ) {
						return $cart_item_data;
					}	
					else {
						$cart_item_data = false;
						return false;						
					}					
					
				}	
				else{

				}				
			}
			else{
				$cart_item_data = false;
				return false;
			}	
		}
		else{
			// nothing selected by user
			$cart_item_data = false;
			return false;
		}
		
	}	
	
	/* Set the price in cart	*/
	public function before_calculate_totals( $cart_obj ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		// Iterate through each cart item
		foreach( $cart_obj->get_cart() as $key=>$value ) {
			if( isset( $value['uselist_price'] ) ) {
				$price = $value['uselist_price'];
				$value['data']->set_price( ( $price ) );
			}
		}
	}
	
	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/atr-woo-global-price-options-public.css', array(), $this->version, 'all' );
	}

	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/atr-woo-global-price-options-public.js', array( 'jquery' ), $this->version, false );
	}
	/**@ Remove quantity_fields in all product type*/
	public function atr_remove_all_quantity_fields( $return, $product ) {
		$options = get_option($this->plugin_name);
		if ( $options ) {
			if ( array_key_exists('disable_quantity', $options) ){				
				if ($options['disable_quantity'] == 'on'){
					if( has_term( $this->cats_to_act_on, 'product_cat', $product->get_id() ) && ( $this->cats_to_act_on ) ){		
						return true; // remove quantity fields 
					}					
				} 
				else{
					return false; // ignore
				}
			}
			else{
				return false; // ignore
			}			
		}
		else{
			return false; // ignore
		}		
		
	}
	
	/* Remove add to cart button on product archive (shop) */
	// add_action( 'woocommerce_after_shop_loop_item', 'remove_add_to_cart_buttons', 1 );	
	public function replace_loop_add_to_cart_button( $button, $product  ) {
		// Only for simple products
		//$cats_to_act_on = $this->activate_price_options_for_categories();
		$button_text = __( 'Select options', "woocommerce" );
		$options = get_option($this->plugin_name);
		if ( $options ) {
			if( $options['add_to_cart_btn_txt'] ){
				$button_text = __($options['add_to_cart_btn_txt'], 'atr-woo-global-price-options') ;
			}						
		}
		
		if( has_term( $this->cats_to_act_on, 'product_cat', $product->get_id() ) && ( $this->cats_to_act_on ) ){		
			$button = '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';
			//$this->hide_loop_price(   );
		}
		else{
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10, 2 );
		}
		
		return $button;
	}
	
	
	public function hide_loop_price(   ) {		
		add_filter( 'woocommerce_get_price_html', [$this, 'woocommerce_remove_prices'], 10, 2 );
	}
		
	/* Get price_options_list option */
	private function get_price_options_list(){
		$options = get_option($this->plugin_name);
		if ( $options ) {
			$price_options = array();
			$price_options_ttl = array();
			$price_options_price = array();
			foreach ($options as $key => $value) {
				$ttl_row = strpos($key, 'p_0_ttl_');
				$price_row = strpos($key, 'p_0_price_');
				if ( ($ttl_row === 0) || ($price_row === 0) ){
					$pieces = explode("_", $key);
					if ( $pieces[2] == 'ttl' ) $price_options_ttl[$pieces[3]] = $value;
					if ( $pieces[2] == 'price' ) $price_options_price[$pieces[3]] = $value;						
				}
			}	
			foreach ($price_options_ttl as $key => $value) {
				if ( $price_options_price[$key] && $price_options_ttl[$key])
				$price_options[$value] = $price_options_price[$key];
			}
			return $price_options;	
		}
	}


	public function woocommerce_remove_prices($price, $product){
		
		//$cats_to_act_on = $this->activate_price_options_for_categories();	
		if( has_term( $this->cats_to_act_on, 'product_cat', $product->get_id() ) && ( $this->cats_to_act_on ) ){	
			$price = '';
		}		
		
		return $price;		
	}
	
	public function check_isset_post( $field_name){		
		if (isset($_POST[$field_name]) && !empty($_POST[$field_name]) )
		{ return true; }
		else 
		{ return false; }	
	}		
	
}
