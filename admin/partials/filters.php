<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$slugs 	  	  = array();
$products 	  = array();
$p_products   = array();
$order_status = array();

$gbc 		  = '';
$date 	  	  = '';
$date_end 	  = '';
$show_deleted = '';

$catargs = array(
    'taxonomy'   	 => 'product_cat',
    'posts_per_page' => -1,
    'orderby'    	 => 'name',
    'order'      	 => 'asc',
    'hide_empty'	 => true
);
$p_cats   = get_terms($catargs);

if(isset($args) && !empty($args)){
	if(isset($args['date_query']) && !empty($args['date_query'])){
		if(isset($args['date_query'][0]) && !empty($args['date_query'][0])){
			if(isset($args['date_query'][0]['after']) && !empty($args['date_query'][0]['before'])){
				$date = $args['date_query'][0]['after'];
			}
			if(isset($args['date_query'][0]['before']) && !empty($args['date_query'][0]['before'])){
				$date_end = $args['date_query'][0]['before'];
			}
		}
	}
	if(isset($args['post_status']) && !empty($args['post_status'])){
        $order_status = $args['post_status'];
    }
    if(isset($args['slugs']) && !empty($args['slugs'])){
        $slugs = $args['slugs'];
    }
    if(isset($args['gbc']) && !empty($args['gbc'])){
        $gbc = $args['gbc'];
    }
    if(isset($args['show_deleted']) && !empty($args['show_deleted'])){
        $show_deleted = $args['show_deleted'];
    }
    if(isset($args['p_products']) && !empty($args['p_products'])){
        $p_products = $args['p_products'];
    }
    if(isset($args['product_ids']) && !empty($args['product_ids'])){
        $product_ids = $args['product_ids'];
    }
}

?>
<form method="POST" action="">
	<div class="form-group">
		<label for="date_calendar">Date</label>
		<span id="deliverydate">
			<input type="text" class="order_date" id="date_calendar" name="date" placeholder="Date" value="<?php echo esc_html($date);?>" autocomplete="off">
			<span class="date-to-sep">To</span>
			<input type="text" class="order_date" id="date_calendar_end" name="date_end" placeholder="Date End" value="<?php echo esc_html($date_end);?>" autocomplete="off">
		</span>
	</div>
	<div class="form-group">
		<label for="order_status">Order Status</label>
		<span class="order_status_label">
			<select class="order_status select2" id="order_status" name="order_status[]" placeholder="Order Status" multiple>
<?php
		    foreach($order_statuses as $order_status_key => $order_status_name){
		    	$Selected = '';
				if(in_array($order_status_key, $order_status)){ 
					$Selected = 'Selected="Selected"';
				}
		        echo '<option value="'.esc_html($order_status_key).'" '.esc_html($Selected).'>'.esc_html($order_status_name).'</option>';
		    }
?>
			</select>
		</span>
	</div>
<?php
	if($show_cats_filter){
		if(isset($p_cats) && !empty($p_cats)){
?>
	<div class="form-group">
		<label>Categories</label>
		<span id="p_cats">
			<select class="p_cats select2" id="p_cats" name="p_cats[]" placeholder="Categories" multiple>
				<?php 
					foreach($p_cats as $key => $p_cat) {
						$slug = $p_cat->slug;
						$name = $p_cat->name;
						$Selected = '';
						if(in_array($slug, $slugs)){ 
							$Selected = 'Selected="Selected"';
						}
						echo '<option value="'.esc_html($slug).'" '.esc_html($Selected).'>'.esc_html($name).'</option>';
					}
				?>
			</select>
			</span>
		</div>
<?php
		}
	}
	if($show_group_by_cats_filter){
?>
		<div class="form-group">
			<label for="gbc">Group By Category</label>
			<input type="checkbox" id="gbc" name="gbc" class="form-input form-control gbc" <?php echo esc_html($gbc); ?>/>
		</div>
<?php
	}
	if($show_product_filter){
?>
	<div class="form-group">
		<label for="product_ids">Products</label>
		<span class="product_ids_label">
			<select class="product_ids select2" id="product_ids" name="product_ids[]" placeholder="Products" multiple>
	                 <!-- Options will be dynamically loaded here via AJAX -->
					<?php
						if(!empty($p_products)){
							foreach($p_products as $key => $p_product) {
								$ID   = $p_product->ID;
								$name = $p_product->post_title;
								$Selected = '';
								if(in_array($ID, $product_ids)){ 
									$Selected = 'Selected="Selected"';
								}
								echo '<option value="'.esc_html($ID).'" '.esc_html($Selected).'>'.esc_html($name).'</option>';
							}
						}
					?>
			</select>
		</span>
	</div>
<?php 
	}
	if($show_deleted_product_filter){
?>
	<div class="form-group">
			<label for="show_deleted">Show only products which are Deleted from Woocommece Products but in order as item.</label>
			<input type="checkbox" id="show_deleted" name="show_deleted" <?php echo esc_html($show_deleted);?> />
	</div>
<?php
	}
?>
	<div class="form-group">
		<input type="submit" name="submit" value="Search" class="btn button" />
		<input type="hidden" id="plugin_nonce" name="nonce" value="<?php echo esc_html(wp_create_nonce('wom'));?>"  />
	</div>
</form>

<?php
	if(isset($totalOrders) && !empty($totalOrders)){
		echo '<div class="total-orders-count">';
			echo 'Total Orders By Date & Status = '.esc_html($totalOrders);
		echo '</div>';
	}
