=== WooCommerce Poor Guys Swiss Knife ===
Contributors: ulih
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJ4K2X953H8CC
Tags: WooCommerce checkout manager, WooCommerce cart manager, cart manager, checkout manager, checkout, cart, shop, WooCommerce, shop settings, cart settings, checkout settings, variations bulk manager, variations manager, minimum items, maximum items, quantity input, minimum quantity, maximum quantity, wholesale, checkout personalization, checkout form, checkout customization, custom forms, custom fields, cart button, payment gateways, payment gateways customization, gateways, shipping, field editor, field, buy, pay, bulk management, variations, variation extender
Requires at least: 3.1
Tested up to: 3.8
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Swiss Knife for WooCommerce

== Description ==

WooCommerce Poor Guys Swiss Knife powerloads your standard WooCommerce installation. You can fine-tune your shop, customize your checkout forms, adapt your shop for country specific needs, set rules for cart operations and a lot more.

WooCommerce Poor Guys Swiss Knife comes packaged with real value and no restrictions. The big brother WooCommerce Rich Guys Swiss Knife offers additional stuff and features.

Data captured via customized (added) form fields is available within your order administration and will be added in the appropiate sections of the individual orders. What shows up can be configured for each individual custom field. Data captured will be shown to the customer on the order receipt page and in emails.

You can even organize your form with drag and drop and define if a field spans over the full row or set it as first or last element on a checkout form row.

