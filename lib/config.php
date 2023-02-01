<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfConfig' ) ) {

	class WpssoCmcfConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssocmcf' => array(			// Plugin acronym.
					'version'     => '2.0.0-dev.5',	// Plugin version.
					'opt_version' => '1',		// Increment when changing default option values.
					'short'       => 'WPSSO CMCF',	// Short plugin name.
					'name'        => 'WPSSO Commerce Manager Catalog Feed XML',
					'desc'        => 'Facebook and Instagram Commerce Manager Catalog Feed XMLs for WooCommerce, Easy Digital Downloads, and Custom Product Pages.',
					'slug'        => 'wpsso-commerce-manager-catalog-feed',
					'base'        => 'wpsso-commerce-manager-catalog-feed/wpsso-commerce-manager-catalog-feed.php',
					'update_auth' => '',		// No premium version.
					'text_domain' => 'wpsso-commerce-manager-catalog-feed',
					'domain_path' => '/languages',

					/*
					 * Required plugin and its version.
					 */
					'req' => array(
						'wpsso' => array(
							'name'          => 'WPSSO Core',
							'home'          => 'https://wordpress.org/plugins/wpsso/',
							'plugin_class'  => 'Wpsso',
							'version_const' => 'WPSSO_VERSION',
							'min_version'   => '15.0.0-dev.5',
						),
					),

					/*
					 * URLs or relative paths to plugin banners and icons.
					 */
					'assets' => array(

						/*
						 * Icon image array keys are '1x' and '2x'.
						 */
						'icons' => array(
							'1x' => 'images/icon-128x128.png',
							'2x' => 'images/icon-256x256.png',
						),
					),

					/*
					 * Library files loaded and instantiated by WPSSO.
					 */
					'lib' => array(
						'submenu' => array(
							'cmcf-general' => 'Facebook Catalog',
						),
					),

					/*
					 * Declare compatibility with WooCommerce HPOS.
					 *
					 * See https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book.
					 */
					'wc_compat' => array(
						'custom_order_tables',
					),
				),
			),
			'opt' => array(
				'defaults' => array(
					'cmcf_img_width'  => 1200,
					'cmcf_img_height' => 628,
					'cmcf_img_crop'   => 1,
					'cmcf_img_crop_x' => 'center',
					'cmcf_img_crop_y' => 'center',
				),
			),
			'head' => array(

				/*
				 * For carousel ads, collection ads and Shops: Product images display in square (1:1) format. The
				 * minimum image size is 500 x 500 px. We recommend 1024 x 1024 px for best quality.
				 *
				 * For single image ads: Product images display at a 1.91:1 aspect ratio. The minimum image size is
				 * 500 x 500 px. We recommend 1200 x 628 px for best quality.
				 *
				 * See https://www.facebook.com/business/help/686259348512056?id=725943027795860.
				 */
				'limit_min' => array(
					'cmcf_img_width'  => 500,
					'cmcf_img_height' => 500,
				),

				/*
				 * See https://www.facebook.com/business/help/120325381656392?id=725943027795860.
				 */
				'cmcf_content_map' => array(	// Element of 'head' array.

					/*
					 * The current availability of the item. Supported values: in stock, out of stock. Out of
					 * stock items don't appear in ads, which prevents advertising items that aren't available.
					 * They do still appear in shops on Facebook and Instagram, but are marked as sold out.
					 */
					'product:availability' => array(
						'https://schema.org/BackOrder'           => 'out of stock',
						'https://schema.org/Discontinued'        => 'out of stock',
						'https://schema.org/InStock'             => 'in stock',
						'https://schema.org/InStoreOnly'         => 'in stock',
						'https://schema.org/LimitedAvailability' => 'in stock',
						'https://schema.org/OnlineOnly'          => 'in stock',
						'https://schema.org/OutOfStock'          => 'out of stock',
						'https://schema.org/PreOrder'            => 'in stock',
						'https://schema.org/PreSale'             => 'in stock',
						'https://schema.org/SoldOut'             => 'out of stock',
					),

					/*
					 * The condition of the item. Supported values: new, refurbished, used.
					 */
					'product:condition' => array(
						'https://schema.org/DamagedCondition'     => 'used',
						'https://schema.org/NewCondition'         => 'new',
						'https://schema.org/RefurbishedCondition' => 'refurbished',
						'https://schema.org/UsedCondition'        => 'used',
					),
				),
			),
			'wp' => array(
				'file' => array(
					'wpsso_f_' => array(
						'label'  => 'Commerce Manager Catalog Feed XML',
						'value'  => WEEK_IN_SECONDS,
						'filter' => 'wpsso_cache_expire_cmcf_xml',	// See WpssoUtil->get_cache_exp_secs().
					),
				),
			),
		);

		public static function get_version( $add_slug = false ) {

			$info =& self::$cf[ 'plugin' ][ 'wpssocmcf' ];

			return $add_slug ? $info[ 'slug' ] . '-' . $info[ 'version' ] : $info[ 'version' ];
		}

		public static function set_constants( $plugin_file ) {

			if ( defined( 'WPSSOCMCF_VERSION' ) ) {	// Define constants only once.

				return;
			}

			$info =& self::$cf[ 'plugin' ][ 'wpssocmcf' ];

			/*
			 * Define fixed constants.
			 */
			define( 'WPSSOCMCF_FILEPATH', $plugin_file );
			define( 'WPSSOCMCF_PLUGINBASE', $info[ 'base' ] );	// Example: wpsso-commerce-manager-catalog-feed/wpsso-commerce-manager-catalog-feed.php.
			define( 'WPSSOCMCF_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_file ) ) ) );
			define( 'WPSSOCMCF_PLUGINSLUG', $info[ 'slug' ] );	// Example: wpsso-commerce-manager-catalog-feed.
			define( 'WPSSOCMCF_URLPATH', trailingslashit( plugins_url( '', $plugin_file ) ) );
			define( 'WPSSOCMCF_VERSION', $info[ 'version' ] );

			/*
			 * Define variable constants.
			 */
			self::set_variable_constants();
		}

		public static function set_variable_constants( $var_const = null ) {

			if ( ! is_array( $var_const ) ) {

				$var_const = (array) self::get_variable_constants();
			}

			/*
			 * Define the variable constants, if not already defined.
			 */
			foreach ( $var_const as $name => $value ) {

				if ( ! defined( $name ) ) {

					define( $name, $value );
				}
			}
		}

		public static function get_variable_constants() {

			$var_const = array();

			$var_const[ 'WPSSOCMCF_PAGENAME' ] = 'commerce-manager-catalog';

			/*
			 * Maybe override the default constant value with a pre-defined constant value.
			 */
			foreach ( $var_const as $name => $value ) {

				if ( defined( $name ) ) {

					$var_const[ $name ] = constant( $name );
				}
			}

			return $var_const;
		}

		/*
		 * Require library files with functions or static methods in require_libs().
		 *
		 * Require and instantiate library files with dynamic methods in init_objects().
		 */
		public static function require_libs( $plugin_file ) {

			require_once WPSSOCMCF_PLUGINDIR . 'vendor/autoload.php';
			require_once WPSSOCMCF_PLUGINDIR . 'lib/register.php';
			require_once WPSSOCMCF_PLUGINDIR . 'lib/rewrite.php';	// Static methods required by WpssoCmcfRegister->activate_plugin().
			require_once WPSSOCMCF_PLUGINDIR . 'lib/xml.php';

			add_filter( 'wpssocmcf_load_lib', array( __CLASS__, 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $success = false, $filespec = '', $classname = '' ) {

			if ( false !== $success ) {

				return $success;
			}

			if ( ! empty( $classname ) ) {

				if ( class_exists( $classname ) ) {

					return $classname;
				}
			}

			if ( ! empty( $filespec ) ) {

				$file_path = WPSSOCMCF_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {

						$classname = SucomUtil::sanitize_classname( 'wpssocmcf' . $filespec, $allow_underscore = false );
					}

					return $classname;
				}
			}

			return $success;
		}
	}
}
