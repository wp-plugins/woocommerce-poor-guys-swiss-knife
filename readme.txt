=== WooCommerce Poor Guys Swiss Knife ===
Contributors: ulih
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJ4K2X953H8CC
Tags: WooCommerce checkout manager, WooCommerce cart manager, cart manager, checkout manager, checkout, cart, shop, WooCommerce, shop settings, cart settings, checkout settings, variations bulk manager, variations manager, minimum items, maximum items, quantity input, product quantities, incremental quantities, minimum quantity, maximum quantity, wholesale, checkout personalization, checkout form, checkout customization, custom forms, custom fields, confirmation, confirmation fields, cart button, payment gateways, payment gateways customization, gateways, shipping, field editor, field, buy, pay, bulk management, variations, variation extender, custom fields per product, custom fields per variation
Requires at least: 3.1
Tested up to: 3.8
Stable tag: 1.6.0
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

WooCommerce Poor Guys Swiss Knife includes, starting with v.1.4.0 the possibility to enable confirmation fields, useful in the context of added email and password custom fields, for instance. The standard email validation field for the email field supplied by WooCommerce within the billing section has to be activated like before.

With version 1.6.0 minimum, maximum and incremental steps for product quantities can be defined globally for product types (simple, variable, etc.) and on a per product basis which allows better adaptation according to store necessities.

The companion plugin WooCommerce Rich Guys Swiss Knife allows you to add more tools to your swiss knife like custom fields on a per product and per variation basis for checkout forms and a lot more.

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
* Activate min/max/step settings for individual products (configuration for values available on edit page for simple and variable products)
* Set minimum and maximum and incremental steps for quantities required or allowed in the context of product types (simple, variable, variations, etc.)

= Checkout Settings =

* Configure default offset and maximum offset for date fields you add to your checkout forms (can be overwritten in the configuration of each date field)
* Add a billing email validator field to the billing section to assure that the user does not misspell his email
 
= Checkout Billing and Shipping Form Sections =

* Order existing and added fields
* Configure required status
* Add new fields (support for date, time, text (email, password, etc.), textarea, number (simple or as ranges) and selections. Selections can be configured to display as dropdown and muliple selects, radio buttons or checkboxes)
* Decide if data captured from a custom field will be shown in the order within the administration and in emails and customer order pages like receipts
* Add confirmation fields for passwords and emails and other fields to oblige the user to validate his input repeating it
* Define labels and placeholders
* Set the css display class which allows you to rearrange your form (wide (full row), first or last element in row)
* Configurator for custom field (allows you to fine-tune your custom fields and add values, limitations or validation to your custom fields)

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

= More Tools =

More Tools for your WooCommerce Swiss Knife are available with the Rich Guys Swiss Knife for WooCommerce:

* Item personalization during checkout
* Custom fields on a per product and per variation basis using Item personalization options
* Bulk operations for variations
* Variation Extender which allows to attach virtual variations meaningful to customers and to map these variations back to a more reduced set of internal variations. This allows you to overcome the resource (runtime) and management limits that most e-commerce stores show in the context of variable products.


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

With WooCommerce Poor Guys Swiss Knife you have 11 diferent custom field types available or directly or via the configuration of the main types.

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

= My custom field data does not show up in emails and receipts. How can I fix this? =
You have to reconfigure what was formerly "Show in order". From version 1.5.0 onwards you have to hide the data. This is easier as the user and you expects his data to be visible by default.

= Can I reconfigure the WooCommerce standard fields? =
Yes, you can change the required status, label and placeholders or even remove the built-in WooCommerce fields, but please test your changes. Especially the email, country and postcode fields can be vital for the correct functioning of your WooCommerce instance.

== Screenshots ==

1. Settings Page WooCommerce Poor Guys Swiss Knife.
2. WooCommerce Rich Guys Swiss Knife (Due date 2013/12/20)
3. Example form using WooCommerce Poor Guys Swiss Knife and WooCommerce Rich Guys Swiss Knife
 
== Changelog ==

= 1.6.0 =

* Added global incremental step setting to existing min and max settings 
* Added Min/Max/Step configuration on a per product basis
* Quantity input can be converted to select for individual products (simple or variable) based on the settings for min/max/step of the product
* Add configuration tab to simple and variable products for min/max and incremental steps for quantities

= 1.5.4 =

* Usage of internal woocommerce 2.0 session object instead of helper class
* Prevent warning message depending on php configurations by checking object first and recreating it if necessary.

= 1.5.3 =

* Additional fix for "Edit my address" for removed standard WooCommerce fields which still triggered required validation in nearly all cirumstances.
* Fake valid postcode for US, UK, CH to bypass the woocommerce validation for zip/postcode if country is set but zip/postcode marked for remove
* Allow for empty billing and shipping address

= 1.5.2 =

* Fix a problem for repeater fields when original field has required attribute. Required for repeater fields collides with WooCommerce as the repeater field validates but is not present in the field range as we add it as a "virtual" field only present during checkout with our own validation
* Add WooCommerce Version check to assure 2.0+ versions of WooCommerce
* Enhancements for billing and shipping forms for logged-in customers which edit their addresses via "Edit my address". Fields presented respect now most of the settings for checkout forms.

= 1.5.1 =

* Remove/hide fields according to configuration for logged-in customers who wish to edit their billing or shipping address 

= 1.5.0 =

* Min and Max input for items in cart and individual item quantities can now be turned off setting 0 or nothing in the administration. All Min and Max values will default to 0
* Allow to set title for additional billing and shipping data for order receipts and emails
* Display data for custom field data of sub-type password as a series of * in order receipts, emails and validation on submit
* Minor code revisions as result of running unit tests on both WooCommerce Poor Guys Swiss Knife and WooCommerce Rich Guys Swiss Knife 
* Fix compatitibility problem of ajax add to cart when WooCommerce Rich Guys Swiss Knife is active
* Switched behaviour "Show in Order" to "Hide in Emails/Receipts" as this is more compliant with user interface expectations. This makes it necessary to reconfigure your custom fields, but only if you do not want to show a custom field, sorry.
* Compatibility tests to prepare launch of WooCommerce Rich Guys Swiss Knife

= 1.4.2 =

* Fix another error on activation

= 1.4.1 =

* Fix activation error

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