<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

use Vitalybaev\GoogleMerchant\Feed;
use Vitalybaev\GoogleMerchant\Product;
use Vitalybaev\GoogleMerchant\Product\Shipping;

if ( ! class_exists( 'WpssoCmcfXml' ) ) {

	class WpssoCmcfXml {

		/*
		 * Clear the feed XML cache files.
		 *
		 * See WpssoCmcfActions->action_refresh_post_cache().
		 */
		static public function clear_cache( $request_locale = null, $request_type = 'feed' ) {

			$wpsso =& Wpsso::get_instance();

			if ( ! $request_locale ) {

				$request_locale = SucomUtil::get_locale();
			}

			$cache_salt     = __CLASS__ . '::get(locale:' . $request_locale . '_type:' . $request_type . ')';
			$cache_file_ext = '.xml';

			$wpsso->cache->clear_cache_data( $cache_salt, $cache_file_ext );	// Clear the feed XML cache file.
		}

		static public function get( $request_locale = null, $request_type = 'feed' ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$original_locale = SucomUtil::get_locale();
			$current_locale  = $original_locale;
			$request_locale  = $request_locale ? $request_locale : $current_locale;
			$is_switched     = false;

			if ( $request_locale !== $current_locale ) {

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log( 'switching to request locale ' . $request_locale );
				}

				$is_switched    = switch_to_locale( $request_locale );
				$current_locale = SucomUtil::get_locale();	// Update the current locale value.

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log( 'switch to locale ' . ( $is_switched ? 'successful' : 'failed' ) );
				}
			}

			$cache_md5_pre  = 'wpsso_f_';
			$cache_type     = 'file';
			$cache_salt     = __CLASS__ . '::get(locale:' . $request_locale . '_type:' . $request_type . ')';
			$cache_file_ext = '.xml';
			$cache_exp_secs = $wpsso->util->get_cache_exp_secs( $cache_md5_pre, $cache_type );

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log( 'original locale = ' . $original_locale );
				$wpsso->debug->log( 'request locale = ' . $request_locale );
				$wpsso->debug->log( 'current locale = ' . $current_locale );
				$wpsso->debug->log( 'cache expire = ' . $cache_exp_secs );
				$wpsso->debug->log( 'cache salt = ' . $cache_salt );
			}

			if ( $cache_exp_secs ) {

				$xml = $wpsso->cache->get_cache_data( $cache_salt, $cache_type, $cache_exp_secs, $cache_file_ext );

				if ( false !== $xml ) {

					if ( $is_switched ) {

						restore_previous_locale();
					}

					return $xml;
				}
			}

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log( 'creating new feed' );
			}

			$site_title = SucomUtil::get_site_name( $wpsso->options, $current_locale );
			$site_url   = SucomUtil::get_home_url( $wpsso->options, $current_locale );
			$site_desc  = SucomUtil::get_site_description( $wpsso->options, $current_locale );
			$rss2_feed  = new Vitalybaev\GoogleMerchant\Feed( $site_title, $site_url, $site_desc, '2.0' );
			$query_args = array( 'meta_query' => self::get_meta_query() );

			$public_ids = WpssoPost::get_public_ids( $query_args );

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log( 'adding ' . count( $public_ids ) . ' public ids' );

				$wpsso->debug->log_arr( 'public_ids', $public_ids );
			}

			foreach ( $public_ids as $post_id ) {

				$mod = $wpsso->post->get_mod( $post_id );

				if ( $mod[ 'is_archive' ] ) {	// Exclude the shop archive page.

					if ( $wpsso->debug->enabled ) {

						$wpsso->debug->log( 'skipping post id ' . $post_id . ': post is an archive page' );
					}

					continue;
				}

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log( 'getting open graph array for ' . $mod[ 'name' ] . ' id ' . $mod[ 'id' ] );
				}

				$mt_og = $wpsso->og->get_array( $mod, $size_names = 'wpsso-cmcf', $md_pre = array( 'cmcf', 'og' ) );

				if ( ! empty( $mt_og[ 'product:variants' ] ) && is_array( $mt_og[ 'product:variants' ] ) ) {

					if ( $wpsso->debug->enabled ) {

						$wpsso->debug->log( 'adding ' . count( $mt_og[ 'product:variants' ] ) . ' variants for post id ' . $post_id );
					}

					foreach ( $mt_og[ 'product:variants' ] as $num => $mt_single ) {

						if ( $wpsso->debug->enabled ) {

							$wpsso->debug->log( 'adding variant #' . $num . ' for post id ' . $post_id );
						}

						self::add_feed_product( $rss2_feed, $mt_single );
					}

				} else {

					if ( $wpsso->debug->enabled ) {

						$wpsso->debug->log( 'adding product for post id ' . $post_id );
					}

					self::add_feed_product( $rss2_feed, $mt_og );
				}
			}

			$xml = $rss2_feed->build();

			if ( $cache_exp_secs ) {

				$wpsso->cache->save_cache_data( $cache_salt, $xml, $cache_type, $cache_exp_secs, $cache_file_ext );
			}

			if ( $is_switched ) {

				restore_previous_locale();
			}

			return $xml;
		}

		/*
		 * See https://developer.wordpress.org/reference/classes/wp_meta_query/.
		 */
		static private function get_meta_query() {

			static $local_cache = null;

			if ( null === $local_cache ) {

				$local_cache  = '';	// Default WP_Query value is an empty string.
				$og_type_key  = WpssoAbstractWpMeta::get_column_meta_keys( 'og_type' );
				$noindex_key  = WpssoAbstractWpMeta::get_column_meta_keys( 'is_noindex' );
				$redirect_key = WpssoAbstractWpMeta::get_column_meta_keys( 'is_redirect' );

				$local_cache = array(
					'relation' => 'AND',
					array(
						'key'     => $og_type_key,
						'value'   => 'product',
						'compare' => '=',
						'type'    => 'CHAR',
					),
					array(
						'key'     => $noindex_key,
						'value'   => '1',
						'compare' => '!=',
						'type'    => 'CHAR',
					),
					array(
						'key'     => $redirect_key,
						'value'   => '1',
						'compare' => '!=',
						'type'    => 'CHAR',
					),
				);
			}

			return $local_cache;	// Return an empty string or array.
		}

		static private function add_feed_product( &$rss2_feed, array $mt_single ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$product = new Vitalybaev\GoogleMerchant\Product();

			self::add_product_data( $product, $mt_single );

			self::add_product_images( $product, $mt_single );

			$rss2_feed->addProduct( $product );
		}

		/*
		 * See https://www.facebook.com/business/help/120325381656392?id=725943027795860.
		 */
		static private function add_product_data( &$product, $mt_single ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			self::sanitize_mt_array( $mt_single );

			$names = array(

				/*
				 * Required fields for products.
				 *
				 * Do not use the 'setAvailability' method as Facebook and Google use different sanitized
				 * availability values.
				 */
				'og:title'                 => 'setTitle',
				'og:description'           => 'setDescription',
				'og:url'                   => 'setLink',
				'product:retailer_item_id' => 'setId',
				'product:title'            => 'setTitle',
				'product:description'      => 'setDescription',
				'product:availability'     => array( 'setAttribute', 'availability', false ),
				'product:condition'        => 'setCondition',
				'product:price'            => 'setPrice',
				'product:url'              => 'setLink',

				/*
				 * Additional required fields for checkout on Facebook and Instagram (US only).
				 */
				'product:category' => 'setGoogleCategory',
				'product:size'     => 'setSize',

				/*
				 * The brand name, unique manufacturer part number (MPN) or Global Trade Item Number (GTIN) of the
				 * item. You only need to enter one of these, not all of them. For GTIN, enter the item's UPC, EAN,
				 * JAN or ISBN. Character limit: 100.
				 */
				'product:brand'       => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:mfr_part_no' => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:isbn'        => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:upc'         => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:ean'         => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:gtin14'      => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:gtin13'      => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:gtin12'      => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:gtin8'       => array( 'addAttribute', 'brand', false ),	// One or more.
				'product:gtin'        => array( 'addAttribute', 'brand', false ),	// One or more.

				/*
				 * Optional fields for products.
				 */
				'product:sale_price'            => 'setSalePrice',
				'product:sale_price_dates'      => array( 'setAttribute', 'sale_price_effective_date', false ),
				'product:item_group_id'         => array( 'setAttribute', 'item_group_id', false ),
				'product:color'                 => 'setColor',
				'product:target_gender'         => array( 'setAttribute', 'gender', false ),
				'product:age_group'             => array( 'setAttribute', 'age_group', false ),
				'product:material'              => 'setMaterial',
				'product:pattern'               => array( 'setAttribute', 'pattern', false ),
				'product:shipping_weight:value' => 'setShippingWeight',
			);

			foreach ( $names as $mt_name => $mixed ) {

				if ( isset( $mt_single[ $mt_name ] ) && '' !== $mt_single[ $mt_name ] ) {	// Not null or empty string.

					if ( is_array( $mixed ) ) {

						list( $method_name, $prop_name, $is_cdata ) = $mixed;

					} else {

						list( $method_name, $prop_name, $is_cdata ) = array( $mixed, '', false );
					}

					$values = is_array( $mt_single[ $mt_name ] ) ? $mt_single[ $mt_name ] : array( $mt_single[ $mt_name ] );

					foreach ( $values as $value ) {

						if ( false !== strpos( $mt_name, ':value' ) ) {

							$mt_name_units = preg_replace( '/:value$/', ':units', $mt_name );

							if ( ! empty( $mt_single[ $mt_name_units ] ) ) {

								$value .= ' ' . $mt_single[ $mt_name_units ];
							}
						}

						/*
						 * Call method from Vitalybaev\GoogleMerchant\Product().
						 */
						if ( method_exists( $product, $method_name ) ) {	// Just in case.

							if ( $prop_name ) {

								$product->$method_name( $prop_name, $value, $is_cdata );

							} else {

								$product->$method_name( $value );
							}
						}
					}
				}
			}
		}

		static private function add_product_images( &$product, $mt_single ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$image_urls = $wpsso->og->get_product_retailer_item_image_urls( $mt_single, $size_names = 'wpsso-cmcf', $md_pre = array( 'cmcf', 'og' ) );

			foreach ( $image_urls as $num => $image_url ) {

				if ( 0 === $num ) {

					$product->setImage( $image_url );

				} else {

					$product->addAdditionalImage( $image_url );
				}
			}
		}

		static private function sanitize_mt_array( &$mt_single ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$content_maps = $wpsso->cf[ 'head' ][ 'cmcf_content_map' ];

			foreach ( $content_maps as $mt_name => $map ) {

				if ( isset( $mt_single[ $mt_name ] ) ) {

					self::map_mt_value( $mt_single[ $mt_name ], $map );
				}
			}

			foreach ( array( 'product:price', 'product:sale_price' ) as $mt_name ) {

				if ( isset( $mt_single[ $mt_name . ':amount' ] ) && isset( $mt_single[ $mt_name . ':currency' ] ) ) {

					$mt_single[ $mt_name ] = trim( $mt_single[ $mt_name . ':amount' ] . ' '. $mt_single[ $mt_name . ':currency' ] );
				}
			}

			foreach ( array( 'product:sale_price_dates' ) as $mt_name ) {

				if ( ! empty( $mt_single[ $mt_name . ':start_iso' ] ) && ! empty( $mt_single[ $mt_name . ':end_iso' ] ) ) {

					$mt_single[ $mt_name ] = $mt_single[ $mt_name . ':start_iso' ] . '/' . $mt_single[ $mt_name . ':end_iso' ];
				}
			}
		}

		static private function map_mt_value( &$value, array $map ) {

			if ( is_array( $value ) ) {

				foreach ( $value as $num => &$arr_val ) {

					self::map_mt_value( $arr_val, $map );
				}

			} else {

				if ( isset( $map[ $value ] ) ) {	// Allow for false.

					$value = $map[ $value ];
				}
			}
		}
	}
}
