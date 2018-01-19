=== Woocommerce Gift Wrapper===
Contributors: littlepackage
Donate link: https://www.paypal.me/littlepackage
Tags: ecommerce, e-commerce, woocommerce, woothemes, woo, gift, present, wrap
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 2.0.6
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Holidays are always coming! Offer your customers gift wrapping, per order, in the WooCommerce cart.

== Description ==

**Features:**

* Create a simple gift wrap option form on the cart and/or checkout page, or go all out with robust gift wrapping offerings
* Set individual prices, descriptions, and images for wrapping types
* Show or hide wrap images in cart
* Static or modal view of giftwrap options on cart page
* Get notice of the customer's intended gift wrap message by email order notification and on the order page
* Fully CSS-tagged for your customizing pleasure.
* If you have suggestions for other features, find a bug, or have a translation, please get in touch.

== Installation ==

= To install plugin =

1. Upload the entire "woocommerce_gift_wrap" folder to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Visit WooCommerce->Settings->Products tab to set your plugin preferences. Look for the "Gift Wrapping" sub tab link.
4. Follow the instructions there and review the settings.

= To remove plugin: =

1. Deactivate plugin through the 'Plugins' menu in WordPress
2. Delete plugin through the 'Plugins' menu in WordPress
3. Your settings will be deleted from your WP database when the plugin is deleted

== Frequently Asked Questions ==

= It doesn't seem to work =
Things to check:

1. Is the plugin activated?
2. Is WooCommerce activated and configured, and are all the theme files current (check WooCommerce->System Status if unsure)
3. Are you using WooCommerce version 2.2.2 or newer? Time to upgrade!
4. Does the your-theme-or-child-theme/woocommerce/cart/cart.php file include the code

`<?php do_action('woocommerce_cart_coupon'); ?>` or
`<?php do_action('woocommerce_before_cart'); ?>` or
`<?php do_action('woocommerce_after_cart'); ?>` or
`<?php do_action('woocommerce_before_checkout_form'); ?>` (for checkout page)

If not, your theme is missing a crucial hook(s) to the functioning of this plugin. Try using a different location for the "Where to Show Gift Wrapping" in the plugin settings.

*Other problem?* Please let me know before leaving negative feedback! I can usually reply to tickets within 24-48 hours.

= Why isn't gift wrapping added when I click the button in the cart? =
Have you added a gift wrapping as a WooCommerce product? This plugin works by creating a product that virtually represents gift wrapping. It is up to you whether that product is visible in the catalog or not, and how fleshed-out you make the product description. But there needs to be a product, and it needs to be in a category whether or not you make more than one wrapping types. That product category is used in the Gift Wrapping settings.

= Why make more than one type of wrapping? =

Maybe you want to offer "Winter Holiday" wrapping and "Birthday" wrapping separately, or maybe you have other types of wrapping paper or boxes you use that may incur different prices or shipping rules. It's up to you whether or not you make more than one wrapping product. You don't have to.

= How can I style the appearance? =
I've added CSS tags to every aspect of the cart forms so you can style away. If you want to streamline your site and speed page-loading, move the CSS to your style.css file and comment out the line in *woocommerce-gift-wrapper.php* that reads: 

`add_action( 'wp_enqueue_scripts', array( &$this, 'gift_load_css_scripts' ));`

= I don't want more than one wrapping added to the cart! =

Yeah, that could be a problem, but rather than hard-code against that possibility I leave the settings to you, and for good reason. If you don't want more than one wrapping possible, make sure to set your wrapping product to "sold individually" under Product Data->Inventory in your Product editor. If you do this make sure your customer has a way to remove the gift wrapping from the cart on small screens, as sometimes responsive CSS designs remove the "Remove from Cart" button from the cart table for small screens.

= Gift wrapping seems to interfere with shipping costs = 

To prevent this happening, I recommend you set up your gift wrap products as WooCommerce "virtual" products (virtual but not downloadable). If setting them up as regular or variable products, make sure to arrange the shipping settings so they don't incur surprise shipping costs.

