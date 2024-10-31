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
$show_cats_filter  = false;
$show_group_by_cats_filter  = false;

include_once( 'filters.php');

?>
	<table id="normalTable">
		<thead class="table-header">
			<tr>
				<th class="order_number">Order Number</th>
				<th class="date">Date</th>
				<th class="name">Name</th>
				<th class="phone">Phone</th>
				<th class="email">Email</th>
				<th class="payment_title">Payment Method</th>
				<th class="address">Shipping Address</th>
				<th class="shipping_method">Shipping Method</th>
				<th class="total-shipping">Shipping Total</th>
				<th class="total">Total</th>
				<th class="status">Status</th>
				<th class="notes">Notes</th>
			</tr>
		</thead>
		<tbody>
<?php
	$g_totals_orders 	= 0;
	$g_totals_shipping 	= 0;
	//$g_totals_shipping_tax = 0;
	$totalOrders = 0;
	if(!empty($orders)){
		foreach($orders as $orderObj){
			$totalOrders  = $totalOrders + 1;
			$order_id 	  = $orderObj->ID;
			$order 		  = wc_get_order($order_id);
			if($order){
				$order_number = $order->get_order_number();
				$order_total  = $order->get_total();
				$status 	  = $order->get_status();
				$first_name = $order->get_shipping_first_name();			
				$name  		= $first_name.' '.$order->get_shipping_last_name();
				$email 		= $order->get_billing_email();//$order->get_shipping_email();
				$phone 		= $order->get_billing_phone();//$order->get_shipping_phone();
				//$order_date = $order->get_date_created(); //$order->order_date;
				$order_date = gmdate( 'd-m-Y', strtotime( $order->get_date_created() ));
				
				$address = $order->get_shipping_address_1().' '.$order->get_shipping_address_2();
				//$billing_country = $order->get_billing_country();
				$notes   = $order->get_customer_note();

				$shipping_method = $order->get_shipping_method();
				$shipping_total = $order->get_shipping_total();
				//$shipping_tax   = $order->get_shipping_tax();

				//$payment_method = $order->get_payment_method();
				$payment_title = wp_strip_all_tags($order->get_payment_method_title());

				$g_totals_orders += $order_total;
				$g_totals_shipping += $shipping_total;
				//$g_totals_shipping_tax += $shipping_tax;

	?>
				<tr>
					<td class="get_order_id order_number" data-id="<?php echo esc_html($order_id);?>"><?php echo esc_html($order_number);?></td>
					<td class="date"><?php echo esc_html($order_date);?></td>
					<td class="name"><?php echo esc_html($name);?></td>
					<td class="phone"><?php echo esc_html($phone);?></td>
					<td class="email"><?php echo esc_html($email);?></td>
					<td class="payment_title"><?php echo esc_html($payment_title);?></td>
					<td class="address"><?php echo esc_html($address);?></td>
					<td class="shipping_method"><?php echo esc_html($shipping_method);?></td>
					<td class="total-shipping"><?php echo esc_html($shipping_total);?></td>
					<td class="total"><?php echo esc_html($order_total);?></td>
					<td class="status"><?php echo esc_html($status);?></td>
					<td class="delivery-notes"><?php echo esc_html($notes);?></td>
				</tr>
	<?php
			}
		}
	}
?>	</tbody>
		<tfoot>
			<tr>
				<th class="order_number">Order Number</th>
				<th class="date">Date</th>
				<th class="name">Name</th>
				<th class="phone">Phone</th>
				<th class="email">Email</th>
				<th class="payment_title">Payment Method</th>
				<th class="address">Shipping Address</th>
				<th class="shipping_method">Shipping Method</th>
				<th class="total-shipping">Shipping</th>
				<th class="total">Total</th>
				<th class="status">Status</th>
				<th class="notes">Notes</th>
			</tr>
			<tr>
				<th class="order_number"><br></th>
				<th class="date"><br></th>
				<th class="name"><br></th>
				<th class="phone"><br></th>
				<th class="email"><br></th>
				<th class="payment_title"><br></th>
				<th class="address"><br></th>
				<th class="shipping_method"><br></th>
				<th class="total-shipping"><br></th>
				<th class="total"><br></th>
				<th class="notes"><br></th>
				<th class="status"><br></th>
			</tr>
			<tr>
				<th class="order_number">Total Orders:</th>
				<th class="date"><?php echo esc_html($totalOrders);?></th>
				<th class="name">-</th>
				<th class="phone">-</th>
				<th class="email">-</th>
				<th class="payment_title">-</th>
				<th class="address">-</th>
				<th class="shipping_method">Shipping Total:</th>
				<th class="total-shipping"><?php echo esc_html(round($g_totals_shipping,2));?></th>
				<th class="total">-</th>
				<th class="notes">Grand Total:</th>
				<th class="status"><?php echo esc_html(round($g_totals_orders,2));?></th>
			</tr>
		</tfoot>
	</table>