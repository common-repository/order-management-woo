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
?>
<div class="order_management_woo">
	<div class="wrap">
		<h2>Preparing Orders</h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2 admin-orders-display">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<a href="<?php echo esc_html(admin_url('admin.php?page=order_management_woo'));?>" class="button btn">Orders Summary</a>
						<a href="<?php echo esc_html(admin_url('admin.php?page=order_management_woo_products'));?>" class="button btn">Categories Sold</a>
						<a href="<?php echo esc_html(admin_url('admin.php?page=order_management_woo_products_per_order'));?>" class="button btn">Products Sold</a>
						<a href="<?php echo esc_html(admin_url('admin.php?page=order_management_woo_order_per_product'));?>" class="button btn">Products Sold Per Order</a>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>