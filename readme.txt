=== Order Management for WooCommerce ===
Contributors: ah72king
Donate link: https://ahsandev.com/
Tags: order, woocommerce management, analytics, reports
Requires at least: 3.0.1
Tested up to: 6.6
Tested Woocommerce up to: 9.1
Requires PHP: 7.1
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
This Plugin is used to provide the features to do management of woocommerce orders, it provide detail reports and analytics for woocommerce store

== Installation ==

= Minimum Requirements =

* Woocommerce Plugin must be installed and active.
* PHP 7.1 or greater is required (PHP 8.0 or greater is recommended)
* MySQL 5.6 or greater, OR MariaDB version 10.1 or greater, is required.

= Automatic installation =

Automatic installation is the easiest option -- WordPress will handle the file transfer, and you won’t need to leave your web browser. To do an automatic install of Order Management for WooCommerce, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”

In the search field type “Order Management for WooCommerce,” then click “Search Plugins.” Once you’ve found us, you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by clicking “Install Now,” and WordPress will take it from there.

= Manual installation =

Manual installation method requires downloading the Order Management for WooCommerce plugin and uploading it to your web server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation).

== Detail Description ==

= General =

Admin user will get date range filter and order status filter using that user can search the details of the woocommerce orders. Other filters like Categories Multi Select, Products Multi Select are provided on specific reports. Filter of Group By Category is provided in Products Sold Per Order. Filter of show Deleted only is provided in Product Sold Report where if item or product is deleted in woocommerce then its data will be shown only.

= Orders Summary =

A table will be shown with the following data:
1. **Order Number**: ($order->get_order_number())
2. **Date**: (gmdate('d-m-Y', strtotime($order->get_date_created())))
3. **Name**: ($order->get_shipping_first_name().' '.$order->get_shipping_last_name())
4. **Phone**: ($order->get_billing_phone())
5. **Email**: ($order->get_billing_email())
6. **Payment Method**: ($order->get_payment_method_title())
7. **Shipping Address**: ($order->get_shipping_address_1().' '.$order->get_shipping_address_2())
8. **Shipping Method**: ($order->get_shipping_method())
9. **Shipping Total**: ($order->get_shipping_total())
10. **Total**: ($order->get_total())
11. **Status**: ($order->get_status())
12. **Notes**: ($order->get_customer_note())

= Categories Sold =

A table will be shown with the following data:
1. **SKU**: Product SKU
2. **Product**: Product name
3. **Quantity**: Quantity orders in all orders (specific filters)
4. **Gross Sales**: Gross Sales of this product in all orders (specific filters)
5. **Categories**: Product belongs to these categories
6. **Orders**: Order numbers with link to backend detail page.

= Product Sold =

A table will be shown with the following data:
1. **Products**: Product name
2. **Orders**: Order number
3. **Created Date**: Date on which order created (Y-m-d)
4. **SKU**: Product SKU
5. **Quantity**: Product qty ordered in this order only
6. **Total**: Product Total in that order
7. **Categories**: Product belongs to these categories
8. **Name**: Name of person to ship to ($order->get_shipping_first_name().' '.$order->get_shipping_last_name())
9. **Email**: Email of person billed ($order->get_billing_email())
10. **Phone**: Phone of person billed ($order->get_billing_phone())
11. **Address**: Shipping Address, State, ZipCode / PostCode

= Product Sold Per Order =

A table will be shown with the following data:
1. **SKU**: Product SKU
2. **Product**: Product name
3. **Quantity**: Quantity in this order
4. **Gross Sales**: Gross Sales of this product in this order
5. **Categories**: Product belongs to these categories
6. **Orders**: Order number with link to backend detail page.

== Notes ==

All data should be cross-verified as there could be some mistakes in calculations.

If you have too many orders and date range is large like one 6 months or 1 year etc 
it will take sometime to load depending on your server load and specs plus memory allocated to wordpress application (your site) matter alot here when doing large queries

## Source Code

The source code for the compressed JavaScript libraries files used in this plugin can be found in the `admin/js/full` directory within the plugin folder.

The minified JavaScript files are located in the `admin/js/min` directory.


## Script Loading

This plugin conditionally loads minified or full JavaScript files based on the `SCRIPT_DEBUG` constant.

- When `SCRIPT_DEBUG` is set to `true`, the full JavaScript files are loaded from `admin/js/full/`.
- When `SCRIPT_DEBUG` is set to `false`, the minified JavaScript files are loaded from `admin/js/min/`.

To enable script debugging, define `SCRIPT_DEBUG` as `true` in your `wp-config.php` file:

define( 'SCRIPT_DEBUG', true );

== Screenshots ==
1. Order Summary
2. Categories Sold
3. Products Sold
4. Products Sold Per Order

== Changelog ==

= 1.0.0 =
* Plugin is introduced.

= 1.0.1 =
* Extra Libraries removed.

= 1.0.2 =
* Date Validation added.
* PDF size converted to A2.

= 1.0.3 =
* Group By Fixed
* Default Sorting removed