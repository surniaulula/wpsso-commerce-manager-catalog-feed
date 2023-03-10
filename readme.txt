=== WPSSO Commerce Manager Catalog Feed XML ===
Plugin Name: WPSSO Commerce Manager Catalog Feed XML
Plugin Slug: wpsso-commerce-manager-catalog-feed
Text Domain: wpsso-commerce-manager-catalog-feed
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-commerce-manager-catalog-feed/assets/
Tags: facebook feed, woocommerce product feed, facebook catalog, woocommerce, instagram, wpml, polylang, easy digital downloads
Contributors: jsmoriss
Requires Plugins: wpsso
Requires PHP: 7.2
Requires At Least: 5.4
Tested Up To: 6.1.1
WC Tested Up To: 7.4.1
Stable Tag: 2.3.0

Facebook and Instagram Commerce Manager Catalog Feed XMLs for WooCommerce and custom product pages.

== Description ==

<!-- about -->

**Facebook and Instagram Commerce Manager Catalog Feed XMLs for WooCommerce and custom product pages.**

**E-Commerce Plugin Optional:**

WooCommerce is suggested but not required - the WPSSO Commerce Manager Catalog Feed XML add-on can also retrieve custom product information entered in the Document SSO metabox.

**Complete WooCommerce Support:**

WooCommerce simple products, product variations, product attributes, product meta data, and custom fields are all fully supported.

<!-- /about -->

**Automatic Multilingual Support:**

The Facebook and Instagram product catalog feed XMLs are automatically created in your site's language(s) from Polylang, WPML, or the installed WordPress languages.

After activating the WPSSO Commerce Manager Catalog Feed XML add-on, see the SSO &gt; Facebook Catalog settings page for your feed URLs.

**Facebook and Instagram Commerce Manager Catalog Feed XML Attributes:**

The following XML product attributes are automatically created from your WooCommerce and custom products:

* Additional image link <code>&#91;additional_image_link&#93;</code>
* Age group <code>&#91;age_group&#93;</code>
* Availability <code>&#91;availability&#93;</code>
* Brand <code>&#91;brand&#93;</code> (inluding MPN, UPC, EAN, and ISBN)
* Condition <code>&#91;condition&#93;</code>
* Color <code>&#91;color&#93;</code>
* Description <code>&#91;description&#93;</code>
* Gender <code>&#91;gender&#93;</code>
* Google product category <code>&#91;google_product_category&#93;</code>
* ID <code>&#91;id&#93;</code>
* Image link <code>&#91;image_link&#93;</code>
* Item group ID <code>&#91;item_group_id&#93;</code>
* Link <code>&#91;link&#93;</code>
* Material <code>&#91;material&#93;</code>
* Pattern <code>&#91;pattern&#93;</code>
* Price <code>&#91;price&#93;</code>
* Sale price <code>&#91;sale_price&#93;</code>
* Sale price effective date <code>&#91;sale_price_effective_date&#93;</code>
* Shipping weight <code>&#91;shipping_weight&#93;</code>
* Size <code>&#91;size&#93;</code>
* Title <code>&#91;title&#93;</code>

<h3>WPSSO Core Required</h3>

WPSSO Commerce Manager Catalog Feed XML (WPSSO CMCF) is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which provides complete structured data for WordPress to present your content at its best on social sites and in search results ??? no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Commerce Manager Catalog Feed XML add-on](https://wpsso.com/docs/plugins/wpsso-commerce-manager-catalog-feed/installation/install-the-plugin/).
* [Uninstall the WPSSO Commerce Manager Catalog Feed XML add-on](https://wpsso.com/docs/plugins/wpsso-commerce-manager-catalog-feed/installation/uninstall-the-plugin/).

== Frequently Asked Questions ==

== Screenshots ==

01. The WPSSO CMCF settings page shows a complete list of available XML feed URLs.

== Changelog ==

<h3 class="top">Version Numbering</h3>

Version components: `{major}.{minor}.{bugfix}[-{stage}.{level}]`

* {major} = Major structural code changes and/or incompatible API changes (ie. breaking changes).
* {minor} = New functionality was added or improved in a backwards-compatible manner.
* {bugfix} = Backwards-compatible bug fixes or small improvements.
* {stage}.{level} = Pre-production release: dev &lt; a (alpha) &lt; b (beta) &lt; rc (release candidate).

<h3>Standard Edition Repositories</h3>

* [GitHub](https://surniaulula.github.io/wpsso-commerce-manager-catalog-feed/)
* [WordPress.org](https://plugins.trac.wordpress.org/browser/wpsso-commerce-manager-catalog-feed/)

<h3>Development Version Updates</h3>

<p><strong>WPSSO Core Premium edition customers have access to development, alpha, beta, and release candidate version updates:</strong></p>

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" (for example) version filter for the WPSSO Core plugin and/or its add-ons. When new development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can reselect the "Stable / Production" version filter at any time to reinstall the latest stable version.</p>

<p><strong>WPSSO Core Standard edition users (ie. the plugin hosted on WordPress.org) have access to <a href="https://wordpress.org/plugins/wpsso-commerce-manager-catalog-feed/advanced/">the latest development version under the Advanced Options section</a>.</strong></p>

<h3>Changelog / Release Notes</h3>

**Version 2.4.0-dev.3 (TBD)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.4.
	* WPSSO Core v15.5.0-dev.3.

**Version 2.3.0 (2023/02/14)**

* **New Features**
	* None.
* **Improvements**
	* Updated the CMCF settings page to show a notice when a background task is active.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Refactored the `WpssoCmcfRewrite::template_redirect()` method.
	* Refactored the `WpssoCmcfXml::get()` method.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.4.
	* WPSSO Core v15.3.0.

**Version 2.2.0 (2023/02/11)**

* **New Features**
	* None.
* **Improvements**
	* Updated rewrite rules to add the rules whether they already exist or not.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.4.
	* WPSSO Core v15.2.0.

**Version 2.1.0 (2023/02/04)**

* **New Features**
	* None.
* **Improvements**
	* Changed the rewrite registration hook from 'wp_loaded' to 'init'.
	* Updated feed query arguments to 'feed_name', 'feed_type', and 'feed_locale'.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.4.
	* WPSSO Core v15.0.1.

**Version 2.0.0 (2023/02/03)**

* **New Features**
	* Added support for the new 'product:variants' meta tags array in WPSSO Core v15.0.0.
* **Improvements**
	* Removed the filter hook to sort the WooCommerce variations array (no longer needed).
* **Bugfixes**
	* None.
* **Developer Notes**
	* Removed support for the 'product:offers' meta tags array.
	* Removed the WPSSO_FEED_XML_QUERY_CACHE_DISABLE constant.
	* Removed the 'wpsso_request_url_query_attrs_cache_disable' filter hook.
	* Renamed the `WpssoCmcfActions->get_product_image_url()` method to `check_product_image_urls()`.
	* Refactored the `WpssoCmcfXml::add_feed_product()` method.
	* Refactored the `WpssoCmcfXml::add_product_data()` method.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.4.
	* WPSSO Core v15.0.0.

== Upgrade Notice ==

= 2.4.0-dev.3 =

(TBD) None.

= 2.3.0 =

(2023/02/14) Updated the CMCF settings page to show a notice when a background task is active.

= 2.2.0 =

(2023/02/11) Updated rewrite rules to add the rules whether they already exist or not.

= 2.1.0 =

(2023/02/04) Changed the rewrite registration hook. Updated feed query arguments.

= 2.0.0 =

(2023/02/03) Added support for the new 'product:variants' meta tags array in WPSSO Core v15.0.0.

