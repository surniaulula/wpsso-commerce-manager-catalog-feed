<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfFilters' ) ) {

	class WpssoCmcfFilters {

		private $p;	// Wpsso class object.
		private $a;	// WpssoCmcf class object.
		private $adv;	// WpssoCmcfFiltersAdvanced class object.
		private $edit;	// WpssoCmcfFiltersEdit class object.
		private $msgs;	// WpssoCmcfFiltersMessages class object.

		/*
		 * Instantiated by WpssoCmcf->init_objects().
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
				'cache_refreshed_notice' => 2,
			) );

			if ( is_admin() ) {

				require_once WPSSOCMCF_PLUGINDIR . 'lib/filters-advanced.php';

				$this->adv = new WpssoCmcfFiltersAdvanced( $plugin, $addon );

				require_once WPSSOCMCF_PLUGINDIR . 'lib/filters-edit.php';

				$this->edit = new WpssoCmcfFiltersEdit( $plugin, $addon );

				require_once WPSSOCMCF_PLUGINDIR . 'lib/filters-messages.php';

				$this->msgs = new WpssoCmcfFiltersMessages( $plugin, $addon );
			}
		}

		public function filter_plugin_image_sizes( array $sizes ) {

			$sizes[ 'cmcf' ] = array(	// Option prefix.
				'name'         => 'cmcf',
				'label_transl' => _x( 'Commerce Manager Catalog Feed XML', 'option label', 'wpsso-commerce-manager-catalog-feed' ),
			);

			return $sizes;
		}

		public function filter_cache_refreshed_notice( $notice_msg, $user_id = null ) {

			$current_locale = SucomUtilWP::get_locale();
			$locale_names   = SucomUtilWP::get_available_feed_locale_names();

			/*
			 * Move the current locale last to generate any notices in the current locale.
			 */
			SucomUtil::move_to_end( $locale_names, $current_locale );

			foreach ( array(
				'feed' => _x( 'Commerce Manager Catalog Feed XML', 'metabox title', 'wpsso-commerce-manager-catalog-feed' ),
			) as $request_type => $metabox_title ) {

				$xml_count = 0;

				foreach ( $locale_names as $request_locale => $native_name ) {

					WpssoCmcfXml::clear_cache( $request_locale, $request_type );

					WpssoCmcfXml::get( $request_locale, $request_type );

					$xml_count++;
				}

				$notice_msg .= sprintf( __( '%1$s for %2$s locales has been refreshed.', 'wpsso-google-merchant-feed' ), $metabox_title, $xml_count ) . ' ';
			}

			return $notice_msg;
		}
	}
}
