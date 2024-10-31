<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ahsandev.com/
 * @since      1.0.0
 *
 * @package    order_management_woo
 * @subpackage order_management_woo/admin/partials
 */
$show_product_filter = false;
$show_deleted_product_filter = false;
$show_cats_filter  = true;
$show_group_by_cats_filter  = false;


include_once( 'filters.php');

?>
	<table id="normalTable">
		<thead>
			<tr>
				<th class="t-sku">Sku</th>
				<th class="t-product">Product</th>
				<th class="t-quantity">Quantity</th>
				<th class="t-gross-sales">Gross Sales</th>
				<th class="t-cat">Categories</th>
				<th class="t-orders">Orders</th>
			</tr>
		</thead>
		<tbody>
<?php
	$g_totals_p = 0;
	$g_totals_q = 0;
	$g_totals_s = 0;
	$g_totals_o = 0;
	$g_totals_o_arr = array();
	if(!empty($p_array)){
		foreach($p_array as $product_id => $p){
			$quantity = $p['quantity'];
			$subtotal = $p['subtotal'];
			$total 	= $p['total'];
			$name 	= $p['name'];
			$orders = $p['orders'];
			$sku 	= $p['sku'];
			$url 	= $p['url'];
			$cats 	= $p['p_cats'];
			$p_cats_s = $p['p_cats_s'];

			$cat_s = false;
			if(!empty($slugs)){
				$cat_s = true;
				$cat_s_show = false;
				if(!empty($cats)){
					foreach($p_cats_s as $k_cat => $p_cat_s){
						if(in_array($p_cat_s,$slugs)){
							$cat_s_show = true;
						}
					}
				}
			}
			
			if($cat_s==false || ($cat_s == true && $cat_s_show == true)){

			$g_totals_p++;
			$g_totals_q += $quantity;
			$g_totals_s += $total;	
	?>
				<tr>
					<td class="t-sku"><?php echo esc_html($sku);?></td>
					<td class="t-product" data-id="<?php echo esc_html($product_id);?>">
						<a href="<?php echo esc_html($url);?>">
							<?php echo esc_html($name);?>
						</a>
					</td>
					<td class="t-quantity"><?php echo esc_html($quantity);?></td>
					<td class="t-gross-sales"><?php echo esc_html($total);?></td>
					<td class="t-cats"><?php echo esc_html(implode(", ",$cats));?></td>
					<td class="t-orders"><?php 
						$t_orders = array();
						if(!empty($orders)){
							asort($orders);
							foreach ($orders as $key => $order) {
								foreach ($order as $order_number => $quantity) {
									if(!isset($t_orders[$order_number])){
										$t_orders[$order_number] = array();
									}
									$t_orders[$order_number]['label'] = $order_number;
									$t_orders[$order_number]['order_no'] = $order_number;
									if(!in_array($order_number,$g_totals_o_arr)){
										$g_totals_o_arr[] = $order_number;
										$g_totals_o++;
									}
								}
							}
						}
						if(!empty($t_orders)){
							ksort($t_orders);

							$last_key_of_array = array_key_last($t_orders);
							foreach($t_orders as $t_key => $t_order){
								$order_number = $t_order['order_no'];
								$label = $t_order['label'];
								echo '<a href="'.esc_html(get_edit_post_link($order_number)).'">';
									echo esc_html($label);
								echo '</a>';
								if($last_key_of_array == $t_key){

								}else{
									echo ' | ';
								}
							}
						}
					?>
					</td>
				</tr>
<?php
			}
		}
	}
?>	</tbody>
		<tfoot>
			<tr>
				<th class="t-sku">Sku</th>
				<th class="t-product">Product</th>
				<th class="t-quantity">Quantity</th>
				<th class="t-gross-sales">Gross Sales</th>
				<th class="t-cat">Categories</th>
				<th class="t-orders">Orders</th>
			</tr>
	<tr>
		<th class="t-sku">Totals</th>
		<th class="t-product"><?php echo esc_html($g_totals_p);?></th>
		<th class="t-quantity"><?php echo esc_html($g_totals_q);?></th>
		<th class="t-gross-sales"><?php echo esc_html(round($g_totals_s,2));?></th>
		<th class="t-cat">-</th>
		<th class="t-orders"><?php echo esc_html($g_totals_o);?></th>
	</tr>
		</tfoot>
	</table>