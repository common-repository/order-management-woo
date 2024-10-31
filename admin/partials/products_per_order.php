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
$show_product_filter = true;
$show_deleted_product_filter = true;
$show_cats_filter  = true;
$show_group_by_cats_filter  = false;

include_once( 'filters.php');

?>
	<table id="normalTable">
		<thead>
			<tr>
				<th class="t-product">Product</th>
				<th class="t-order">Order</th>
				<th class="t-date">Created Date</th>
				<th class="t-sku">Sku</th>
				<th class="t-quantity">Quantity</th>
				<th class="t-total">Total</th>
				<th class="t-cat">Categories</th>
				<th class="t-c_name">Name</th>
				<th class="t-c_email">Email</th>
				<th class="t-c_phone">Phone</th>
				<th class="t-c_address">Address</th>
			</tr>
		</thead>
		<tbody>
<?php
	if(!empty($p_array)){
		foreach($p_array as $order_no => $p_array2){
			foreach($p_array2 as $product_id => $p_array){
				
				$quantity 	= $p_array['quantity'];
				$total 		= $p_array['total'];
				$name 		= $p_array['name'];
				$sku 		= $p_array['sku'];
				$url 		= $p_array['url'];
				$cats 		= $p_array['p_cats'];
				$p_cats_s 	= $p_array['p_cats_s'];
				$created_at = $p_array['created_at'];
				$order_id 	= $p_array['order_id'];
				$c_name 	= $p_array['c_name'];
				$c_email 	= $p_array['c_email'];
				$c_phone 	= $p_array['c_phone'];
				$c_address 	= $p_array['c_address'];
				$deleted 	= $p_array['deleted'];

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

					if((!empty($show_deleted) && $deleted == true) || empty($show_deleted)){

						if($deleted){
							$url = '#';
						}
		?>
					<tr>
						<td class="t-product" data-id="<?php echo esc_html($product_id);?>">
							<a href="<?php echo esc_html($url);?>">
								<?php echo esc_html($name);?>
							</a>
						</td>
						<td class="t-order">
							<a href="<?php echo esc_html(get_edit_post_link($order_id));?>">
								<?php echo esc_html($order_no); ?>
							</a>
						</td>
						<td class="t-date"><?php echo esc_html($created_at);?></td>
						<td class="t-sku"><?php echo esc_html($sku);?></td>
						<td class="t-quantity"><?php echo esc_html($quantity);?></td>
						<td class="t-total"><?php echo esc_html($total);?></td>
						<td class="t-cats"><?php echo esc_html(implode(", ",$cats));?></td>
						<td class="t-c_name"><?php echo esc_html($c_name);?></td>
						<td class="t_c_email"><?php echo esc_html($c_email);?></td>
						<td class="t-c_phone"><?php echo esc_html($c_phone);?></td>
						<td class="t-c_address"><?php echo esc_html($c_address);?></td>
					</tr>
	<?php
					}
				}
			}
		}
	}
?>	</tbody>
		<tfoot>
			<tr>
				<th class="t-product">Product</th>
				<th class="t-order">Order</th>
				<th class="t-date">Created Date</th>
				<th class="t-sku">Sku</th>
				<th class="t-quantity">Quantity</th>
				<th class="t-total">Total</th>
				<th class="t-cat">Categories</th>
				<th class="t-c_name">Name</th>
				<th class="t-c_email">Email</th>
				<th class="t-c_phone">Phone</th>
				<th class="t-c_address">Address</th>
			</tr>
		</tfoot>
	</table>