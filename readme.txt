=== Woocommerce Global Price Options ===
Contributors: yehudaT
Donate link: http://www.atarimtr.com/
Tags: global prices, price options, product options, photography, sell photos, woocommerce
Requires at least: 4.4.14
Tested up to: 6.4.2
Stable tag: 1.0.5
WC tested up to: 7.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add global price options by category to Woocommerce products. 

== Description ==

Add global price options by category/categories to Woocommerce products. Allows you to set fixed list of prices (Variations like) to all products in selected categories. 
It is aimed for use by WooCommerce shops that use identical price options for all products in a category or categories.

e.g.

*   Photographer sites selling photos in different sizes options (But same options to all). 
*   Sites that sell by license and need to show list of license options on each product (like music files etc.) 
*   Sites that have a product category with same price to all products in it, variated by any feature selected by the customer.


>Set price options once, apply to all products in the selected categories.

It eliminates the use of variable products while let the shop owner force selection of price option form the pre defined list.

>Tested up to Woocommerce Version 6.0.0

>Requires at least Woocommerce Version 2.6.0

See in the video

[youtube https://youtu.be/fKa5ALeY9Wo]

= Features =

*   Globally add price options to products. 
*   You can dynamically add and manage the price options with title and price for each option.
*   Price options can be displayed as radio buttons or drop down list in the product page.
*   Select categories to apply the price options. All other categories remain intact.
*   Adjust the text on the "Read more" button in the shop/archive pages. e.g "select license" or "select options" etc.
*   In the cart, a title and description is added to each item with the selected option details. You can control the label for the description.
*   Customer can add the product to cart with multiple options (one at a time.)
*   You can opt to make product without price definition to be purchasable with your price options. 



== Installation ==

Installing can be done either by searching for "ATR Woocommerce Global Price Options" via the "Plugins > Add New" screen in your WordPress dashboard, 
or by using the following steps:

1. Upload the plugin dir to the '/wp-content/plugins/' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

Important! You must go to plugin settings page and select the options apply to your shop.

= Settings =

1.   Use the Woocommerce->ATR Woo GPO screen to configure the plugin.
2.   In "Manage your price list" tab add a title and price for each price option.
3.   You can add options by clicking "Add price option" button at the bottom of the table.
4.   You can remove option by clicking the "Remove" button on a row side.
5.   On the "Other options" tab you can control other options.
6.   Added import export settings
7.   Added option to disable quantity.
8.   In settings "Select categories" added select all / select none

== Customize the display of the price list ==

If you need to change the HTML of the header and footer of the price list display, you can use the following filters in your theme's functions.php file:

[Using filters to change ATR Woocommerce Global Price Options plugin display of price options list in single product page](https://gist.github.com/yehudaTiram/9f4006c885c9d65edd01873262db4c7b)

== Screenshots ==

1. The product screen with the global price options
2. Setting - The global price options are set here
3. Setting - Selecting categories to apply the global price option on and other options


== Changelog ==

= 1.0.5 =
* Fix - when settings for item_label in cart empty.
* Tested for WP 5.5
* Tested for Woocommerce  4.3.2

= 1.0.4 =
* Fix - When option "Text label for selected option in the cart" is empty the selected option title was not displayed.

= 1.0.3 =
* Added option for custom separator between title and price
* Added option for custom separator between price and currency

= 1.0.2 =
* 2118-4-20

1. Settings - Added import export of price list

2. Added option to enable/disable quantity.

3. Settings "Select categories" - added "select all"/"select none"

4. Fixed selected price option not shown in order details

= 1.0.1 =
* 2118-4-7

1. Added option to select between Radio buttons list or Dropdown list

2. Fixed filters for price options list Head, Footer, Before item and After item

3. Added settings option for Header & Footer of price options list
