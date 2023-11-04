<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfSubmenuFacebookCatalog' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoCmcfSubmenuFacebookCatalog extends WpssoAdmin {

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->menu_id   = $id;
			$this->menu_name = $name;
			$this->menu_lib  = $lib;
			$this->menu_ext  = $ext;

			$this->menu_metaboxes = array(
				'feedxml' => _x( 'Commerce Manager Catalog Feed XML', 'metabox title', 'wpsso-commerce-manager-catalog-feed' ),
			);
		}

		protected function add_settings_page_callbacks() {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 'form_button_rows' => 1 ), PHP_INT_MAX );
		}

		public function filter_form_button_rows( $form_button_rows ) {

			if ( $this->p->util->cache->is_refresh_running() ) {

				return array();
			}

			/*
			 * Remove all action buttons from this settings page and add a "Refresh XML Cache" button.
			 */
			$form_button_rows = array(
				array(
					'refresh_feed_xml_cache' => _x( 'Refresh XML Cache', 'submit button', 'wpsso-commerce-manager-catalog-feed' ),
				),
			);

			return $form_button_rows;
		}

		protected function get_table_rows( $page_id, $metabox_id, $tab_key = '', $args = array() ) {

			$table_rows = array();

			if ( $this->p->util->cache->is_refresh_running() ) {

				$task_name_transl = _x( 'refresh the cache', 'task name', 'wpsso' );

				$table_rows[ 'wpssocmcf_disabled' ] = '<tr><td align="center">' .
					'<p class="status-msg">' . sprintf( __( 'A background task to %s is currently running.',
						'wpsso-commerce-manager-catalog-feed' ), $task_name_transl ) . '</p>' .
					'<p class="status-msg">' . sprintf( __( '%s is currently unavailable pending completion of a cache refresh task.',
						'wpsso-commerce-manager-catalog-feed' ), $args[ 'metabox_title' ] ) . '</p>' .
					'</td></tr>';

				return $table_rows;
			}

			$locale_names = SucomUtil::get_available_feed_locale_names();

			foreach ( $locale_names as $locale => $native_name ) {

				$url = WpssoCmcfRewrite::get_url( $locale );
				$xml = WpssoCmcfXml::get( $locale );

				$item_count = substr_count( $xml, '<item>' );
				$img_count  = substr_count( $xml, '<g:image_link>' );
				$addl_count = substr_count( $xml, '<g:additional_image_link>' );
				$xml_size   = number_format( ( strlen( $xml ) / 1024 ) );	// XML size in KB.

				$table_rows[ 'cmcf_url_' . $locale ] = '' .
					$this->form->get_th_html( $native_name, $css_class = 'medium' ) .
					'<td>' . $this->form->get_no_input_clipboard( $url ) .
					'<p class="status-msg left">' .
					sprintf( _x( '%1$s feed items, %2$s image links, and %3$s additional image links.',
						'option comment', 'wpsso-commerce-manager-catalog-feed' ), $item_count, $img_count, $addl_count ) .
					'</p>' .
					'</td>';
			}

			return $table_rows;
		}
	}
}
