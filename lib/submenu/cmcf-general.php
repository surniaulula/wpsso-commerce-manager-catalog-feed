<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfSubmenuCmcfGeneral' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoCmcfSubmenuCmcfGeneral extends WpssoAdmin {

		private $doing_task = false;

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->menu_id   = $id;
			$this->menu_name = $name;
			$this->menu_lib  = $lib;
			$this->menu_ext  = $ext;
		}

		/*
		 * Add settings page filters and actions hooks.
		 *
		 * Called by WpssoAdmin->load_setting_page() after the 'wpsso-action' query is handled.
		 */
		protected function add_plugin_hooks() {

			$this->doing_task = $this->p->util->cache->doing_task();

			$this->p->util->add_plugin_filters( $this, array(
				'form_button_rows' => 1,	// Form buttons for this settings page.
			), PHP_INT_MAX );			// Run filter last to remove all form buttons.
		}

		/*
		 * Remove all action buttons from this settings page and add a "Refresh XML Cache" button.
		 */
		public function filter_form_button_rows( $form_button_rows ) {

			if ( $this->doing_task ) {

				$form_button_rows = array();

			} else {

				$form_button_rows = array(
					array(
						'refresh_feed_xml_cache' => _x( 'Refresh XML Cache', 'submit button', 'wpsso-commerce-manager-catalog-feed' ),
					),
				);
			}

			return $form_button_rows;
		}

		/*
		 * Called by WpssoAdmin->load_setting_page() after the 'wpsso-action' query is handled.
		 */
		protected function add_meta_boxes() {

			foreach ( array(
				'feed' => _x( 'Commerce Manager Catalog Feed XML', 'metabox title', 'wpsso-commerce-manager-catalog-feed' ),
			) as $metabox_id => $metabox_title ) {

				$metabox_screen  = $this->pagehook;
				$metabox_context = 'normal';
				$metabox_prio    = 'default';
				$callback_args   = array(	// Second argument passed to the callback function / method.
					'page_id'       => $this->menu_id,
					'metabox_id'    => $metabox_id,
					'metabox_title' => $metabox_title,
				);

				add_meta_box( $this->pagehook . '_' . $metabox_id, $metabox_title,
					array( $this, 'show_metabox_table' ), $metabox_screen,
						$metabox_context, $metabox_prio, $callback_args );
			}
		}

		public function get_table_rows( $metabox_id, $tab_key, $metabox_title = '' ) {

			$table_rows = array();

			if ( $this->doing_task ) {

				$this->add_table_rows_doing_task( $table_rows, $metabox_title );

				return $table_rows;
			}

			switch ( $metabox_id . '-' . $tab_key ) {

				case 'cmcf-general-feed':

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

					break;
			}

			return $table_rows;
		}

		private function add_table_rows_doing_task( &$table_rows, $metabox_title ) {	// Pass by reference is OK.

			$task_name_transl = _x( $this->doing_task, 'task name', 'wpsso' );

			$table_rows[ 'wpssocmcf_disabled' ] = '<tr><td align="center">' .
				'<p class="status-msg">' . sprintf( __( 'A background task to %s is currently running.',
					'wpsso-commerce-manager-catalog-feed' ), $task_name_transl ) . '</p>' .
				'<p class="status-msg">' . sprintf( __( '%s is currently unavailable pending completion of a maintenance task.',
					'wpsso-commerce-manager-catalog-feed' ), $metabox_title ) . '</p>' .
				'</td></tr>';
		}
	}
}
