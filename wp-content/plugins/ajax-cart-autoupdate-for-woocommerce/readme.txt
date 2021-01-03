=== Ajax Cart AutoUpdate for WooCommerce ===
Contributors: taisho
Tags: woocommerce,ajax,cart,update,autoupdate,quantity,auto-update,refresh,qty,basket,notices
Donate link: https://www.paypal.me/TaishoDev/usd
Requires at least: 4.6
Tested up to: 5.4
Requires PHP: 5.4
Stable tag: 1.5.4
License: GPLv3
License URL: http://www.gnu.org/licenses/gpl-3.0.html

A light plugin that automatically updates cart page and mini-cart when product quantity is changed. Removes the default "Update cart" button. Optionally turns off cart page notices.

== Description ==

A light plugin that automatically updates cart page and mini-cart when product quantity is changed. Removes the default "Update cart" button. Optionally turns off cart page notices.

<h4>Features</h4>

* Cart page and mini-cart widget are updated automatically on quantity change through Ajax (no page reloading).
* "Update cart" button is removed from the cart page.
* Both mouse and keyboard changes are supported.
* Works for custom dropdown lists with 'qty' class.
* Compatible with plugin [Qty Increment Buttons for WooCommerce](https://wordpress.org/plugins/qty-increment-buttons-for-woocommerce/).
* Uses the default WooCommerce cart update event.
* Cart update is delayed by time in milliseconds since the last action affecting quantity, changeable in plugin settings, default 1000. It means that the update will fire only once when the customer is done with changes.
* Optionally change min quantity in the cart from 0 to 1, on by default.
* Optionally remove all notices from the cart page, on by default.

== Installation ==

<h4>Recommended:</h4>
Install and activate from WordPress "Plugins > Add New" screen.

<h4>Manual installation:</h4>
1. Unzip plugin file and upload folder to directory /wp-content/plugins/
1. Activate plugin in WordPress \"Plugins\" menu.

== Frequently Asked Questions ==

= Will this plugin work correctly on my website? =

Ajax Cart AutoUpdate for WooCommerce is designed to work on any WooCommerce-based website. It's tested for compatibility with version 3.0 (previously 2.6) and newer. It uses the default Ajax update event for the cart page, exactly the same as the "Update cart" button, for 100% compatibility.

= Update delay is set to 0. Why doesn't my cart update instantly? =

This plugin relies on the same event as "Update cart" button and none of them will update instantly. To update the quantity on the backend, a request needs to be sent from the website to the server and then a response back to the website. How fast it happens, is based entirely on the server response time.

= Quantity field on my cart page disappeared. How to fix it?

By default, this plugin changes the minimum quantity in the cart from 0 to 1. If min = max quantity, WooCommerce hides the quantity input field, so all products with max quantity = 1 will have it hidden with this setting checked.

= My cart page notices disappeared, how do I restore them? =

Removing cart page notices is enabled by default in plugin settings. Simply uncheck this option.

= Can I remove only selected cart page notices? =

Currently, it's not possible. Removing cart page notices is only an addition to the core functionality of this plugin, however, if enough users communicate me that such feature is important, I will consider it for future releases.

= Will this plugin affect my page speed? =

Very little, it's a small plugin that executes only on the cart page.

== Changelog ==

= 1.5.4 =
* Added info about compatibility with PHP 7.4.
* Added info about compatibility with WordPress 5.4.
* Added info about compatibility with WooCommerce 4.0.
* The oldest supported WooCommerce version is now 3.0 (previously 2.6). While the older versions should work correctly with this and future releases of ACAU, I will only run tests on 3.x.x and 4.x.x WooCommerce versions.
* Removed Spanish translation files from plugin core due to this translation no longer being maintained. All translations are now here, maintaned by contributors: https://translate.wordpress.org/projects/wp-plugins/ajax-cart-autoupdate-for-woocommerce/

= 1.5.3 =
* Fixed a problem with "Update cart" button still showing on some themes. CSS selector for hiding it is now consistent with more flexible jQuery selector used to update cart values.

= 1.5.2 =
* Reworked the code to make translate.wordpress.org load all strings for translation automatically.

= 1.5.1 =
* Fix - eliminated a PHP notice related to the new setting.
* The plugin is now correctly marked as WordPress 5.3 compatible.

= 1.5 =
* Increased required WordPress version from 4.4 to 4.6 for translate.wordpress.org compatibility.
* Tested for compatibility with WooCommerce 3.8.0.
* Tested for compatibility with WordPress 5.3.
* New setting - custom color for spinning wheels.

= 1.4.2 =
* Fix - resolved a bug since 1.4 that caused the current plugin settings to be lost after saving and made it impossible to change them.
* Fix - resolved a bug since 1.4 that caused the plugin's script to be loaded on all pages instead of only on the cart page.
* Improved descriptions in plugin settings.

= 1.4.1 =
* Declared compatibility with WooCommerce 3.7.0 (no changes were needed).
* Fix - CSS minification for plugin settings page that was accidentally turned off in release 1.4 is now correctly applied.

= 1.4 =
* Increased required PHP version from 5.3 to 5.4 due to changes in the backend of plugin settings.
* Added a PHP version check on plugin activation to prevent activation when the required PHP version is not installed on server.
* Instead of enqueuing a .js file, the plugin script is now enqueued inline by a function wc_enqueue_js.

= 1.03 =
* Translation-ready.
* New translation: Spanish.

= 1.02 =
* Added support for dropdown lists with 'qty' class.

= 1.01 =
* Fixed a bug on the settings page that prevented users from unchecking default options.

= 1.0 =
* Initial release