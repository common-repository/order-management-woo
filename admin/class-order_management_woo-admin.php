<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ahsandev.com/
 * @since      1.0.0
 *
 * @package    order_management_woo
 * @subpackage order_management_woo/admin
 * @author     Ahsan Khan <ahsandev.creative@gmail.com>
 */
class order_management_woo_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	private $pages;
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->pages = array(
			'order_management_woo',
			'order_management_woo_products',
			'order_management_woo_products_per_order',
			'order_management_woo_order_per_product'
		);

	}
	public function admin_menu(){
		$name = 'Order Management for WooCommerce';
		add_menu_page(
			$name,
			'Order Management',
			'manage_options',
			$this->plugin_name,
			array($this,'display_orders'),
			'dashicons-chart-pie',
			5
		); 
		// Override the first submenu page to have a different title
        add_submenu_page(
            $this->plugin_name,
            $name.' >  Orders Summary ',
            'Orders Summary',
            'manage_options', 
            $this->plugin_name,
            array($this, 'display_orders'),
            1
        );
		add_submenu_page(
			$this->plugin_name,
			$name.' >  Categories Sold',
			'Categories Sold',
			'manage_options', 
			$this->plugin_name.'_products', 
			array($this,'display_products'),
			1
		);
		add_submenu_page(
			$this->plugin_name,
			$name.' >  Products Sold',
			'Products Sold',
			'manage_options', 
			$this->plugin_name.'_products_per_order', 
			array($this,'display_products_per_order'),
			2
		);
		add_submenu_page(
			$this->plugin_name,
			$name.' >  Products Sold Per Order',
			'Products Sold Per Order',
			'manage_options', 
			$this->plugin_name.'_order_per_product', 
			array($this,'display_order_per_product'),
			3
		);
	}
	private function validate_date($date, $format = 'Y-m-d') {
	    // Create the format date
	    $d = DateTime::createFromFormat($format, $date);

	    // Return the comparison    
	    return $d && $d->format($format) === $date;
	}
    private function wp_query_loop(){
        $statuses = wc_get_order_statuses();

        $data = array();
        $args = array();
        if(isset($_REQUEST['nonce'])) {
            if(!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), 'wom')){
                die('Security check fail');
            }else{
                if(isset($_POST) && !empty($_POST)){
                    if(isset($_POST['date']) && !empty($_POST['date'])){
                    	$statuses_selected = array_keys($statuses);
                        if(isset($_POST['order_status']) && !empty($_POST['order_status'])){
                        	if(is_array($_POST['order_status'])){
                        		$statuses_selected = array_map('sanitize_text_field',$_POST['order_status']);
                        	}
                        }

                        $args['post_type'] = array('shop_order');

                        $date_start = '';
                        $date_end 	= '';

                        $date_start_before_validate = sanitize_text_field($_POST['date']);
                        if($this->validate_date($date_start_before_validate)) {
                            $date_start = $date_start_before_validate;
                        	$date_end 	= $date_start_before_validate;
                        }
                        
                        if(isset($_POST['date_end']) && !empty($_POST['date_end'])){
                            $date_end_before_validate = sanitize_text_field($_POST['date_end']);
                            if($this->validate_date($date_end_before_validate)) {
                            	 $date_end 	= $date_end_before_validate;
                            }
                        }

                        if(!empty($statuses_selected)){
                            $args['post_status'] = $statuses_selected;
                        }

                        if(!empty($date_start) && !empty($date_end)){
                        	// if dates are set we will query all orders in that range, so -1 posts_per_page set
                        	$args['posts_per_page'] = -1;
	                        $args['date_query'] = array(
	                            array(
	                                'after' => $date_start,
	                                'before' => $date_end,
	                                'inclusive' => true,
	                            )
	                        );
	                    }else{
	                    	// If dates are empty or invalid we will query for only 10 orders, so 10 posts_per_page set
	                    	$args['posts_per_page'] = 10;
	                    }
	                    
                        $data = get_posts($args);

                        wp_reset_postdata();
                    }
                }
		        if(isset($_POST['p_cats']) && !empty($_POST['p_cats'])){
		            if(is_array($_POST['p_cats'])){
		                $slugs = array_map('sanitize_text_field',$_POST['p_cats']);
		                $args['slugs'] = $slugs;
		            }
		        }
		        if(isset($_POST['product_ids']) && !empty($_POST['product_ids'])){
	                if(is_array($_POST['product_ids'])){
	                    $product_ids = array_map('sanitize_text_field',$_POST['product_ids']);
	                    if(!empty($product_ids) && is_array($product_ids)){

	                    	$product_ids = array_map('intval', array_filter($product_ids, function($id) {
							    return !empty($id);
							}));

			                $args_products = array(
			                    'post_type'   		=> 'product',
			                    'posts_per_page'    => -1,
			                    'orderby'    		=> 'name',
			                    'order'      		=> 'asc',
			                    'post__in'          => $product_ids
			                );
			                $p_products = get_posts($args_products);

			                wp_reset_postdata();

			                $args['p_products']  = $p_products;
			                $args['product_ids'] = $product_ids;
			            }
		            }
	            }
		        if(isset($_POST['gbc']) && !empty($_POST['gbc'])){
		            $gbc = "checked";
		            $args['gbc'] = $gbc;
		        }
		        if(isset($_POST['show_deleted']) && !empty($_POST['show_deleted'])){
	                $show_deleted = 'checked';
	                $args['show_deleted'] = $show_deleted;
	            }
            }
        }

        return [$data,$args,$statuses];
    }
	public function display_orders(){
		global $woocommerce;

		include_once( 'partials/header.php');

		$loop_data =  $this->wp_query_loop();
		$orders =  $loop_data[0];
		$args   =  $loop_data[1];
		$order_statuses  =  $loop_data[2];

		include_once( 'partials/display_orders.php');
		include_once( 'partials/footer.php');

	}
	public function display_products(){
		global $woocommerce;

		include_once( 'partials/header.php');

		$orders 		= array();
		$p_array 		= array();
		$p_cats 	    = array();

        $loop_data =  $this->wp_query_loop();
		$orders =  $loop_data[0];
		$args   =  $loop_data[1];
		$order_statuses  =  $loop_data[2];

		$totalOrders = 0;
		if(!empty($orders)){
		    foreach($orders as $orderObj){
		    	$order_id = $orderObj->ID;
		    	$order = wc_get_order($order_id);
			    $order_number = $order->get_order_number();
			    $totalOrders = $totalOrders + 1;
			    $refunds = $order->get_refunds();
				$has_refunds = (bool) $refunds;
			    $items = $order->get_items();

			    if(!empty($items)){
			        foreach($items as $item_id => $item){

			            $product_id   = $item->get_product_id();
			            $variation_id = $item->get_variation_id(); // The variation ID
			            if(!empty($variation_id) && $variation_id > 0){
			            	$product_id  = $variation_id;
			            }
			            $sku 		  = '';
			            $url 		  = '';
			            $_product = wc_get_product($product_id);
			            if($_product){
			            	$sku = $_product->get_sku();
			            	$url = get_permalink($product_id);
			            }

			            $product_type = $item->get_type();
					    $quantity 	  = $item->get_quantity();
					    $subtotal 	  = $item->get_subtotal();
					    $total 		  = $item->get_total();
					    $name 	  	  = $item->get_name();

					    $refund_amount  = 0;
						$refund_total   = 0;
						if(true === $has_refunds){
							$refund_qty = absint($order->get_qty_refunded_for_item($item_id));
							$quantity   = max(0, $quantity - $refund_qty);
							$refund_amount = abs($order->get_total_refunded_for_item($item_id));
							if($refund_amount > 0){
								$refund_total = max( 0, $total - $refund_amount);
								$total = $refund_total;
							}
						}

						if($quantity > 0 ){

						    $product_terms_obj = get_the_terms( $product_id, 'product_cat' );
						    $product_cats = array();
						    $product_cats_s = array();
						    if(!empty($product_terms_obj)){
						    	foreach ($product_terms_obj as $key => $product_term_obj) {
						    		$product_cats_name = $product_term_obj->name;
						    		$product_cats_slug = $product_term_obj->slug;
						    		$product_cats[] = $product_cats_name;
						    		$product_cats_s[] = $product_cats_slug;
						    	}
						    }

				            if(!empty($variation_id) && $variation_id > 0){
				            	$product_id = $variation_id;
				            }
				            if(!isset($p_array[$product_id])){
				            	$p_array[$product_id] = array();
				            }
				            if(!isset($p_array[$product_id]['quantity'])){
				            	$p_array[$product_id]['quantity'] = 0;
				            }
				            if(!isset($p_array[$product_id]['subtotal'])){
				            	$p_array[$product_id]['subtotal'] = 0;
				            }
				            if(!isset($p_array[$product_id]['total'])){
				            	$p_array[$product_id]['total'] = 0;
				            }
				            if(!isset($p_array[$product_id]['orders'])){
				            	$p_array[$product_id]['orders'] = array();
				            }
				            if(!isset($p_array[$product_id]['p_cats'])){
				            	$p_array[$product_id]['p_cats'] = array();
				            }
				            if(!isset($p_array[$product_id]['p_cats_s'])){
				            	$p_array[$product_id]['p_cats_s'] = array();
				            }

				            $p_array[$product_id]['quantity'] = $p_array[$product_id]['quantity'] + $quantity;
				            $p_array[$product_id]['subtotal'] = $p_array[$product_id]['subtotal'] + $subtotal;
				            $p_array[$product_id]['total'] 	= $p_array[$product_id]['total'] + $total;
				            $p_array[$product_id]['name'] 	= $name;
				            $p_array[$product_id]['sku'] 	= $sku;
				            $p_array[$product_id]['url'] 	= $url;
				            $p_array[$product_id]['p_cats'] = $product_cats;
				            $p_array[$product_id]['p_cats_s'] = $product_cats_s;

				            if(!in_array($order_number, $p_array[$product_id]['orders'])){
					            $p_array[$product_id]['orders'][][$order_number] = $quantity;
					        }
					    }
			        }
			    }
		    }
		}
		include_once( 'partials/products.php');
		include_once( 'partials/footer.php');
	}
    public function display_products_per_order(){
        global $woocommerce;
        include_once( 'partials/header.php');

        $orders 	 = array();
        $p_array 	 = array();
        $p_cats 	 = array();
        $product_ids = array();

        $loop_data =  $this->wp_query_loop();
		$orders =  $loop_data[0];
		$args   =  $loop_data[1];
		$order_statuses  =  $loop_data[2];

		if(isset($args) && !empty($args)){
		    if(isset($args['product_ids']) && !empty($args['product_ids'])){
		        $product_ids = $args['product_ids'];
		    }
		}

        $totalOrders = 0;
        $shippingAddress = false;
        if(!empty($orders)){
            foreach($orders as $orderObj){
                $order_id = $orderObj->ID;
                $order = wc_get_order( $orderObj->ID);
                $order_number 	= $order->get_order_number();
                $checkZone = false;
                $totalOrders = $totalOrders + 1;
                $c_name  		= $order->get_shipping_first_name().' '.$order->get_shipping_last_name();
                $c_email 		= $order->get_billing_email();
                $c_phone 		= $order->get_billing_phone();
                $c_postcode 	= $order->get_shipping_postcode();
                $c_address 		= $order->get_shipping_address_1().' '.$order->get_shipping_address_2().', '.$order->get_shipping_city().', '.$order->get_shipping_state().', '.$c_postcode;
                $refunds = $order->get_refunds();
                $has_refunds = (bool) $refunds;

                $items = $order->get_items();
                foreach($items as $item_id => $item){

                    $product_id = $item->get_product_id();

                    if(in_array($product_id, $product_ids) || empty($product_ids)){

                        $variation_id = $item->get_variation_id(); // The variation ID

                        if(!empty($variation_id) && $variation_id > 0){
                            $product_id  = $variation_id;
                        }
                        $productDeletedFromOrder = false;
                        if($product_id == 0 || empty($product_id)){
                            $productDeletedFromOrder = true;
                        }
                        $sku 		  = '';
                        $url 		  = '';
                        $_product = wc_get_product($product_id);
                        if($_product){
                            $sku = $_product->get_sku();
                            $url = get_permalink($product_id);
                        }

                        $product_type = $item->get_type(); // The order item type
                        $quantity 	  = $item->get_quantity(); // Line item quantity
                        $subtotal 	  = $item->get_subtotal(); // Line item subtotal
                        $total 		  = $item->get_total(); // Line item total
                        $name 	  	  = $item->get_name(); // Line item total

                        $refund_amount  = 0;
                        $refund_total   = 0;
                        if(true === $has_refunds){
                            $refund_qty = absint($order->get_qty_refunded_for_item($item_id));
                            $quantity   = max(0, $quantity - $refund_qty);
                            $refund_amount = abs($order->get_total_refunded_for_item($item_id));
                            if($refund_amount > 0){
                                $refund_total = wc_price(max( 0, $total - $refund_amount));
                                $total = $refund_total;
                            }
                        }

                        if($quantity > 0 || $productDeletedFromOrder == true){

                            $product_terms_obj = get_the_terms( $product_id, 'product_cat' );
                            $product_cats = array();
                            $product_cats_s = array();
                            if(!empty($product_terms_obj)){
                                foreach ($product_terms_obj as $key => $product_term_obj) {
                                    $product_cats_name = $product_term_obj->name;
                                    $product_cats_slug = $product_term_obj->slug;
                                    $product_cats[] = $product_cats_name;
                                    $product_cats_s[] = $product_cats_slug;
                                }
                            }

                            if(!empty($variation_id) && $variation_id > 0){
                                $product_id = $variation_id;
                            }
                            if(!isset($p_array[$order_number])){
                                $p_array[$order_number] = array();
                            }
                            if(!isset($p_array[$order_number][$product_id])){
                                $p_array[$order_number][$product_id] = array();
                            }
                            if(!isset($p_array[$order_number][$product_id]['quantity'])){
                                $p_array[$order_number][$product_id]['quantity'] = 0;
                            }
                            if(!isset($p_array[$order_number][$product_id]['total'])){
                                $p_array[$order_number][$product_id]['total'] = 0;
                            }
                            if(!isset($p_array[$order_number][$product_id]['orders'])){
                                $p_array[$order_number][$product_id]['orders'] = array();
                            }
                            if(!isset($p_array[$order_number][$product_id]['p_cats'])){
                                $p_array[$order_number][$product_id]['p_cats'] = array();
                            }
                            if(!isset($p_array[$order_number][$product_id]['p_cats_s'])){
                                $p_array[$order_number][$product_id]['p_cats_s'] = array();
                            }
                            $p_array[$order_number][$product_id]['quantity'] = $quantity;
                            $p_array[$order_number][$product_id]['total']    = $total;
                            $p_array[$order_number][$product_id]['name'] 	   = $name;
                            $p_array[$order_number][$product_id]['sku'] 	   = $sku;
                            $p_array[$order_number][$product_id]['url'] 	   = $url;
                            $p_array[$order_number][$product_id]['p_cats']   = $product_cats;
                            $p_array[$order_number][$product_id]['p_cats_s'] = $product_cats_s;
                            $p_array[$order_number][$product_id]['order_id'] = $order_id;
                            $p_array[$order_number][$product_id]['created_at'] = $order->get_date_created()->format ('Y-m-d');
                            $p_array[$order_number][$product_id]['c_name']  		= $c_name;
                            $p_array[$order_number][$product_id]['c_email'] 		= $c_email;
                            $p_array[$order_number][$product_id]['c_phone']  		= $c_phone;
                            $p_array[$order_number][$product_id]['c_postcode']  	= $c_postcode;
                            $p_array[$order_number][$product_id]['c_address']  	= $c_address;
                            $p_array[$order_number][$product_id]['deleted']  		= $productDeletedFromOrder;
                        }
                    }
                }
            }
        }
        include_once( 'partials/products_per_order.php');
        include_once( 'partials/footer.php');
    }
    public function display_order_per_product(){
        global $woocommerce;
        include_once( 'partials/header.php');

        $orders  = array();
        $p_array = array();
        $oPPA 	 = array(); //order Per ProductArray
        $gbcA 	 = array(); //order Per ProductArray Group By Cats
        $p_cats  = array();

        $gbc = '';

        $loop_data 	=  $this->wp_query_loop();
		$orders =  $loop_data[0];
		$args   =  $loop_data[1];
		$order_statuses  =  $loop_data[2];

		if(isset($args) && !empty($args)){
		    if(isset($args['gbc']) && !empty($args['gbc'])){
		        $gbc = $args['gbc'];
		    }
		}

        $totalOrders = 0;
        if(!empty($orders)){
            foreach($orders as $orderObj){
                $order_id = $orderObj->ID;
                $order = wc_get_order( $orderObj->ID);
                $order_number 	= $order->get_order_number();
                $totalOrders = $totalOrders + 1;
                $refunds = $order->get_refunds();
                $has_refunds = (bool) $refunds;
                $items = $order->get_items();
                if(!empty($items)){
	                foreach($items as $item_id => $item){

	                    $product_id   = $item->get_product_id();
	                    $variation_id = $item->get_variation_id(); // The variation ID

	                    if(!empty($variation_id) && $variation_id > 0){
	                        $product_id  = $variation_id;
	                    }
	                    $sku 		  = '';
	                    $url 		  = '';
	                    $_product = wc_get_product($product_id);
	                    if($_product){
	                        $sku = $_product->get_sku();
	                        $url = get_permalink($product_id);
	                    }
	                    $product_type = $item->get_type(); // The order item type
	                    $quantity 	  = $item->get_quantity(); // Line item quantity
	                    $subtotal 	  = $item->get_subtotal(); // Line item subtotal
	                    $total 		  = $item->get_total(); // Line item total
	                    $name 	  	  = $item->get_name(); // Line item total
	                    $refund_amount  = 0;
	                    $refund_total   = 0;
	                    if(true === $has_refunds){
	                        $refund_qty = absint($order->get_qty_refunded_for_item($item_id));
	                        $quantity   = max(0, $quantity - $refund_qty);
	                        $refund_amount = abs($order->get_total_refunded_for_item($item_id));
	                        if($refund_amount > 0){
	                            $refund_total = max( 0, $total - $refund_amount);
	                            $total = $refund_total;
	                        }
	                    }
	                    if($quantity > 0 ){

	                        $product_terms_obj = get_the_terms( $product_id, 'product_cat' );
	                        $product_cats = array();
	                        $product_cats_s = array();
	                        if(!empty($product_terms_obj)){
	                            foreach ($product_terms_obj as $key => $product_term_obj) {
	                                $product_cats_name = $product_term_obj->name;
	                                $product_cats_slug = $product_term_obj->slug;
	                                $product_cats[] = $product_cats_name;
	                                $product_cats_s[] = $product_cats_slug;
	                            }
	                        }

	                        if(!isset($oPPA[$order_id])){
	                            $oPPA[$order_id] = array();
	                        }
	                        if(!isset($oPPA[$order_id][$product_id])){
	                            $oPPA[$order_id][$product_id] = array();
	                        }
	                        if(!isset($oPPA[$order_id][$product_id]['quantity'])){
	                            $oPPA[$order_id][$product_id]['quantity'] = 0;
	                        }
	                        if(!isset($oPPA[$order_id][$product_id]['subtotal'])){
	                            $oPPA[$order_id][$product_id]['subtotal'] = 0;
	                        }
	                        if(!isset($oPPA[$order_id][$product_id]['total'])){
	                            $oPPA[$order_id][$product_id]['total'] = 0;
	                        }
	                        if(!isset($oPPA[$order_id][$product_id]['orders'])){
	                            $oPPA[$order_id][$product_id]['orders'] = array();
	                        }
	                        if(!isset($oPPA[$order_id][$product_id]['p_cats'])){
	                            $oPPA[$order_id][$product_id]['p_cats'] = array();
	                        }
	                        if(!isset($oPPA[$order_id][$product_id]['p_cats_s'])){
	                            $oPPA[$order_id][$product_id]['p_cats_s'] = array();
	                        }
	                        $oPPA[$order_id][$product_id]['quantity'] = $oPPA[$order_id][$product_id]['quantity'] + $quantity;
	                        $oPPA[$order_id][$product_id]['subtotal'] = $oPPA[$order_id][$product_id]['subtotal'] + $subtotal;
	                        $oPPA[$order_id][$product_id]['total'] 	= $oPPA[$order_id][$product_id]['total'] + $total;
	                        $oPPA[$order_id][$product_id]['name'] 	= $name;
	                        $oPPA[$order_id][$product_id]['sku'] 		= $sku;
	                        $oPPA[$order_id][$product_id]['url'] 		= $url;
	                        $oPPA[$order_id][$product_id]['p_cats'] 	= $product_cats;
	                        $oPPA[$order_id][$product_id]['p_cats_s'] = $product_cats_s;
	                        if(!in_array($order_number, $oPPA[$order_id][$product_id]['orders'])){
	                            $oPPA[$order_id][$product_id]['orders'][$order_number] = $quantity;
	                        }
	                        if(!empty($gbc)){
	                            if(!empty($product_terms_obj)){
	                                foreach($product_terms_obj as $key => $product_term_obj){
	                                	$product_cat_s = $product_term_obj->slug;
	                                	$product_cats_name = $product_term_obj->name;
	                                	if (!isset($gbcA[$product_cat_s])) {
	                                    	$gbcA[$product_cat_s] = array();
	                                	}
	                                	if (!isset($gbcA[$product_cat_s][$order_id])) {
	                                    	$gbcA[$product_cat_s][$order_id] = array();
	                                	}
	                                	if (!isset($gbcA[$product_cat_s][$order_id][$product_id])) {
	                                    	$gbcA[$product_cat_s][$order_id][$product_id] = array();
	                                	}
	                                	if (!isset($gbcA[$product_cat_s][$order_id][$product_id]['quantity'])) {
	                                    	$gbcA[$product_cat_s][$order_id][$product_id]['quantity'] = 0;
	                                	}
	                                	if (!isset($gbcA[$product_cat_s][$order_id][$product_id]['subtotal'])) {
	                                    	$gbcA[$product_cat_s][$order_id][$product_id]['subtotal'] = 0;
	                                	}
	                                	if (!isset($gbcA[$product_cat_s][$order_id][$product_id]['total'])) {
	                                    	$gbcA[$product_cat_s][$order_id][$product_id]['total'] = 0;
	                                	}
	                                	if (!isset($gbcA[$product_cat_s][$order_id][$product_id]['orders'])) {
	                                    	$gbcA[$product_cat_s][$order_id][$product_id]['orders'] = array();
	                                	}
	                                	if (!isset($gbcA[$product_cat_s][$order_id][$product_id]['p_cats'])) {
	                                    	$gbcA[$product_cat_s][$order_id][$product_id]['p_cats'] = array();
	                                	}
	                                	if (!isset($gbcA[$product_cat_s][$order_id][$product_id]['p_cats_s'])) {
	                                    	$gbcA[$product_cat_s][$order_id][$product_id]['p_cats_s'] = array();
	                                	}
	                                	$gbcA[$product_cat_s][$order_id][$product_id]['quantity'] = $gbcA[$product_cat_s][$order_id][$product_id]['quantity'] + $quantity;
	                                	$gbcA[$product_cat_s][$order_id][$product_id]['subtotal'] = $gbcA[$product_cat_s][$order_id][$product_id]['subtotal'] + $subtotal;
	                                	$gbcA[$product_cat_s][$order_id][$product_id]['total'] = $gbcA[$product_cat_s][$order_id][$product_id]['total'] + $total;
	                                	$gbcA[$product_cat_s][$order_id][$product_id]['name'] = $name;
	                                	$gbcA[$product_cat_s][$order_id][$product_id]['sku'] = $sku;
	                                	$gbcA[$product_cat_s][$order_id][$product_id]['url'] = $url;
	                                	$gbcA[$product_cat_s][$order_id][$product_id]['p_cats'] = array($product_cats_name);
	                                	$gbcA[$product_cat_s][$order_id][$product_id]['p_cats_s'] = array($product_cat_s);
	                                	if (!in_array($order_number, $gbcA[$product_cat_s][$order_id][$product_id]['orders'])) {
	                                    	$gbcA[$product_cat_s][$order_id][$product_id]['orders'][$order_number] = $quantity;
	                                	}
	                                }
	                            }
	                        }
	                    }
	                }
	            }
            }
        }
        include_once( 'partials/order_per_product.php');
        include_once( 'partials/footer.php');
    }
	public function enqueue_styles() {
		global $pagenow;
		$pn = $this->plugin_name;
        $screen = get_current_screen();
        $base = $screen->base;
        if(($pagenow == 'admin.php') && strpos($base, $pn) !== false){
        	$plugin_dir = plugin_dir_url(__FILE__);

        	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'full' : 'min';
            $suffix_file = '.min';
            if($suffix == 'min'){
				$suffix_file = '.min';
            }else{
            	$suffix_file = '';
            }

            $lib_dir = $plugin_dir.'css/'.$suffix.'/';

        	wp_enqueue_style($pn.'_daterangepicker-css', $lib_dir.'daterangepicker'.$suffix_file.'.css',array(),'3.1.0');

		    wp_enqueue_style($pn.'_select2-css', $lib_dir.'select2'.$suffix_file.'.css',array(),'4.0.13');

		    wp_enqueue_style($pn.'_datatables-buttons-css', $lib_dir.'buttons.dataTables'.$suffix_file.'.css',array(),'3.1.0');

		    wp_enqueue_style($pn.'_datatables-css', $lib_dir.'dataTables'.$suffix_file.'.css',array(),'2.1.2');

		    wp_register_style($pn.'_jquery-ui', $plugin_dir.'css/jquery-ui.css', array(), '1.12.1');
            wp_enqueue_style($pn.'_jquery-ui');

            wp_enqueue_style('jquery-ui-datepicker');

            wp_enqueue_style($pn, plugin_dir_url(__FILE__) . 'css/order_management_woo-admin.css', array(), $this->version, 'all');
        }
	}
	public function enqueue_scripts() {
		global $pagenow;
		$pn = $this->plugin_name;
        $screen = get_current_screen();
        $base = $screen->base;
        if(($pagenow == 'admin.php') && strpos($base, $pn) !== false){
            $date = gmdate('Y-m-d');
            $name_page = get_bloginfo('name');
            $plugin_dir = plugin_dir_url(__FILE__);

            $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'full' : 'min';
            $suffix_file = '.min';
            if($suffix == 'min'){
				$suffix_file = '.min';
            }else{
            	$suffix_file = '';
            }

            $lib_dir = $plugin_dir.'js/'.$suffix.'/';

            wp_enqueue_script('jquery-ui-core');

            wp_enqueue_script('moment');
    		
    		wp_enqueue_script($pn.'_daterangepicker-js', $lib_dir.'daterangepicker'.$suffix_file.'.js', array('jquery'), '3.1.0', true);

    		wp_enqueue_script($pn.'_datatables-js', $lib_dir.'dataTables'.$suffix_file.'.js', array('jquery'), '2.1.2', true);

    		wp_enqueue_script($pn.'_datatables-buttons-js', $lib_dir.'dataTables.buttons'.$suffix_file.'.js', array('jquery'), '3.1.0', true);

    		wp_enqueue_script($pn.'_datatables-buttons-html5-js', $lib_dir.'buttons.html5'.$suffix_file.'.js', array('jquery'), '3.1.0', true);

    		wp_enqueue_script($pn.'_jszip-js', $lib_dir.'jszip'.$suffix_file.'.js', array('jquery'), '3.10.1', true);
    		
    		wp_enqueue_script($pn.'_pdfmake-js', $lib_dir.'pdfmake'.$suffix_file.'.js', array('jquery'), '0.2.10', true);

    		wp_enqueue_script($pn.'_vfs-fonts-js',$plugin_dir . 'js/vfs_fonts.js', array('jquery'), '0.2.10', true);
    		
    		wp_enqueue_script($pn.'_select2-js', $lib_dir.'select2'.$suffix_file.'.js', array('jquery'), '4.0.13', true);
    		
    		wp_enqueue_script('jquery-ui-datepicker');

            wp_enqueue_script($pn, $plugin_dir . 'js/order_management_woo-admin.js', array('jquery'), $this->version, false);
            
            wp_localize_script($pn, $pn, array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'name_page' => $name_page,
                'date' => $date
            ));
        }
	}
    public function order_management_woo_get_products() {
        if(isset($_REQUEST['nonce'])) {
            if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), 'wom')) {
                die('Security check fail');
            } else {
                if (isset($_POST['q']) && !empty($_POST['q'])) {
                    $search_query = sanitize_text_field($_POST['q']);

                    $args_products = array(
                        'post_type' => "product",
                        'posts_per_page' => 30,
                        'orderby' => 'name',
                        'order' => 'asc',
                        's'    => $search_query
                    );
                    $p_products = get_posts($args_products);
                    wp_reset_postdata();

                    $results = array();
                    if (!empty($p_products)) {
                        foreach ($p_products as $p_product) {
                            $results[] = array(
                                'product_id' => $p_product->ID,
                                'product_name'   => $p_product->post_title
                            );
                        }
                    }
                    wp_send_json($results);
                } else {
                    // If no search query is set, return an empty response
                    wp_send_json(array());
                }
            }
        }
    }
}