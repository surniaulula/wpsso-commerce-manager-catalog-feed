<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023 Jean-Sebastien Morisset (https://wpsso.com/)
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

			$xml_count      = 0;
			$current_locale = SucomUtil::get_locale();

			/*
			 * Calls SucomUtil::get_available_locale_names() and applies the 'sucom_available_feed_locale_names'
			 * filter.
			 *
			 * Returns an associative array with locale keys and native names (example: 'en_US' => 'English (United
			 * States)').
			 */
			$locale_names = SucomUtil::get_available_feed_locale_names();	// Uses a local cache.

			/*
			 * Move the current locale last to generate any notices in the current locale.
			 */
			SucomUtil::move_to_end( $locale_names, $current_locale );

			foreach ( $locale_names as $locale => $native_name ) {

				WpssoCmcfXml::clear_cache( $locale );	// Clear the feed XML cache file.

				WpssoCmcfXml::get( $locale );

				$xml_count++;
			}

			$metabox_title = _x( 'Commerce Manager Catalog Feed XML', 'metabox title', 'wpsso-commerce-manager-catalog-feed' );

			$notice_msg .= sprintf( __( '%1$s for %2$s locales has been refreshed.', 'wpsso-commerce-manager-catalog-feed' ), $metabox_title, $xml_count ) . ' ';

			return $notice_msg;
		}
	}
}
