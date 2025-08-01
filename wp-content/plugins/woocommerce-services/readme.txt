=== WooCommerce Tax (formerly WooCommerce Shipping & Tax) ===
Contributors: woocommerce, automattic, woothemes, allendav, kellychoffman, jkudish, jeffstieler, nabsul, robobot3000, danreylop, mikeyarce, shaunkuschel, orangesareorange, pauldechov, dappermountain, radogeorgiev, bor0, royho, cshultz88, bartoszbudzanowski, harriswong, ferdev, superdav42
Tags: tax, vat, gst, woocommerce, payment
Requires PHP: 7.4
Requires at least: 6.6
Requires Plugins: woocommerce
Tested up to: 6.8
WC requires at least: 9.8
WC tested up to: 10.0
Stable tag: 3.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

We’re here to help with tax rates: collect accurate sales tax, automatically.

== Description ==

Attention: Shipping features have moved to a new dedicated plugin. Download WooCommerce Shipping.

Enable automated taxes
That's it! Once you update your tax settings, your store will collect sales tax at checkout based on the store address in your WooCommerce Settings.

Eliminate the need to even think about sales taxes for your store
Automatically calculate how much sales tax should be collected for WooCommerce orders — by city, country, or state — at checkout.

== Installation ==

This section describes how to install the plugin and get it working.

1. Install and activate WooCommerce if you haven't already done so
2. Upload the plugin files to the `/wp-content/plugins/woocommerce-tax` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Install, activate and connect to your WordPress.com account if you haven't already done so
5. Enable automated taxes from WooCommerce > Settings > Tax (make sure "enable taxes" is checked in General settings first)

== Frequently Asked Questions ==

= Why is a WordPress.com account connection required? =

A WordPress.com connection is required to securely access our tax APIs, and to avoid API abuse.

= This works with WooCommerce, right? =

Yep! We follow the L-2 policy, meaning if the latest version of WooCommerce is 8.7, we support back to WooCommerce version 8.5.

= Are there Terms of Service? =

