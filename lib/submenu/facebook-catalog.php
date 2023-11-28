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
				'feed' => _x( 'Commerce Manager Catalog Feed XML', 'metabox title', 'wpsso-commerce-manager-catalog-feed' ),
			);
		}

		protected function add_form_buttons( &$form_button_rows ) {

			/*
			 * If a refresh is running, remove all buttons.
			 */
			if ( $this->p->util->cache->is_refresh_running() ) {

				return array();
			}

			/*
			 * Remove all action buttons and add a "Refresh XML Cache" button.
			 */
			$form_button_rows = array(
				array(
					'refresh_feed_xml_cache' => _x( 'Refresh XML Cache', 'submit button', 'wpsso-commerce-manager-catalog-feed' ),
				),
			);
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

				$url        = WpssoCmcfRewrite::get_url( $locale, $request_type = 'feed' );
				$xml        = WpssoCmcfXml::get( $locale, $request_type = 'feed' );
				$css_id     = SucomUtil::sanitize_css_id( 'cmcf_feed_' . $locale . '_url' );
				$item_count = substr_count( $xml, '<item>' );
				$img_count  = substr_count( $xml, '<g:image_link>' );
				$addl_count = substr_count( $xml, '<g:additional_image_link>' );
				$xml_size   = number_format( ( strlen( $xml ) / 1024 ) );	// XML size in KB.
				$xml_info   = array(
					sprintf( _x( '%s feed items', 'option comment', 'wpsso-commerce-manager-catalog-feed' ), $item_count ),
					sprintf( _x( '%s image links', 'option comment', 'wpsso-commerce-manager-catalog-feed' ), $img_count ),
					sprintf( _x( '%s additional image links', 'option comment', 'wpsso-commerce-manager-catalog-feed' ), $addl_count ),
					sprintf( _x( '%s KB file size', 'option comment', 'wpsso-commerce-manager-catalog-feed' ), $xml_size ),
				);

				$table_rows[ $css_id ] = '' .
					$this->form->get_th_html( $native_name, $css_class = 'medium', $css_id,
						array( 'locale' => $locale, 'native_name' => $native_name ) ) .
					'<td>' . $this->form->get_no_input_clipboard( $url ) .
					'<p class="status-msg left">' . implode( '; ', $xml_info ) . '</p></td>';
			}

			return $table_rows;
		}
	}
}