= I don't want to show gift wrapping in my catalog =

Visit your gift wrap product and set Catalog Visibility to "hidden" in the upper right corner near the blue update button. If you have more than one gift wrap product, do this for each one.

= Can I make the plugin's CSS/JavaScript load on the cart page only? =
Yes. It's a good idea to load scripts conditionally to keep page load times down. You only need the plugin scripts on the WooCommece cart page, so just add the following to your functions.php:

`function wcgiftwrapper_manage_scripts() {
	if ( !is_page( 'cart' ) ) {
		wp_dequeue_script( 'wcgiftwrap-js' );
		wp_dequeue_script( 'wcgiftwrap-css' );
	}
}
add_action( 'wp_enqueue_scripts', 'wcgiftwrapper_manage_scripts', 99 );`

= I don't want to show something =
This plugin is heavily CSS-tagged. If you don't want to show a part of what Gift Wrapper displays, add custom CSS to your Wordpress theme settings, Wordpress theme css (usually style.css), or - better yet - Wordpress child theme CSS file (style.css).

An example might be:

Let's hide the gift note textarea/textbox. Add this CSS to your theme:

`.wc_giftwrap_notes_container textarea{display:none}`

- or -

`.wc_giftwrap_notes{display:none}`

Both would work. I cannot support all the requests for free custom theme help any longer! Please study up CSS or hire a developer to help you make custom theme and plugin modifications. Thank you for understanding.

= The popup doesn't work with my theme =
If your theme uses a Bootstrap modal, there may be a conflict with this plugin.

= This plugin breaks my theme =
I'm sorry I am not responsible nor for hire to make this plugin work with every theme out there. It works with most themes I've tried, both paid and free, and I offer it gratis and as is. If you have suggestions for how to make it work for your theme, I will consider hard-coding in your theme fixes. Thank you for understanding.

= I would like this plugin in my language, or to say something different on screen =
This plugin comes ready with a .POT file. If you aren't already familiar with [localisation (translation) of Wordpress plugins and themes, you can learn more here](https://premium.wpmudev.org/blog/how-to-translate-a-wordpress-plugin/). You can add .PO files to the /lang folder of this plugin to change it to your language, or even to just adjust the English currently used.

To change what this plugin says on screen, create PO/MO file(s) in your language. If your site is in English (US), then you would be creating a PO file called *woocommerce-gift-wrapper-en_US.po* and putting it in the /lang/ folder inside the WooCommerce Gift Wrapper plugin folder (/wp-content/plugins/woocommerce-gift-wrapper/lang/). If your site is in French (France), your PO file would be /wp-content/plugins/woocommerce-gift-wrapper/lang/woocommerce-gift-wrapper-fr_FR.po. Note in this case, you would be editing or overwriting the existing po file for French. 

