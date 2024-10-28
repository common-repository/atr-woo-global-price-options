<?php

/**
* The admin-facing settings of the plugin. 
* Code based on https://github.com/pinoceniccola/WordPress-Plugin-Settings-API-Template
*
* @link       http://atarimtr.com
* @since      1.0.0
*
* @package    Atr_Woo_Gpo
* @subpackage Atr_Woo_Gpo/admin
*/

class Atr_Woo_Gpo_Admin_Settings {

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
	* The text domain of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $textdomain    The current version of this plugin.
	*/	
	private $textdomain;
	/*
	* Fired during plugins_loaded (very very early),
	* so don't miss-use this, only actions and filters,
	* current ones speak for themselves.
	*/
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->textdomain = 'atr-woo-global-price-options';		
	}
	/*
	* Loads both the general and advanced settings from
	* the database into their respective arrays. Uses
	* array_merge to merge with default values if they're
	* missing.
	*/
	/**
	* Initialise settings
	* @return void
	*/
	public function init() {
		$this->settings = $this->settings_fields();
		$this->options = $this->get_options(); 
		$this->register_settings();
	}

	/**
	* Add settings page to admin menu
	* @return void
	*/
	public function add_menu_item() {
		//$page = add_options_page( $this->plugin_name, $this->plugin_name, 'manage_options' , $this->plugin_name,  array( $this, 'settings_page' ) );
		
		$page = add_submenu_page( 'woocommerce', // The slug for this menu parent item
		__( 'ATR Woocommerce Global Price Options', $this->textdomain ), // The title to be displayed in the browser window for this page.
		__( 'ATR Woo GPO', $this->textdomain ),// The text to be displayed for this menu item
		'manage_options', // Which type of users can see this menu item
		$this->plugin_name,// The unique ID - that is, the slug - for this menu item
		array( $this, 'settings_page'));// The name of the function to call when rendering this menu's page	
		
	}

	/**
	* Add settings link to plugin list table
	* @param  array $links Existing links
	* @return array 		Modified links
	*/
	public function add_action_links( $links ) {
		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page='.$this->plugin_name) ) .'">' . __('Settings', $this->textdomain) . '</a>';
		$links[] = '<a href="http://atarimtr.com" target="_blank">More plugins by Yehuda Tiram</a>';		
		return $links;
	}


	/**
	* Build settings fields
	* @return array Fields to be displayed on settings page
	*/
	private function settings_fields() {
		$Settings_fields = new Atr_Woo_Gpo_Admin_Settings_fields( $this->get_plugin_name(), $this->get_version() );
		$populate_main_fields = $Settings_fields->create_price_list_fields();

		$settings['main'] = array(
		'title'					=> __( 'Manage your price list', $this->textdomain ),
		'description'			=> __( 'Please add option title and price in the textboxes.', $this->textdomain ),
		'fields'				=> $populate_main_fields
		);

		$settings['extra'] = array(
			'title'					=> __( 'Other options', $this->textdomain ),
			'description'			=> __( 'Select options. The settings you define here will apply only for the products in the categories you select in "Select categories" section below.', $this->textdomain ),
			'fields'				=> array(			
				array(
					'id' 			=> 'before_price_options',
					'label'			=> __( 'Header text for price options list', $this->textdomain ),
					'description'	=> __( 'Write the text to display before the price options list. <u><strong><span style="color:red;">Leave empty for none</span></strong></u>', $this->textdomain ),
					'type'			=> 'text',
					'default'		=> __('Please select an option to purchase this product.', $this->textdomain),
					'placeholder'	=> __('List header text', $this->textdomain),
				),				
				array(
					'id' 			=> 'after_price_options',
					'label'			=> __( 'Footer text for price options list', $this->textdomain ),
					'description'	=> __( 'Write the text to display after the price options list. <u><strong><span style="color:red;">Leave empty for none</span></strong></u>', $this->textdomain ),
					'type'			=> 'text',
					'default'		=> __('You can select more than one option to purchase the product. Just add it again with another option.', $this->textdomain),
					'placeholder'	=> __('List footer text', $this->textdomain),
				),	
				array(
					'id' 			=> 'add_to_cart_btn_txt',
					'label'			=> __( 'Text on "View details" product button', $this->textdomain ),
					'description'	=> __( 'Write the text to display on shop and archives "View details" button for the products.', $this->textdomain ),
					'type'			=> 'text',
					'default'		=> __('Select options', $this->textdomain),
					'placeholder'	=> __('"View details" text', $this->textdomain),
				),	
				array(
					'id' 			=> 'cart_item_option_label',
					'label'			=> __( 'Text label for selected option in the cart.', $this->textdomain ),
					'description'	=> __( 'Write the text to display on the label for the selected option <u>in the cart</u> ( This text is displayed after the customer adds to cart.).', $this->textdomain ),
					'type'			=> 'text',
					'default'		=> __('Your selection', $this->textdomain),
					'placeholder'	=> __('label for selected option in the cart', $this->textdomain),
				),					
				array(
					'id' 			=> 'make_product_empty_price_purchasable',
					'label'			=> __( 'Make products with empty price purchasable', $this->textdomain ),
					'description'	=> __( 'Force products with no price to be purchasable. If you select this you can save yourself the work of setting price to each product affected by the plugin. (You set the options price by the plugin.)', $this->textdomain ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),	
				array(
					'id' 			=> 'disable_quantity',
					'label'			=> __( 'Disable quantities', $this->textdomain ),
					'description'	=> __( 'Disable quantity selection. If you select this, the quantity select option will be removed from each product affected by the plugin. (The user can add only 1 item to the cart)', $this->textdomain ),
					'type'			=> 'checkbox',
					'default'		=> 'off'
				),				
				array(
					'id' 			=> 'categories_checkboxes',
					'label'			=> __( 'Select categories', $this->textdomain ),
					'description'	=> __( 'Select multiple product categories to apply the price options.', $this->textdomain ),
					'type'			=> 'categories_checkbox_multi_select',
					'options'		=> array( $this->get_product_categories()),
					'default'		=> array(  )
				),
				array(
					'id' 			=> 'separator_between_ttl_and_price',
					'label'			=> __( 'Text between option title and price digits.', $this->textdomain ),
					'description'	=> __( 'Write the text to display between option title and price digits. The default is \' - \'', $this->textdomain ),
					'type'			=> 'text',
					'default'		=> __(' - ', $this->textdomain),
					'placeholder'	=> __('title price separator', $this->textdomain),					
				),				
				array(
					'id' 			=> 'separator_between_price_and_currency_symbol',
					'label'			=> __( 'Text between option price digits and currency symbol.', $this->textdomain ),
					'description'	=> __( 'Write the text to display between option price digits and currency symbol. <br />(<strong>use &amp;nbsp; for space</strong>) The default is empty.', $this->textdomain ),
					'type'			=> 'text',
					'default'		=> __('', $this->textdomain),
					'placeholder'	=> __('price currency separator', $this->textdomain),					
				),
			)
		);		

		$settings = apply_filters( 'plugin_settings_fields', $settings );
		
		return $settings;
	}

	public function get_product_categories(){	
		
		if ( $this->check_wp_version( '4.5.0' ) ){
			$terms = get_terms( 'product_cat', array(
				'hide_empty' => false,
			) );			
		}
		else{
			$terms = get_terms( array(
				'taxonomy' => 'product_cat',
				'hide_empty' => false,
				'orderby' => 'term_group',
			) );				
		}
		return $terms;			
	}

	public function check_wp_version( $ver_num ){
		$wp_version = get_bloginfo('version');
		if ($wp_version < $ver_num) {
			return true;
		} else {
			return false;
		}		
	}

	/**
	* Options getter
	* @return array Options, either saved or default ones.
	*/
	public function get_options() {
		$options = get_option($this->plugin_name);
		if ( !$options && is_array( $this->settings ) ) {
			$options = Array();
			foreach( $this->settings as $section => $data ) {
				foreach( $data['fields'] as $field ) {
					$options[ $field['id'] ] = $field['default'];
				}
			}

			add_option( $this->plugin_name, $options );
		}

		return $options;
	}

	/**
	* Register plugin settings
	* @return void
	*/
	public function register_settings() {
		if( is_array( $this->settings ) ) {
			register_setting( $this->plugin_name, $this->plugin_name, array( $this, 'validate_fields' ) );
			
			foreach( $this->settings as $section => $data ) {
				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->plugin_name );

				foreach( $data['fields'] as $field ) {
					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), $this->plugin_name, $section, array( 'field' => $field ) );
				}
			}
			
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	* Generate HTML for displaying fields
	* @param  array $args Field data
	* @return void
	*/
	public function display_field( $args ) {
		
		$field = $args['field'];

		$html = '';

		$option_name = $this->plugin_name ."[". $field['id']. "]";

		$data = (isset($this->options[$field['id']])) ? $this->options[$field['id']] : '';

		switch( $field['type'] ) {

		case 'text':
			if ( array_key_exists ( 'gpo_type' , $field ) ){
				if ( $field['gpo_type'] == 'gpo_option_ttl'){
					$html .= '<div id="gpo-settings-title-' . esc_attr( $field['id'] ) . '" class="gpo-settings-title"><input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/></div>';
				}
				elseif( $field['gpo_type'] == 'gpo_option_price'){
					$remove_option = '<a href="javascript:void(0);" class="remove_button" title="Remove this option"><img title="Remove this option" alt="Remove this option" src="/wp-content/plugins/' . $this->plugin_name . '/public/css/remove_option.png"/></a>';
					$html .= '<div id="gpo-settings-price-' . esc_attr( $field['id'] ) . '" class="gpo-settings-price"><input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . $remove_option . '</div>';
				}	
				elseif ( $field['gpo_type'] == 'gpo_option_ttl2'){
					$html .= '<div id="gpo-settings-title-' . esc_attr( $field['id'] ) . '" class="gpo-settings-title"><input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/></div>';
				}
				elseif( $field['gpo_type'] == 'gpo_option_price2'){
					$remove_option = '<a href="javascript:void(0);" class="remove_button" title="Remove this option"><img title="Remove this option" alt="Remove this option" src="/wp-content/plugins/' . $this->plugin_name . '/public/css/remove_option.png"/></a>';
					$html .= '<div id="gpo-settings-price-' . esc_attr( $field['id'] ) . '" class="gpo-settings-price"><input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . $remove_option . '</div>';
				}					
				else {
					$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
				}				
			}
			else {
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			}
			break;			
		case 'password':
		case 'number':
			$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;

		case 'text_secret':
			$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;

		case 'textarea':
			$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . sanitize_textarea_field( $data ) . '</textarea><br/>'. "\n";
			break;

		case 'checkbox':
			$checked = '';
			if( $data && 'on' == $data ){
				$checked = 'checked="checked"';
			}
			$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;
			
		case 'categories_checkbox_multi_select':		
			foreach( $field['options'] as $k => $v ) {
				$html .= '<input type="text" id="atrCatSearchInput"  placeholder="' . __( 'Search for categories...', $this->textdomain ) .'" title="Type in a category name"><a href="javascript:void(0);" class="atr-cats-select-actions atr-expand-all-cats" title="Expand all categories">Expand all</a><a href="javascript:void(0);" class="atr-cats-select-actions atr-close_all-cats" title="Close all categories">Close all</a><a href="javascript:void(0);" class="atr-cats-select-actions atr-check-all-cats" title="Check all categories">Check all</a><a href="javascript:void(0);" class="atr-cats-select-actions atr-uncheck-cats" title="Uncheck all categories">Uncheck all</a>';
				$html .= '<ul class="gpo-cat-list">';
				foreach( $v as $term_obj => $term_prop ) {
					$checked = false;
					if( is_array($data) && in_array( $term_prop->term_id, $data ) ) {
						$checked = true;
					}
					$html .= '<li parent-id="' . $term_prop->parent . '" li-id="' . $term_prop->term_id . '"><label for="' . esc_attr( $field['id'] . '_' . $term_prop->name ) . '">';
					$html .= '<input class="categories-select-chkbox" type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $term_prop->term_id ) . '" id="' . esc_attr( $field['id'] . '_' . $term_prop->term_id ) . '" /> ';
					$html .= $term_prop->name . '</label></li>';					
				}
				$html .= '</ul>';

			}
			break;

		case 'checkbox_multi':
			foreach( $field['options'] as $k => $v ) {
				$checked = false;
				if( is_array($data) && in_array( $k, $data ) ) {
					$checked = true;
				}
				$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
			}
			break;

		case 'radio':
			foreach( $field['options'] as $k => $v ) {
				$checked = false;
				if( $k == $data ) {
					$checked = true;
				}
				$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
			}
			break;

		case 'select':
			if ( array_key_exists ( 'gpo_type' , $field ) ){
				if ( $field['gpo_type'] == 'gpo_list_control'){
					$html .= '<div id="gpo-settings-list-control-wrap" class="gpo-settings-title">';
					$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
					foreach( $field['options'] as $k => $v ) {
						$selected = false;
						if( $k == $data ) {
							$selected = true;
						}
						$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
					}
					$html .= '</select> ';					
					$html .= '</div>';
				}
			}	
			else{
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';				
			}
			break;

		case 'select_multi':
			$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
			foreach( $field['options'] as $k => $v ) {
				$selected = false;
				if( $data ){
					if( in_array( $k, $data ) ) {
						$selected = true;
					}					
				}				

				$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
			}
			$html .= '</select> ';
			break;

		}

		switch( $field['type'] ) {

		case 'checkbox_multi':
		case 'radio':
		case 'select_multi':
			$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

		default:
			$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}

		echo $html;
	}

	/**
	* Validate individual settings field
	* @param  string $data Inputted value
	* @return string       Validated value
	*/
	function validate_fields( $data ) { 
		if ( $data['add_to_cart_btn_txt'] != '' ) {
			$data['add_to_cart_btn_txt'] = sanitize_text_field($data['add_to_cart_btn_txt']);			
		}
		if ( $data['before_price_options'] != '' ) {
			$data['before_price_options'] = sanitize_text_field($data['before_price_options']);			
		}
		if ( $data['after_price_options'] != '' ) {
			$data['after_price_options'] = sanitize_text_field($data['after_price_options']);			
		}
		if ( $data['cart_item_option_label'] != '' ) {
			$data['cart_item_option_label'] = sanitize_text_field($data['cart_item_option_label']);			
		}		
		if ( $data['make_product_empty_price_purchasable'] != '' ) {
			$data['make_product_empty_price_purchasable'] = 'on';			
		}
		if ( $data['disable_quantity'] != '' ) {
			$data['disable_quantity'] = 'on';			
		}			
		if ( $data['separator_between_ttl_and_price'] != '' ) {
			$data['separator_between_ttl_and_price'] = sanitize_text_field($data['separator_between_ttl_and_price']);			
		}
		if ( $data['separator_between_price_and_currency_symbol'] != '' ) {
			$data['separator_between_price_and_currency_symbol'] = sanitize_text_field($data['separator_between_price_and_currency_symbol']);			
		}	

		if ( $data['list_control'] != '' ) {
			$valid_values = array( 'radio', 'ddl' );
			$value = sanitize_text_field( $data['list_control'] );
			if( in_array( $value, $valid_values ) ) {
				$data['list_control'] = $value;	
			}			
			else{
				add_settings_error( $this->plugin_name, esc_attr( 'Price_list_display_error' ) , __('Price list display format selection is not valid! Default radio button is used', $this->textdomain), 'error' );
				$data['list_control'] = 'radio';	
			}		
		}		
		//Sanitize and validate the price options rows
		foreach($_POST as $key => $val){
			if( $key === 'atr-woo-global-price-options' ){
				foreach( $val as $key1 => $val1){
					$original_val = $data[$key1]; // Field value before sanitization 
					$ttl_row = strpos($key1, 'p_0_ttl_'); // The option title
					$price_row = strpos($key1, 'p_0_price_'); // The option price
									
					if ( $ttl_row === 0 ){ // We check p_0_ttl_X
						$key1_index = str_replace('p_0_ttl_', '', $key1);
						$data[$key1] = sanitize_text_field($data[$key1]);
						$data[$key1] = str_replace( ';', ',', $data[$key1]);	// We use ; as a delimiter in \public\class-atr-woo-global-price-options-public.phpadd_cart_item_data()
						if ( $data[$key1] == '' ){
							add_settings_error( $this->plugin_name, esc_attr( 'title_not_entered' ) , __('No title was entered in field ' . $key1_index . '! Price in that row was also removed. (Faulty content might be removed by the plugin.)', $this->textdomain), 'error' );
							$data[$key1] = '';
							$price_key1 = str_replace('p_0_ttl_', 'p_0_price_', $key1);
							$data[$price_key1] = '';
							
							unset($data[$key1]);
							unset($data[$price_key1]);
							//return false;
						}							
					}
					elseif( $price_row === 0 ){ // We check p_0_price_X
						$key1_index = str_replace('p_0_price_', '', $key1);
						$data[$key1] = sanitize_text_field($data[$key1]);	
						if ( $data[$key1] == '' ){
							add_settings_error( $this->plugin_name, esc_attr( 'price_not_entered' ), __('No price was entered in field ' . $key1_index . '!. Title in that row was also removed. (Faulty content might be removed by the plugin.)', $this->textdomain), 'error' );
							$data[$key1] = '';
							$ttl_key1 = str_replace('p_0_price_', 'p_0_ttl_', $key1);
							$data[$ttl_key1] = '';
							
							unset($data[$key1]);
							unset($data[$ttl_key1]);							
						}
						else{
							$data[$key1] = filter_var($data[$key1],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
							$data[$key1] = wc_format_decimal( $data[$key1], '', false );
							if ( $original_val != $data[$key1] ) add_settings_error( $this->plugin_name, esc_attr( 'price_changed' ) , __('The price you entered in field ' . $key1_index . ' was changed on saving. Please review again. (Faulty content might be removed by the plugin.)', $this->textdomain), 'error' );
						}							
					}
				}					
			}	
		}				
		return $data;
	}

	public function display_gpo_settings_notices() {
		settings_errors( $this->plugin_name );
	}	

	/**
	* Load settings page content
	* @return void
	*/
	public function settings_page() {
		// Build page HTML output
		// If you don't need tabbed navigation just strip out everything between the <!-- Tab navigation --> tags.
		?>
		<div class="wrap gpo-settings-fields-wrap" id="<?php echo $this->plugin_name; ?>">
		<h2><?php _e('Price options for Woocommerce products', $this->textdomain); ?></h2>
		<p><?php _e('Settings.', $this->textdomain); ?></p>

		<!-- Tab navigation starts -->
		<h2 class="nav-tab-wrapper settings-tabs hide-if-no-js">
		<?php 
		foreach( $this->settings as $section => $data ) {
			echo '<a href="#' . $section . '" class="nav-tab">' . $data['title'] . '</a>';
		}
		?>
		</h2>
		<?php $this->do_script_for_tabbed_nav(); ?>
		<!-- Tab navigation ends -->

		<form action="options.php" method="POST">
		<?php settings_fields( $this->plugin_name ); ?>
		<div class="settings-container">
		<?php do_settings_sections( $this->plugin_name ); ?>
		</div>
		<?php submit_button(); ?>
		</form>
		</div>

		<div class="atr-imp-exp-wrapper metabox-holder">
			<div class="postbox">
				<h3><span><?php _e( 'Export your settings to file' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Export your settings as a .json file. This allows you to easily import them later here, or into another site.' ); ?></p>
					<form method="post">
						<p><input type="hidden" name="atr_gpo_imp_exp_action" value="export_settings" /></p>
						<p>
							<?php wp_nonce_field( 'atr_gpo_export_nonce', 'atr_gpo_export_nonce' ); ?>
							<?php submit_button( __( 'Export' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div>
			</div>

			<div class="postbox">
				<h3><span><?php _e( 'Import Settings' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Import your settings from a .json file. This file can be obtained by exporting the settings here, or on another site using the Export button above.' ); ?></p>
					<form method="post" enctype="multipart/form-data">
						<p>
							<input type="file" name="atr_gpo_import_file"/>
						</p>
						<p>
							<input type="hidden" name="atr_gpo_imp_exp_action" value="import_settings" />
							<?php wp_nonce_field( 'atr_gpo_import_nonce', 'atr_gpo_import_nonce' ); ?>
							<?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div>
			</div>
		</div>	
		<?php
	}

	/**
	 * Check if post exist  and not empty
	 */	
	public function check_isset_post( $field_name){		
		if (isset($_POST[$field_name]) && !empty($_POST[$field_name]) )
		{ return true; }
		else 
		{ return false; }	
	}

	/**
	 * Process a settings export that generates a .json file of the shop settings
	 */
	function atr_gpos_settings_export() {
		if( empty( $_POST['atr_gpo_imp_exp_action'] ) || 'export_settings' != $_POST['atr_gpo_imp_exp_action'] )
			return;
		if( ! wp_verify_nonce( $_POST['atr_gpo_export_nonce'], 'atr_gpo_export_nonce' ) )
			return;
		if( ! current_user_can( 'manage_options' ) )
			return;
		//$settings = get_option( 'atr_gpo_settings' );
		$settings = get_option( $this->plugin_name );
		ignore_user_abort( true );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=atr_gpo-settings-export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );
		echo json_encode( $settings );
		exit;
	}

	/**
	 * Process a settings import from a json file
	 */
	function atr_gpo_settings_import() {
		if( empty( $_POST['atr_gpo_imp_exp_action'] ) || 'import_settings' != $_POST['atr_gpo_imp_exp_action'] )
			return;
		if( ! wp_verify_nonce( $_POST['atr_gpo_import_nonce'], 'atr_gpo_import_nonce' ) )
			return;
		if( ! current_user_can( 'manage_options' ) )
			return;
		$import_file = $_FILES['atr_gpo_import_file']['tmp_name'];
		if( empty( $import_file ) ) {
			add_settings_error( $this->plugin_name, esc_attr( 'Import_settings_file_empty_error' ) , __('No file to import was selected. Please upload a valid .json file', $this->textdomain), 'error' );
			return false;
			//wp_die( __( 'Please upload a file to import', $this->textdomain ) );
		}
		$extension = '';
		if ( is_array( pathinfo($_FILES["atr_gpo_import_file"]["name"]) )){
			if ( array_key_exists( 'extension', pathinfo($_FILES["atr_gpo_import_file"]["name"]))){
				$extension = pathinfo($_FILES["atr_gpo_import_file"]["name"])['extension'];
			}	
			else{
				add_settings_error( $this->plugin_name, esc_attr( 'Import_settings_file_ext_not_json_error' ) , __('Please upload a valid .json file. (The file you tried to upload has no extension)', $this->textdomain), 'error' );
				return false;				
			}
		}
		
		if( $extension != 'json' ) {
			add_settings_error( $this->plugin_name, esc_attr( 'Import_settings_file_ext_not_json_error' ) , __('Please upload a valid .json file', $this->textdomain), 'error' );
			return false;	
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings = (array) json_decode( file_get_contents( $import_file ) );
		update_option( $this->plugin_name, $settings );
		wp_safe_redirect( admin_url( 'admin.php?page='.$this->plugin_name ) ); exit;
	}
	
	
	/**
	* Print jQuery script for tabbed navigation
	* @return void
	*/
	private function do_script_for_tabbed_nav() {
		//Tabbed navigation.
		?>
		<script>
		jQuery(document).ready(function($) {
			var headings = jQuery('.settings-container > h2, .settings-container > h3');
			var paragraphs  = jQuery('.settings-container > p');
			var tables = jQuery('.settings-container > .section-tab-content');
			var triggers = jQuery('.settings-tabs a');

			triggers.each(function(i){
				triggers.eq(i).on('click', function(e){
					e.preventDefault();
					triggers.removeClass('nav-tab-active');
					headings.hide();
					paragraphs.hide();
					tables.hide();

					triggers.eq(i).addClass('nav-tab-active');
					headings.eq(i).show();
					paragraphs.eq(i).show();
					tables.eq(i).show();
				});
			})

			triggers.eq(0).click();
		});
		</script>
		<?php
	}	

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Atr_Woo_Gpo_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}	
}		
