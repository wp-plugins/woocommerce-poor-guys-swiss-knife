=== WooCommerce Poor Guys Swiss Knife ===
Contributors: ulih
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJ4K2X953H8CC
Tags: WooCommerce checkout manager, WooCommerce cart manager, file upload, color picker, WooCommerce color picker, WooCommerce file upload, cart manager, checkout manager, checkout, cart, shop, WooCommerce, shop settings, cart settings, checkout settings, variations bulk manager, variations manager, minimum items, maximum items, quantity input, product quantities, incremental quantities, minimum quantity, maximum quantity, wholesale, checkout personalization, checkout form, checkout customization, custom forms, custom fields, confirmation, confirmation fields, cart button, payment gateways, payment gateways customization, gateways, shipping, field editor, field, buy, pay, bulk management, variations, variation extender, custom fields per product, custom fields per variation, checkout localization
Requires at least: Wordpress 3.1 and WooCommerce 2.0
Tested up to: 3.9.0
Stable tag: 1.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Swiss Knife for WooCommerce

== Description ==

WooCommerce Poor Guys Swiss Knife powerloads your standard WooCommerce installation. You can fine-tune your shop, customize your checkout forms, adapt your shop for country specific needs, set rules for cart operations and a lot more.

WooCommerce Poor Guys Swiss Knife comes packaged with real value and no restrictions. The big brother WooCommerce Rich Guys Swiss Knife offers additional stuff and features.

= Most important WooCommerce Poor Guys Swiss Knife Features =

Checkout / My Account:

* WooCommerce Checkout form customization (works also for billing and shipping fields on the customer's "My Account" page)
* Drag and Drop for all Checkout form fields to order all fields (WooCommerce build-in fields and your custom fields)
* Support for all input types and data (select, checkbox, radio, text, textarea, date picker, time picker, number ranges, password, email) without restrictions
* Full i18n support to allow localization and translation of all labels, placeholders
* Remove WooCommerce build-in fields
* Manage required state for all fields
* Handle minimum and maximum calendar offset for date fields
* Separate build-in billing and shipping fields from your own custom fields adding a custom title
* Manage build-in and custom field alignment and span for each field (left, right, full)
* Add second email validator field for build-in email field
* Add second validator field to custom input fields for passwords, additional email inputs, etc. 
* Hide captured data in emails and receipts on a per field basis
* Load values and options into inputs and selects via custom javascript

Localization:

* Configure and handle behaviour of vital build in fields for enabled countries (checkout form reconfigures dynamically when customer switches billing or shipping country and this allows you to handle labels and placeholders for enabled countries)

General:

* Set labels for shop buttons (Add to Cart, Read more, Select options, etc.) 
* Manage available payment gateways on a per product basis with intelligent filter to show the most restrictive combination when a customer has more than one product in his cart

Cart:

* Set required minimum / allowed maximum and incremental steps globally for all products based on product type
* Set minimum, maximum and incremental steps on a per product basis
* Switch off quantity input for product types

= Features available with WooCommerce Rich Guys Swiss Knife (WCRGSK) =

* Checkout personalization on a per product and variation basis
* Attach another general form section to checkout form apart from billing, shipping and comment section
* Manage order comments section (add fields, manage title, label and placeholder for comment textarea or hide section)
* Variation bulk manager to apply configurations to variations based on filters
* Add Variation description
* Html injects (Add whatever you want in between your checkout form fields)
* Color picker field
* Acceptable terms and conditions on per product basis
* File uploads for checkout form sections and on a per product basis
* Product terms and conditions

Data captured via customized (added) form fields is available within your order administration and will be added in the appropriate sections of the individual orders. What shows up can be configured for each individual custom field. Data captured will be shown to the customer on the order receipt page and in emails.

WooCommerce Poor Guys Swiss Knife has been developed in cooperation with [Nicestay](http://www.nicestay.net) to set up a transfer and shuttle service website related with their short term rental business of apartments in Barcelona.

The companion plugin WooCommerce Rich Guys Swiss Knife allows you to add more tools to your swiss knife like custom fields on a per product and per variation basis for checkout forms and a lot more.

= What's new? =
	
* Now includes translations for German, Brazilian Portuguese and Spanish.
* WooCommerce Checkout Localization Management for core WooCommerce Fields
* Date Format for date picker inputs

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

= WooCommerce Checkout Localization =

* Let WooCommerce handle localization configuration for core address fields: address_1, address_2, state, postcode, city
* Decide which fields will be localized when customer switches billing or shipping country and which will use your base configuration
* Configure behaviour of localized fields for every single country
 
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

= Can I translate all labels and placeholders = 
Yes, WCPGSK allows you to translate all labels and placeholders

= After installation labels and placeholder do not show up. What can I do? = 
Please save the WooCommerce Poor Guys Swiss Knife Settings page (WooCommerce -> WooCommerce Poor Guys Swiss Knife) at least once.
(Starting with version 1.6.2 it's not necessary anymore to save the WooCommerce Poor Guys Swiss Knife settings after installation and first activation.)

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

= 1.9.1 =

* Fix script problems caused by missing jquery library (sorry for that)

= 1.9.0 =

* Improve date field handling by adding configuration options to exclude days of week, weekends and holidays
* Improve single checkbox handling by avoiding unwanted checkbox checked on first (single) checkbox
* Better load of javascript code
* Fix storage problem of user javascript code
* Fix product editor error when "Enable Payment Gateways Configuration" is enabled under "Shop Settings"

= 1.8.4 =

* Fix problem for select fields with presentation select and option multiple enabled. Now data is captured and stored correctly.

= 1.8.3 =

* Fix obsolete warning for wcpgsk_session as reported by one user
* Add support for basic date formats
* Add validation for max and min dates (if value is date and not integer)

= 1.8.2 =

* Enable special handling for empty line when converting to radio buttons and checkboxes from select, which allows to have all resulting radios and checkboxes unchecked
* Allow fixed date range for date fields
* Fix support for empty labels and placeholders for core billing and shipping fields (partially as switching of country will load country specific defaults and layout (address, post-code, town) into the checkout form. This may be addressed in a future update.)

= 1.8.1 =

* Fix sold individually problem which was not respecting the product configuration when applying the type configuration
* Fix quantity input for grouped products allowing to set individual products to 0
* Add support for custom checkout script (via file and via database)

= 1.8.0 =

* Second maintenance release for WooCommerce 2.1+
* Implement configuration localization for address, postcode, state and city fields
* Fix and improve button label handling for WooCommerce 2.1+
* Fix problems with quantity inputs for WC 2.1+ installations
* Fix problems with asterisks and labels for fields that support localization
* Brazilian Portuguese Translation

= 1.7.0 =

* Maintenance release for WooCommerce 2.1.0+
* Fix problems for Payment Gateways Configuration for WC 2.1+ installations
* Fix problems for WooCommerce Messages and Errors with WC 2.1+ installations
* Fix problems with quantity inputs for WC 2.1+ installations
* Maintain backward compatibility for WC < 2.1 installations
* Fix validation support for custom fields

= 1.6.2 =

* Add basic reflection of configuration settings for label and description (placeholder) for default Billing and Shipping fields to Wordpress User Profiles
* Fix display problem for default WooCommerce fields after initial plugin activation which required to save the settings of WooCommerce Poor Guys Swiss Knife at least once
* Fix quantity input for individual products grouped together to display on a grouped product page

= 1.6.1 =

* Fix problem with required setting for default woocommerce fields that are not required

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
* [Tessa Theme](http://takebarcelona.com/tessa-theme/): Tessa maximizes content and scales gracefully from mobile devices to desktop fullscreen. Tessa is ideal for photography, art and design presentation. "Tessa" has builtin WooCommerce Support and plays nicely with WPML as well.
* [Nicestay](http://www.nicestay.net): Sponsor website that offers short term rentals of apartments for holidays and business in Barcelona, Madrid, Catalonia and the rest of Spain. 

== Updates ==

Updates are available via WordPress plugin directory. Additional information about the plugin is available on [WooCommerce Poor Guys Swiss Knife homepage](http://takebarcelona.com/woocommerce-poor-guys-swiss-knife/).

== Thanks ==
To family and friends for support

== Collaboration ==
Whoever wants to work or share his translations, welcome. We will provide i18n po and mo files soon. Thank you! Bugs reports, suggestions and feedback are highly appreciated..

== Credits ==
@samirbridi for the Brazilian Portuguese translation