I recommend [Poedit](https://poedit.net/) to get string translations done quickly and simply.

= Why did the text inputs disappear in version 2.0?=
While I understand that this made translation of the plugin easier for some people, it ultimately made the plugin untranslatable for most people. In order to make the plugin truly localizable, I had to re-write it. For those people who had already customized their text strings, I left those as they were. It is up to you to do a little clean up and upgrade your plugin install if you wish to change those strings now.

The following information is for folks who have been using the plugin since before version 2.0 and edited strings in the old WooCommerce Gift Wrapper settings panel. 

These strings can either be edited via SQL database in the wp_options table, or deleted. The options are:

- giftwrap_header
- giftwrap_button
- giftwrap_text_label

**PLEASE MAKE A DATABASE BACKUP BEFORE RUNNING SQL COMMANDS, ESP. IF YOU ARE UNFAMILIAR WITH SQL**

To delete these options using SQL so you can use translations as mentioned above, run the following SQL command:

`DELETE FROM wp_options WHERE option_name LIKE ('giftwrap_details') OR option_name LIKE ('giftwrap_button') OR option_name LIKE ('giftwrap_text_label');`

If you don't use PHPMyAdmin, or know much SQL, you can use the Wordpress plugin [SQL Executioner](https://wordpress.org/plugins/sql-executioner/) to run this command. If you’ve changed your WP database table prefix, you may need to change wp_ for your own prefix (myprefix_ for example) where it says “wp_options” in the SQL command.

Removing these options frees you up to create translation files. For more on how to do that, read the previous question, above.

To simply change one of these options and call it a day for now, you can run the following SQL command:

`UPDATE wp_options SET giftwrap_header = replace(giftwrap_header, 'Add Gift Wrap to your Order?', 'Add Gift Wrap?');`

This SQL command would change the header to say "Add Gift Wrap?" To change what the button says, use something like:

`UPDATE wp_options SET giftwrap_button = replace(giftwrap_button, 'Submit', 'Add Wrapping!');`

This SQL command would make the button say "Add Wrapping!" Do you see the trend? 

I hope this helps! The goal was to make this plugin as accessible to as many people around the world as possible. I apologize for the hiccup between 1.0 and 2.0, and hope you find the plugin useful!

[Want to submit your language translation for this plugin?](https://translate.wordpress.org/projects/wp-plugins/woocommerce-gift-wrapper)

== Screenshots ==

1. Screenshot of the settings page (WooCommerce -> Settings -> Products -> Gift Wrapping submenu)
2. Screenshot of a modal (a site using the Mystile WooCommerce theme)

== Other Notes ==

I need your support & encouragement! If you have found this FREE plugin useful, and *especially if you have benefitted commercially from it*, please consider donating to support the plugin's future on the web:

[paypal.me/littlepackage](http://paypal.me/littlepackage "Little Package PayPal.me")

I understand you have a budget and might not be able to afford to buy the developer (me) a beer or a slice of pizza in thanks. Maybe you can **leave a positive review**?

[Please leave a review of WooCommerce Gift Wrapper](https://wordpress.org/support/plugin/woocommerce-gift-wrapper/reviews "Leave a Review of Gift Wrapper")

Thank you!

= Translations =

Russian translation by @Balya, 12/2016
Partial Portuguese translation by Sotnas, 8/2017

== Upgrade Notice ==
= 1.0 =
* Initial release
= 1.0.1 =
* Clarifications on settings page to help prevent users making the wrong category invisible
= 1.0.2 =
* Removed setting to hide gift wrap from catalog as it was potentially disruptive if category was set wrong
= 1.0.3 =
* Now compatible with versions of WC < 2.2.2
* Minor CSS fix
= 1.1.0 =
* Finished l10n install
* Added in copyright/fork notice for Gema75
* Modal view in cart
= 1.2.0 =
* Wordpress 4.3 ready
* Woocommerce version < 2.2.2 support removed
* Spanish and French translations
= 1.2.3 =
* Wordpress 4.4 ready
* Option to add more than one gift wrap product to cart
= 1.4 =
* Fixes for <form> tag issues
* Provisional fixes for Avada theme popup/modal conflict issues
* Adds gift wrap note set by customer to WooCommerce order confirmation page
= 2.0 =
* WC Gift Wrapper no longer uses admin-created field labels - you must now use localization (WP translations) to edit plugin strings. If you have saved strings in earlier versions, version 3.0 is backward-compatible but you will not be able to change those strings without creating a .po file.
* WooCommerce 3.0 ready

== Changelog ==
= 1.0 October 29 2014 =
* Initial release

= 1.0.1 November 6 2014 =
* Clarifications on settings page to help prevent users making the wrong category invisible; multi-select may need to be removed.

= 1.0.2 November 6 2014 =
* Removed setting to hide gift wrap from catalog as it was potentially disruptive if category was set wrong

= 1.0.3 December 2 2014 =
* Now compatible with versions of WC < 2.2.2
* Minor CSS fix

= 1.1.0 January 13 2014 =
* Finished l10n install
* Added in copyright/fork notice for Gema75
* Modal view in cart

= 1.2.0 August 12 2015 =
* Wordpress 4.3 ready
* Fixed JS and modal issues (modal was clipped when page was scrolled, JS now loaded in footer)
* JS dialog option when replacing wrapping already in cart
* User notes added below Product name in cart for customer reassurance
* Woocommerce version < 2.2.2 support removed
* Spanish and French translations

= 1.2.1 November 8 2015 =
* CSS fixes (remove Bootstrap general CSS)
* Modal product listing fixes
* Settings link from plugin page

= 1.2.2 December 6 2015 =
* Frontend accessibility improvements

= 1.2.3 December 11 2015 =
* Checks for WP 4.4
* Option to add more than one gift wrap product to cart

= 1.2.4 December 21 2015 =
* CSS fix for modal (pop-up) in cart - textarea label display

= 1.2.5 June 4 2016 =
* Checks for compatibility with WP 4.5.2, WC 2.5.5

= 1.2.6 June 7 2016 =
* Increased z-index on popup modal to help with theme/plugin conflicts

= 1.2.7 August 1 2016 =
* Checks for compatibility with WP 4.6
* Style and script changes for Divi theme
* Update Bootstrap modal to version 3.3.7
* Make modal accessible

= 1.2.8 August 5 2016 =
* Further simple CSS fixes for Divi and Avada theme, others (future plans to fix Bootstrap conflict with Avada theme)

= 1.2.9 September 29 2016 =
* Feature: Textarea MAXLENGTH setting 
* Fix: Modal compatibility with Avada theme (e.g. not load Bootstrap twice; still problems with DOM)

= 1.3.0 October 28 2016 =
* Feature: Gift wrapping options now on Checkout page
* Feature: Put gift wrapping prompts in one, or more than one place
* Feature: More CSS tags for Cart page
* Fix: CSS clear:both for textarea header
* Full Avada compatability with a bootstrap modal form is near-impossible. Dropping support for now.

= 1.3.1 October 28 2016 =
* Fix: Minor PHP error thrown

= 1.3.2 November 30 2016 =
* Fix: Text domain use string not variable

= 1.3.3 January 3 2017 =
* Feature: Russian translation by @Balya ( http://webkit.pro/ )
* Feature: Added rough Spanish translation back in
* Feature: Woocommerce 2.7 ready
* Feature: Delete DB options on plugin uninstall
* Fix: accessing Product object directly throws warnings (line 370 woocommerce-gift-wrapper.php)

= 1.4 March 16 2017 =
* Fix: Omit open and closing &lt;form&gt; tags on modal and embedded gift wrap forms
* Fix: only run JS console once if two gift wrap locations on page
* Feature: Add gift wrap notes to WooCommerce Thank You page.
* Tweak: Closer to Avada modals working (not complete)
* Tweak: Move settings and admin functions into their own class files

= 2.0 March 31 2017 =
* Feature: Woo 3.0 ready
* Feature: move toward correct WP l18n, rather than user-created strings (plugin still accept/use strings saved in previous versions)
* Fix: <form> tags for modal below cart
* Fix: Duplicate div IDs "wc-giftwrap" in cart
* Fix: Duplicate radio IDs in modals (if two used in cart)
* Tweak: Escaping and sanitizing code
* Tweak: Code revised/evolved, Gema75 copyright updated

= 2.0.1 March 31 2017 =
* Fix: Correct text domain to match plugin slug for l18n

= 2.0.2 April 21 2017 =
* Fix: Don't access order properties directly in /classes/class-gift-wrapper-admin.php

= 2.0.3 April 27 2017 =
* Fix: Provision for smart folks who don't want to upgrade to Woo 3.0 yet

= 2.0.4 June 14 2017 =
* Fix: Translation strings not translating when set within variable. 
* Feature: Readme.txt edited to further explain how to translate/edit plugin text strings.

= 2.0.5 August 19 2017 =
* Feature: Option to add gift wrap before cart added, thank you @Sotnas
* Feature: Portuguese translation added

= 2.0.6 November 21 2017 =
* Fix: Do not show out-of-stock gift wrap items
* Fix: JS naming conflict between above cart and below checkout slide-out and modal where below checkout modal not working
* Fix: WC sessions cleared after order complete
