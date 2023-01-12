<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoFcfFilters' ) ) {

	class WpssoFcfFilters {

		private $p;	// Wpsso class object.
		private $a;	// WpssoFcf class object.
		private $adv;	// WpssoFcfFiltersAdvanced class object.
		private $edit;	// WpssoFcfFiltersEdit class object.
		private $msgs;	// WpssoFcfFiltersMessages class object.

		/**
		 * Instantiated by WpssoFcf->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( true === $do_once ) {

				return;	// Stop here.
			}

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'plugin_image_sizes'     => 1,
				'cache_refreshed_notice' => 3,
			) );

			if ( is_admin() ) {

				require_once WPSSOFCF_PLUGINDIR . 'lib/filters-advanced.php';

				$this->adv = new WpssoFcfFiltersAdvanced( $plugin, $addon );

				require_once WPSSOFCF_PLUGINDIR . 'lib/filters-edit.php';

				$this->edit = new WpssoFcfFiltersEdit( $plugin, $addon );

				require_once WPSSOFCF_PLUGINDIR . 'lib/filters-messages.php';

				$this->msgs = new WpssoFcfFiltersMessages( $plugin, $addon );
			}
		}

		public function filter_plugin_image_sizes( array $sizes ) {

			$sizes[ 'fcf' ] = array(	// Option prefix.
				'name'         => 'fcf',
				'label_transl' => _x( 'Facebook Catalog Feed XML', 'option label', 'wpsso-facebook-catalog-feed' ),
			);

			return $sizes;
		}

		public function filter_cache_refreshed_notice( $notice_msg, $user_id, $read_cache = false ) {

			$xml_count = 0;

			$locale_names = SucomUtil::get_available_feed_locale_names();	// Uses a local cache.

			foreach ( $locale_names as $locale => $native_name ) {

				switch_to_locale( $locale );

				$xml = WpssoFcfXml::get( $read_cache );

				$xml_count++;
			}

			restore_current_locale();	// Calls an action to clear the SucomUtil::get_locale() cache.

			$notice_msg .= sprintf( __( 'The Facebook Catalog Feed XML for %d locales has been refreshed.', 'wpsso-facebook-catalog-feed' ), $xml_count ) . ' ';

			return $notice_msg;
		}
	}
}
