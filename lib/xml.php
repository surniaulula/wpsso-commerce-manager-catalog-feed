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
use Vitalybaev\GoogleMerchant\Meta\Product;

if ( ! class_exists( 'WpssoCmcfXml' ) ) {

	class WpssoCmcfXml {

		static $product_callbacks = array(

			/*
			 * Required fields for products.
			 *
			 * See https://www.facebook.com/business/help/120325381656392?id=725943027795860
			 */
			'og:title'                 => 'setTitle',
			'og:description'           => 'setDescription',
			'og:url'                   => 'setCanonicalLink',
			'product:retailer_item_id' => 'setId',
			'product:title'            => 'setTitle',
			'product:description'      => 'setDescription',
			'product:availability'     => 'setAvailability',
			'product:condition'        => 'setCondition',
			'product:price'            => 'setPrice',
			'product:url'              => 'setLink',

			/*
			 * Required fields for checkout on Facebook and Instagram (US only).
			 */
			'product:category' => 'setGoogleCategory',
			'product:size'     => 'setSize',

			/*
			 * The brand name, unique manufacturer part number (MPN) or Global Trade Item Number (GTIN) of the
			 * item. You only need to enter one of these, not all of them. For GTIN, enter the item's UPC, EAN,
			 * JAN or ISBN. Character limit: 100.
			 */
			'product:brand'       => 'addBrand',	// One or more.
			'product:mfr_part_no' => 'addBrand',	// One or more.
			'product:isbn'        => 'addBrand',	// One or more.
			'product:upc'         => 'addBrand',	// One or more.
			'product:ean'         => 'addBrand',	// One or more.
			'product:gtin14'      => 'addBrand',	// One or more.
			'product:gtin13'      => 'addBrand',	// One or more.
			'product:gtin12'      => 'addBrand',	// One or more.
			'product:gtin8'       => 'addBrand',	// One or more.
			'product:gtin'        => 'addBrand',	// One or more.

			/*
			 * Optional fields for products.
			 */
			'product:sale_price'            => 'setSalePrice',
			'product:sale_price_dates'      => 'setSalePriceEffectiveDate',
			'product:item_group_id'         => 'setItemGroupId',
			'product:color'                 => 'setColor',
			'product:target_gender'         => 'setGender',
			'product:age_group'             => 'setAgeGroup',
			'product:material'              => 'setMaterial',
			'product:pattern'               => 'setPattern',
			'product:shipping_weight:value' => 'setShippingWeight',
		);

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

				$is_switched    = switch_to_locale( $request_locale );	// Switches to locale if the WP language is installed.
				$current_locale = SucomUtil::get_locale();		// Update the current locale value.

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

			$site_title = SucomUtil::get_site_name( $wpsso->options, $request_locale );
			$site_url   = SucomUtil::get_home_url( $wpsso->options, $request_locale );
			$site_desc  = SucomUtil::get_site_description( $wpsso->options, $request_locale );
			$rss2_feed  = new Vitalybaev\GoogleMerchant\Feed( $site_title, $site_url, $site_desc, '2.0' );
			$query_args = array( 'meta_query' => WpssoAbstractWpMeta::get_column_meta_query_og_type( $og_type = 'product', $request_locale ) );
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

						self::add_feed_product( $rss2_feed, $mt_single, $request_type );
					}

				} else {

					if ( $wpsso->debug->enabled ) {

						$wpsso->debug->log( 'adding product for post id ' . $post_id );
					}

					self::add_feed_product( $rss2_feed, $mt_og, $request_type );
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
		 * See https://www.facebook.com/business/help/120325381656392?id=725943027795860
		 */
		static private function add_feed_product( &$rss2_feed, array $mt_single, $request_type = 'feed' ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$product = new Vitalybaev\GoogleMerchant\Meta\Product();

			self::add_product_data( $product, $mt_single );

			self::add_product_images( $product, $mt_single );

			$rss2_feed->addProduct( $product );
		}

		/*
		 * See https://www.facebook.com/business/help/120325381656392?id=725943027795860
		 */
		static private function add_product_data( &$product, $mt_single ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			self::sanitize_mt_array( $mt_single );

			self::add_object_data( $product, $mt_single, self::$product_callbacks );
		}

		static private function add_product_images( &$product, $mt_single ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$image_urls = $wpsso->og->get_product_retailer_item_image_urls( $mt_single, $size_names = 'wpsso-cmcf', $md_pre = array( 'cmcf', 'og' ) );

			foreach ( $image_urls as $num => $image_url ) {

				if ( 0 == $num ) {

					$product->setImage( $image_url );

				} else $product->addAdditionalImage( $image_url );
			}
		}

		static private function add_object_data( &$object, array $data, array $callbacks ) {

			foreach ( $callbacks as $key => $callback ) {

				if ( empty( $callback ) ) {	// Not used.

					continue;

				} elseif ( isset( $data[ $key ] ) && '' !== $data[ $key ] ) {	// Not null or empty string.

					if ( is_array( $callback ) ) {

						list( $method_name, $prop_name, $is_cdata ) = $callback;

					} else {

						list( $method_name, $prop_name, $is_cdata ) = array( $callback, '', false );
					}

					$values = is_array( $data[ $key ] ) ? $data[ $key ] : array( $data[ $key ] );

					foreach ( $values as $value ) {

						foreach ( array( ':value' => ':units', '_cost'  => '_currency' ) as $value_suffix => $append_suffix ) {

							if ( false !== strpos( $key, $value_suffix ) ) {

								$key_append = preg_replace( '/' . $value_suffix . '$/', $append_suffix, $key );

								if ( ! empty( $data[ $key_append ] ) ) {

									$value .= ' ' . $data[ $key_append ];
								}
							}
						}

						if ( method_exists( $object, $method_name ) ) {	// Just in case.

							if ( $prop_name ) {

								$object->$method_name( $prop_name, $value, $is_cdata );

							} else {

								$object->$method_name( $value );
							}

						} else {

							$notice_pre = sprintf( '%s error:', __METHOD__ );

							$notice_msg = sprintf( __( '%1$s::%2$s() method does not exist.', 'wpsso-commerce-manager-catalog-feed' ),
								get_class( $object ), $method_name );

							SucomUtil::safe_error_log( $notice_pre . ' ' . $notice_msg );
						}
					}
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

			} elseif ( isset( $map[ $value ] ) ) {	// Allow for false.

				$value = $map[ $value ];
			}
		}
	}
}
