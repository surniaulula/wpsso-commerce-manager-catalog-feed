=== WPSSO Commerce Manager Catalog Feed XML ===
Plugin Name: WPSSO Commerce Manager Catalog Feed XML
Plugin Slug: wpsso-commerce-manager-catalog-feed
Text Domain: wpsso-commerce-manager-catalog-feed
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-commerce-manager-catalog-feed/assets/
Tags: facebook, instagram, woocommerce, wpml, polylang
Contributors: jsmoriss
Requires Plugins: wpsso
Requires PHP: 7.4.33
Requires At Least: 5.9
Tested Up To: 6.8.2
WC Tested Up To: 10.0.4
Stable Tag: 4.13.0

Meta (Facebook and Instagram) Commerce Manager Catalog Feed XMLs for WooCommerce and custom product pages.

== Description ==

<!-- about -->

**Meta (Facebook and Instagram) Commerce Manager Catalog Feed XMLs for WooCommerce and custom product pages.**

**E-Commerce Plugin Optional:**

WooCommerce is suggested but not required - the WPSSO Commerce Manager Catalog Feed XML add-on can also use product information from custom product pages.

**Complete WooCommerce Support:**

WooCommerce simple products, product variations, product attributes, product meta data, and custom fields are all fully supported.

<!-- /about -->

**Automatic Multilingual Support:**

The Meta (Facebook and Instagram) product catalog feed XMLs are automatically created in your site's language(s) from Polylang, qTranslate-XT, WPML, or the installed WordPress languages.

After activating the WPSSO Commerce Manager Catalog Feed XML add-on, see the SSO &gt; Facebook Commerce Manager settings page for your feed URLs.

**Meta (Facebook and Instagram) Commerce Manager Catalog Feed XML Attributes:**

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

WPSSO Commerce Manager Catalog Feed XML is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which creates extensive and complete structured data to present your content at its best for social sites and search results – no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

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

**Version 4.13.0 (2025/06/22)**

* **New Features**
	* None.
* **Improvements**
	* Added information about product inclusion criteria in the SSO &gt; Facebook Commerce Manager settings page.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v21.0.0.

**Version 4.12.0 (2025/04/09)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Added a new `WPSSOCMCF_ADMIN_FEED_XML_STATS` constant (false by default).
	* Removed the `WPSSOCMCF_XML_INFO_DISABLE` constant.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v18.20.0.

**Version 4.11.0 (2024/12/11)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Updated vendor/sabre/uri v2.3.4 to v3.0.2.
	* Updated vendor/sabre/xml v2.2.11 to v4.0.6.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v18.18.2.

**Version 4.10.0 (2024/12/01)**

* **New Features**
	* None.
* **Improvements**
	* Added a check for the required PHP XMLReader and XMLWriter extensions.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Updated vendor/sabre/uri v2.3.3 to v2.3.4.
	* Updated vendor/sabre/xml v2.2.6 to v2.2.11.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v18.18.2.

== Upgrade Notice ==

= 4.13.0 =

(2025/06/22) Added information about product inclusion criteria in the SSO &gt; Facebook Commerce Manager settings page.

= 4.12.0 =

(2025/04/09) Added a new `WPSSOCMCF_ADMIN_FEED_XML_STATS` constant (false by default).

= 4.11.0 =

(2024/12/11) Updated vendor/sabre/uri and vendor/sabre/xml.

= 4.10.0 =

(2024/12/01) Added a check for the required PHP XMLReader and XMLWriter extensions.