Absolutely! You can read our Terms of Service [here](https://wordpress.com/tos).

== External services ==

This plugin relies on the following external services:

1. WordPress.com connection:
   - Description: The plugin makes requests to our own endpoints at WordPress.com (proxied via https://api.woocommerce.com) to fetch automated tax calculations.
   - Website: https://wordpress.com/
   - Terms of Service: https://wordpress.com/tos/
   - Privacy Policy: https://automattic.com/privacy/

2. Usage Tracking:
   - Description: The plugin will send usage statistics to our own service, after the user has accepted our Terms of Service.
   - Script: https://stats.wp.com/w.js
   - Terms of Service: https://wordpress.com/tos/
   - Privacy Policy: https://automattic.com/privacy/

== Screenshots ==

1. Enabling automated taxes
2. Checking on the health of WooCommerce Tax

== Changelog ==

= 3.0.7 - 2025-07-21 =
* Fix   - Missing release files.

= 3.0.6 - 2025-07-21 =
* Add   - Support for Itemized tax rates.
* Fix   - TaxJar error notices displaying incorrectly on block cart and checkout.

= 3.0.5 - 2025-07-14 =
* Tweak - WooCommerce 10.0 Compatibility.

= 3.0.4 - 2025-06-30 =
* Fix   - Corrected tax calculation for orders shipped within Arizona from stores based in Arizona.

= 3.0.3 - 2025-06-12 =
* Tweak - Update Org store screenshots.

= 3.0.2 - 2025-06-02 =
* Rename the plugin and updates the description in the Org store.

= 3.0.1 - 2025-05-22 =
* Fix   - Maintain label purchase functionality on iOS app for eligible installations.

= 3.0.0 - 2025-05-08 =
* Add   - Legacy site detection to maintain shipping functionality for existing installations.
* Tweak - Improve tax tracking.

= 2.8.9 - 2025-04-07 =
* Tweak - WordPress 6.8 & WooCommerce 9.8 Compatibility.

= 2.8.8 - 2025-03-03 =
* Tweak - WooCommerce 9.7 Compatibility.

= 2.8.7 - 2025-01-20 =
* Add   - Option to apply US Colorado Retail Delivery Fee tax by using `wc_services_apply_us_co_retail_delivery_fee` filter.

= 2.8.6 - 2025-01-06 =
* Tweak - PHP 8.4 compatibility.

= 2.8.5 - 2024-12-10 =
* Fix   - Fixed an issue that prevented editing an order when automated tax is enabled.

= 2.8.4 - 2024-12-09 =
* Fix   - Support High-Performance Order Storage in shipping label reports.

= 2.8.3 - 2024-10-29 =
* Tweak - WordPress 6.7 Compatibility.

= 2.8.2 - 2024-09-23 =
* Fix   - Keep live rates enabled for eligible stores when WCS&T is active alongside WooCommerce Shipping.
* Tweak - Hide shipping migration banner for all stores not eligible to buy shipping labels.
* Tweak - Try WooCommerce Shipping modal copy.

= 2.8.1 - 2024-09-09 =
* Tweak - Hide migration banner for merchants still using legacy functionality.

= 2.8.0 - 2024-09-03 =
* Add - A new shipping migration experience from this plugin to the newly released WooCommerce Shipping plugin.

= 2.7.0 - 2024-07-25 =
* Add - Parallel compatibility with WooCommerce Shipping plugin.

= 2.6.2 - 2024-07-16 =
* Fix - Require HS Tariff number on customs form for EU destination countries.

= 2.6.1 - 2024-07-02 =
* Tweak - WooCommerce 9.0 and WordPress 6.6 compatibility.

= 2.6.0 - 2024-06-04 =
* Add - Logger for "Live Rates" feature on the front-end.

= 2.5.7 - 2024-05-13 =
* Add - wc_connect_shipment_item_quantity_threshold and wc_connect_max_shipments_if_quantity_exceeds_threshold filter hooks to be able to cap the number of shipment splits possible for an item with very large quantity.

= 2.5.6 - 2024-05-06 =
* Tweak - WooCommerce 8.8 compatibility.

= 2.5.5 - 2024-04-29 =
* Add - Prevent upcoming Woo Shipping and Woo Tax plugins from running in parallel with this plugin unless both are active, then they will take over for this plugin.

= 2.5.4 - 2024-03-25 =
* Tweak - WordPress 6.5 compatibility.

= 2.5.3 - 2024-03-12 =
* Fix - Colorado tax nexus workaround should only apply to Colorado from addresses.

= 2.5.2 - 2024-03-04 =
* Fix - Miscalculation tax from TaxJar and decided to use nexus address.

= 2.5.1 - 2024-02-12 =
* Fix - Cannot call constructor in classes/wc-api-dev/class-wc-rest-dev-data-continents-controller.php.

= 2.5.0 - 2024-01-08 =
* Add - Ability to keep connected to WordPress.com after Jetpack is uninstalled.
* Fix - Deprecation notices for PHP 8.2.

= 2.4.2 - 2023-11-30 =
* Fix - When automated taxes are enabled, the order refund button will fail

= 2.4.1 - 2023-11-28 =
* Fix - Street address is not included when recalculating the tax in edit order page.

= 2.4.0 - 2023-10-31 =
* Add - Ability to connect to WordPress.com without the Jetpack plugin.
* Fix - NUX banner display on Edit Order pages.

= 2.3.7 - 2023-10-23 =
* Add - Load Sift when printing a label.

= 2.3.6 - 2023-10-10 =
* Fix - Occasionally block user to checkout when using WooCommerce Blocks.
* Fix - Fix notice error when shipping location(s) is disabled in WooCommerce settings.

= 2.3.5 - 2023-09-20 =
* Tweak - Move Jetpack Connection requirement to the top in FAQ.

= 2.3.4 - 2023-09-05 =
* Fix - Shipping label reports to display proper HTML.

= 2.3.3 - 2023-08-22 =
* Tweak - Update .org assets.

= 2.3.2 - 2023-08-09 =
* Add   - Added QIT tools for development.

= 2.3.1 - 2023-07-17 =
* Fix    - Fix notice error on the WooCommerce tax settings page.

= 2.3.0 - 2023-07-11 =
* Add   - Add USPS HAZMAT support.

= 2.2.5 - 2023-05-23 =
* Update - Security update.

= 2.2.4 - 2023-03-14 =
* Fix   - Incompatibility with Kadence WooCommerce Email Designer.

= 2.2.3 - 2023-02-14 =
* Fix   - Link correction on Automated taxes description text.

= 2.2.2 - 2023-02-02 =
* Fix   - Adjust checkout US zipcode validation to run only when exactly 5 or 10 digits are typed.

= 2.2.1 - 2023-01-24 =
* Fix   - Fix warning on checkout page apper if zipcode doesn't match selected state.

= 2.2.0 - 2023-01-19 =
* Add   - Add option to let user pick whether to save the last package & service or not.

= 2.1.1 - 2023-01-02 =
* Fix   - Save the selected package box and do not skip the package step.

= 2.1.0 - 2022-11-30 =
* Tweak - Catch malformed zipcode and display WC notice.

= 2.0.0 - 2022-11-16 =
* Add   - High-Performance Order Storage compatibility.
* Add   - Add list of tax rate backup files for merchants to click and download.
* Tweak - Transition version numbering from SemVer to WordPress versioning.

= 1.26.3 - 2022-08-03 =
* Tweak - Always let the user to pick the package box.
* Add   - Add filter to override TaxJar result.
* Fix   - Uncatch error when installing/connecting the Jetpack.

= 1.26.2 - 2022-07-04 =
* Fix   - Change the wp-calypso commit to fix NPM Error when run `npm run prepare`.
* Fix   - E2E Tests: npm ci, update puppeteer to v2
* Fix   - JS Tests: npm ci
* Tweak - Replace colors npm package with chalk

= 1.26.1 - 2022-06-21 =
* Add   - Display warning if non-roman character is entered in address fields.
* Fix   - "Division by Zero" fatal error on PHP 8.

= 1.26.0 - 2022-05-27 =
* Add   - Tool to clear cached Tax server responses from the transients.
* Tweak - Enable shipping tax by default if is Florida interstate shipping.

= 1.25.28 - 2022-05-12 =
* Fix   - Notice: Undefined index: 'from_country' when validating TaxJar request.

= 1.25.27 - 2022-05-03 =
* Fix   - Cart with non-taxable product still calculate the tax.
* Tweak - Validate the TaxJar request before calling the api and cache 404 and 400 TaxJar response error for 5 minutes.

= 1.25.26 - 2022-04-19 =
* Fix   - Display error on cart block and checkout block from WC Blocks plugin.
* Fix   - TaxJar does not calculate Quebec Sales Tax when shipping from Canadian address.

= 1.25.25 - 2022-03-29 =
* Fix   - TaxJar does not get the tax if the cart has non-taxable on the first item.
* Tweak - Use regex to check on WC Rest API route for WooCommerce Blocks compatibility.

= 1.25.24 - 2022-03-17 =
* Fix - Empty document is opened when Firefox is set to open PDF file using another program.
* Fix - Label purchase modal sections getting cut off.

= 1.25.23 - 2022-02-10 =
* Tweak - Make "Name" field optional if "Company" field is not empty.
* Fix   - Added "Delete California tax rates" tool.
* Fix   - Extract WC_Connect_TaxJar_Integration::backup_existing_tax_rates() for re-usability.

= 1.25.22 - 2022-02-02 =
* Fix   - TaxJar does not get the tax if the cart has non-taxable item.
* Tweak - Bump WP tested version to 5.9 and WC tested version to 6.1.

= 1.25.21 - 2022-01-26 =
* Fix - Use 'native' pdf support feature for Firefox version 94 or later.
* Fix - Only call WC Subscriptions API when "access_token_secret" value is saved in database.
* Fix - Add name field to fields sent for EasyPost API address verification.
* Fix - Display company name under origin and destination address when create shipping label.
* Fix - Don't override general "Enable Tax" setting with WC Services Automated Taxes setting.

= 1.25.20 - 2021-11-15 =
* Fix - Hide "Shipping Label" and "Shipment Tracking" metabox when the label setting is disabled.
* Fix - Wrap TaxJar API zipcodes with wc_normalize_postcode() before inserting into the database.
* Fix - Update shipping label to only show non-refunded order line items.
* Fix - Added 3 digits currency code on shipping label price for non USD.

= 1.25.19 - 2021-10-14 =
* Add - Notice about tax nexus in settings.
* Fix - Country drop down list no longer showing currency name.

= 1.25.18 - 2021-08-16 =
* Add   - Added "Automated Taxes" health item on status page.
* Fix   - Show error when missing required destination phone for international shipments.
* Fix   - Prevent PHP notice when a label's `commercial_invoice_url` value is `null`.
* Fix   - Prevent fatal error when viewing draft order.
* Tweak - Bump WP tested version to 5.8.
* Tweak	- Bump WC Tested version to 5.5.

= 1.25.17 - 2021-07-13 =
* Tweak - Replace Calypso FormCheckbox with CheckboxControl.

= 1.25.16 - 2021-07-09 =
* Tweak - Replace components with @wordpress/components.

= 1.25.15 - 2021-06-30 =
* Fix   - Ensure shipping label metabox is displayed to users with the correct capabilities.
* Add   - Added `wcship_user_can_manage_labels` filter to check permissions to print shipping labels.
* Add   - Added `wcship_manage_labels` capability to check permissions to print shipping labels.

= 1.25.14 - 2021-06-15 =
* Fix   - Issue with printing blank label in Safari.
* Fix   - DHL Express labels - require customs form when shipping to Puerto Rico.
* Fix   - Update DHL Express pickup link.

= 1.25.13 - 2021-05-20 =
* Fix   - Prevent new sites from retrying failed connections.
* Fix   - Data encoding when entities are part of order meta.
* Tweak - Update WC version support in headers.
* Fix   - Plugin deletion when WooCommerce core is not present.
* Tweak - Rename automatic tax names for US.
* Fix   - Check Jetpack constant defined by name.
* Fix   - Sometimes taxes charged on shipping when they should not.

= 1.25.12 - 2021-04-21 =
* Fix   - UPS account connection form retry on invalid submission.
* Fix   - Fix PHP 5.6 compatibility issue.
* Tweak - Update plugin author name.
* Fix   - Removes unnecessary subscription debug error logs.

= 1.25.11 - 2021-04-06 =
* Fix	- Ensure status page is displayed on new WC navigation menu.
* Add   - Run phpcbf as a pre-commit rule.
* Fix   - Fix PHPUnit tests. Rename `test_` to `test-` to match our phpcs rules. Remove travis and move to github action.
* Tweak - Updated .nvmrc to use 10.16.0
* Tweak - Update the shipping label status endpoint to accept and return multiple ids.
* Tweak	- Display spinner icon during service data refresh.
* Add	- Adds Dockerized E2E tests with GitHub Action integration.
* Fix   - Handle DHL live rates notice creation and deletion errors.

= 1.25.10 - 2021-03-24 =
* Add   - Add an endpoint for shipping label creation eligibility and share code for store eligibility.
* Fix   - Shipping validation notice shown when no address entered.
* Tweak - Stop retrying to fetch /services when authentication fails on connect server.

= 1.25.9 - 2021-03-17 =
* Add   - WC Admin notice about DHL live rates.
* Add	- Live rates section in settings page.
* Tweak - Cleanup stripe functionality.
* Tweak - Display better errors on checkout page when address fields are missing / invalid.
* Tweak - Refresh on status page does not reload page.
* Fix   - UPS invoice number allows numbers and letters.
* Add 	- Tracks shipping services used at checkout.
* Add   - Update the existing endpoint `POST /connect/packages` to create shipping label packages, and add an endpoint `PUT /connect/packages` to update shipping label packages.
* Fix   - Only display shipping validation errors on the cart or checkout pages.
* Tweak - Removes deprecated Jetpack constant JETPACK_MASTER_USER
* Fix   - Revert radio button dot offset in the "Create shipping label" modal.

= 1.25.8 - 2021-03-02 =
* Tweak - Add support for new Jetpack 9.5 data connection.
* Tweak - Change minimum Jetpack version support to Jetpack 7.5.

= 1.25.7 - 2021-02-09 =
* Fix   - Prevent error notices on checkout page load.
* Tweak - Highlight rate call usage over limit on WooCommerce Shipping settings page.
* Fix   - Connect carrier account link broken on subdirectory installs.
* Fix   - Position dot in the center of radio buttons in "Create shipping label".
* Fix   - Adjust radio button dot style in "Create shipping label" in high contrast mode on Windows.

= 1.25.6 - 2021-01-26 =
* Fix 	- Refreshes shipping methods after registering or removing carrier accounts.
* Tweak	- Changed rates response caching method from cache to transient.

= 1.25.5 - 2021-01-11 =
* Fix	- Redux DevTools usage update.
* Add	- Display subscriptions usage.
* Add	- Subscription activation.
* Add 	- Uses same DHL logo for all registered DHL accounts.
* Tweak - Adds WCCom access token and site ID to connect server request headers.

= 1.25.4 - 2020-12-08 =
* Tweak - Remove Stripe connect functionality.
* Tweak - Remove unused method in shipping settings view.
* Fix	- Breaking behavior on account registration page.
* Add	- Allows registration of additional accounts.
* Tweak - Carrier description on dynamic carrier registration form.
* Fix   - Adjust documentation links.

= 1.25.3 - 2020-11-24 =
* Add   - Initial code for WooCommerce.com subscriptions API.
* Add   - Dynamic carrier registration form.
* Fix   - When adding "signature required" to some packages, prices were not updating.
* Add   - DHL Schedule Pickup link within order notes.
* Fix   - UI fix for input validation for package dimensions and weights.
* Fix   - Correct validation for UPS fields in Carrier Account connect form.
* Tweak - Add message to explain automated tax requires tax-exclusive product pricing.
* Fix   - Disable USPS refunds for untracked labels only.