WooCommerce Poor Guys Swiss Knife has been developed in cooperation with [Nicestay](http://www.nicestay.net) to set up a transfer and shuttle service website related with their short term rental business of apartments in Barcelona.

From version 1.3 onwards captured data in the context of billing and shipping custom fields will display as additional billing and shipping information on order receipt page and emails.

Now includes translations for German and Spanish.

== Usage ==

= Shop Settings =

* Set cart button label
* Enable Fast Cart (show cart after a customer added something to the cart in the shop. Also available in WooCommerce)
* Enable Fast Checkout (go directly to checkout, when a customer adds a product from the shop)
* Enable Payment Gateways (allows you to restrict the available payment gateways on a per product basis)

= Variation Overload and Bulk Settings =

Allows you to modify variations with bulk operations and to powerload variations allowing you to use existing variations as groups and to attach logical variations which sets you free of runtime limits preventing you to run more than 200 or 300 variations.

You need to upgrade to WooCommerce Rich Guys Swiss Knife to use Variation Overload and Bulk Settings

= Cart Settings =

* Minimum and maximum of allowed cart items (not to confuse quantities of individual items or products)
* Turn of quantity input specifically for product types (simple, variable, grouped, etc.)
* Set minimum and maximum for quantities required or allowed for individual products in the context of product types (simple, variable, variations, etc.)

= Checkout Settings =

* Configure default offset and maximum offset for date fields you add to your checkout forms (can be overwritten in the configuration of each date field)
* Add a billing email validator field to the billing section to assure that the user does not misspell his email
 
= Checkout Billing and Shipping Form Sections =

* Order existing and added fields
* Configure required status
* Add new fields (support for date, time, text, textarea, number and select. Select fields can be configured to display as dropdown selects, radios or checkboxes)
* Decide if data captured from a custom field will be shown in the order within the administration
* Define labels and placeholders
* Set the css display class which allows you to rearrange your form (wide (full row), first or last element in row)
* Configurator for custom field (allows you to finetune your custom fields and add values, limitations or validation to your custom fields)

= Checkout Order Section =

You need to upgrade to WooCommerce Rich Guys Swiss Knife to edit the Order Form Section

= Checkout Shared Section =

You need to upgrade to WooCommerce Rich Guys Swiss Knife to add an additional Shared Form Section 

= Checkout Item Personalization Forms =
 
You need to upgrade to WooCommerce Rich Guys Swiss Knife to add personalization forms for individual products but also for variations

== Installation ==

= Minimum Requirements =

* WordPress 3.1 or greater (may work on versions below but not tested)
* PHP version 5.2.4 or greater
* WooCommerce 2.0 or greater

= Manual installation on server =

1. Download
2. Upload to your '/wp-contents/plugins/' directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

= Installation on hosted site =
1. Download the plugin file to your computer, unzip preserving directory names and structure
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation's wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

= Usage =

Find instructions under "Other Notes"

== Upgrade Notice ==

Automatic updates should work without problems, but, like always, backup your site and you'll feel better.

== Frequently Asked Questions ==

= What field types does WooCommerce Poor Guys Swiss Knife support = 
With WooCommerce Poor Guys Swiss Knife you can support:

* Fields of type text with the sub-types via validation of: email, password
* Textarea
* Date
* Time
* Number (as simple numeric input or ranges)
* Select (with subtypes of checkboxes, radio buttons or single or multiple select boxes)

With WooCommerce Poor Guys Swiss Knife you have more or less 11 diferent custom field types available.

= Does Poor Guys Swiss Knife limit options for selects or other custom fields = 
Not in any way. You can add as many options as you like. You can also add as many custom fields as you like.

= There's only select available, how can I create checkboxes and radio buttons = 
Checkboxes, radio buttons and selects are in fact all selections and you can define the presentation of a select field as dropdown or multiple selection box, as checkboxes or radio buttons. You only have to keep in mind the logic. Radio Buttons and dropdown selects are for single and exclusive selections, whereas a multiple choice selection box and checkboxes allow multiple selections. You can configure this in the settings of your select custom field.

= Can I deactivate the plugin without problems = 
Yes, you can. WooCommerce Poor Guys Swiss Knife does not alter in any way your WooCommerce installation. It uses hooks and filters to interact with WooCommerce.

If you uninstall the plugin after deactivation, you will lose all your customizations but WooCommerce Poor Guys Swiss Knife will not delete any data captured via customized checkout forms, only the configuration.

= Can I display all the data captured =
Yes, you can configure your added checkout form fields to show up in the administration within the order. The data is displayed on order receipts and emails as well.

If you add item customization forms using WooCommerce Rich Guys Swiss Knife your data will show up in your order inside the administration as well.

== Screenshots ==

1. Settings Page WooCommerce Poor Guys Swiss Knife.
2. WooCommerce Rich Guys Swiss Knife (Due date 2013/12/20)
3. Example form using WooCommerce Poor Guys Swiss Knife and WooCommerce Rich Guys Swiss Knife
 
== Changelog ==

= 1.4.0 =

* Password input type support
* Email input type with validation field if desired
* Additional validation repeater field for fields of type text, password, number, email, etc.
* Minor bug fixes


= 1.3.0 =

* Add captured data in the context of shipping and billing to order receipt and email as this was missing, sorry
* Minor bug fixes

= 1.2.0 =

* Language .mo and .po files for Spanish and German
* Default language .pot file for collaborators who want to help translate WooCommerce Poor Guys Swiss Knife
* Fix compatibility problem with WooCommerce Rich Guys Swiss Knife

= 1.1.2 =
Official Wordpress Release including 3.8. compatibility check

= 1.1.1 =
Add about box with version information

= 1.1.0 =
Public Release based on a complete revision

= 1.0.0 =
Internal usage release

= 0.1.0 =
Development start

== Links ==
* [TakeBarcelona](http://takebarcelona.com): the home of "Tessa Authorship" and more plugins and themes.
* [WooCommerce Poor Guys Swiss Knife](http://takebarcelona.com/woocommerce-poor-guys-swiss-knife/): Home of this plugin.
* [WooCommerce Rich Guys Swiss Knife](http://takebarcelona.com/woocommerce-rich-guys-swiss-knife/): The big brother of this plugin. Most of you will have enough with the little one...
* [Tessa Authorship](http://takebarcelona.com/tessa-authorship/): A tool to reflect WordPress user independent authorship information, biographies.
* [Tessa Theme](http://takebarcelona.com/tessa-theme/): Tessa maximizes content and scales from fullscreen to mobile devices. Tessa is ideal for photography, art and design presentation. "Tessa" has builtin WooCommerce Support and plays nicely with WPML as well.
* [Nicestay](http://www.nicestay.net): Sponsor website that offers short term rentals of apartments for holidays and business in Barcelona, Madrid, Catalonia and the rest of Spain. 

== Updates ==

Updates to the plugin will be posted on the [WooCommerce Poor Guys Swiss Knife homepage](http://takebarcelona.com/woocommerce-poor-guys-swiss-knife/) where you will always find the newest version.

== Thanks ==
To family and friends for support

== Collaboration ==
Whoever wants to work or share his translations, welcome. We will provide i18n po and mo files soon. Thank you! Bugs reports, suggestions and feedback are highly appreciated. Translations for Spanish and German and Catalan will follow.