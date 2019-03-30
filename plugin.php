<?php
/*
Plugin Name: thx.jp/
Plugin URI:
Description: thx.jp/ カスタマイズの中核（Customize Core）プラグイン
Version: 0.1.0
Author:Gackey.21
Author URI: https://thx.jp
License: GPL2
*/
?>
<?php
/*  Copyright 2019 Gackey.21 (email : gackey.21@gmail.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as
		 published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
if ( ! class_exists( 'thx_Customize_Core' ) ) {
	class thx_Customize_Core {
		//読み込むurl
		static $js_url = array();
		static $css_url = array();

		public function __construct() {
			$thx_cc_option = get_option('thx_cc_option');

			//管理画面の設定
			add_action('admin_menu', 'thx_admin_menu');
			add_action('admin_init', 'thx_settings_init');

			//プラグインメニューの設定
			add_filter(
				'plugin_action_links_' . plugin_basename( __FILE__ ),
				array( $this, 'add_action_links' )
			);

			//アンインストール
			if(function_exists('register_uninstall_hook')) {
				register_uninstall_hook (__FILE__, 'thx_Customize_Core::thx_cc_uninstall');
			}

			//アンチエイリアス
			if ($thx_cc_option['antialiase'] == 1) {
				$this::$css_url[]
					= plugins_url( 'src/css/thx-antialiase.css', __FILE__ );
			}

			//テキストの自動拡大
			if ($thx_cc_option['text_size_adjust'] == 1) {
				$this::$css_url[]
					= plugins_url( 'src/css/thx-text-size-adjust.css', __FILE__ );
			}

			//引用符の解除
			if ($thx_cc_option['remove_texturize'] == 1) {
				remove_filter("the_content", "wptexturize");
				remove_filter("the_excerpt", "wptexturize");
				remove_filter("the_title", "wptexturize");
			}

			//行間の崩れないルビ
			if ($thx_cc_option['ruby'] == 1) {
				$this::$css_url[]
					= plugins_url( 'src/css/thx-ruby.css', __FILE__ );
				$this::$js_url[]
					= plugins_url( 'src/js/thx-ruby.js', __FILE__ );
			}

			//和欧間スペース
			if ($thx_cc_option['wao_space'] == 1) {
				if ($thx_cc_option['wao_space_js_php'] == 'jQuery') {
					$this::$js_url[]
						= plugins_url( 'src/js/thx-wao-space.js', __FILE__ );
				} else {
					add_filter('the_content', 'wao_space', 21000);
					add_filter('the_category_content', 'wao_space', 21000);
				}

				$this::$css_url[]
					= plugins_url( 'src/css/thx-wao-space.css', __FILE__ );
			}

			// // フックのカスタマイズ
			// if ($thx_cc_option['hook_customize'] == 1) {
			// 	add_action('wp_enqueue_scripts', array($this, 'typesquare_std_into_footer'));
			// }

			// コンテンツ変更
			if ($thx_cc_option['content_replace'] == 1) {
				add_filter('the_content', 'content_replace', 20900);
			}

			// // jQueryの非同期読み込み
			// if ($thx_cc_option['js_tag'] && ! is_admin()) {
			// 	add_filter( 'script_loader_tag', 'replace_script_tag' );
			// }

			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		}//__construct()

		static function thx_cc_uninstall() {
			delete_option('thx_cc_option');
		}

		public function add_action_links ( $links ) {
			$add_link = '<a href="options-general.php?page=thx-jp-customize-core">設定</a>';
			array_unshift( $links, $add_link);
			return $links;
		}

		// //「TypeSquare Webfonts for エックスサーバー」をフッターで読み込む
		// public function typesquare_std_into_footer() {
		// 	wp_deregister_script( 'typesquare_std' );
		// 	// $this::$js_url[] = "//webfonts.xserver.jp/js/xserver.js";
		// 	wp_enqueue_script(
		// 		'typesquare_std',
		// 		"//webfonts.xserver.jp/js/xserver.js",
		// 		array( 'jquery' ),
		// 		false,
		// 		true
		// 	);
		// }//typesquare_std_into_footer()

		//ファイル読み込み
		public function file_to_str($path) {
			require_once( ABSPATH.'wp-admin/includes/file.php' );
			if ( WP_Filesystem() ) {
				global $wp_filesystem;
				$str = $wp_filesystem->get_contents($path);
				return $str;
			}
		}

		//ファイル書き出し
		public function str_to_file($path, $str) {
			require_once( ABSPATH.'wp-admin/includes/file.php' );
			if ( WP_Filesystem() ) {
				global $wp_filesystem;
				$wp_filesystem -> put_contents( $path, $str );
			}
		}

		//キューイング
		public function enqueue_scripts() {
			$tCC = new thx_Customize_Core();
			foreach ($this::$css_url as $url) {
				$tCC -> enqueue_file_style($url);
			}
			foreach ($this::$js_url as $url) {
				$tCC -> enqueue_file_script($url);
			}
		}//enqueue_scripts()

		//cssファイルをキューイング
		public function enqueue_file_style($css_url) {
			// cssのurlからファイル名のみ取り出す
			$css_name = preg_replace('{.*\/}uis', '', $css_url);
			// cssのファイル名から拡張子を除去
			$css_name = preg_replace('{\..*}uis', '', $css_name);
			//キュー
			wp_enqueue_style( $css_name, $css_url );
		}//enqueue_file_style()

		//jsファイルをキューイング
		public function enqueue_file_script($js_url) {
			// jsのurlからファイル名のみ取り出す
			$js_name = preg_replace('{.*\/}uis', '', $js_url);
			// jsのファイル名から拡張子を除去
			$js_name = preg_replace('{\..*}uis', '', $js_name);
			//キュー
			wp_enqueue_script( $js_name, $js_url, array( 'jquery' ), false, true );
		}//enqueue_file_script()
	}//class
}//! class_exists

require_once('src/php/menu.php');
require_once('src/php/wao.php');
require_once('src/php/content_replace.php');
require_once('src/php/script_tag_replace.php');

new thx_Customize_Core;
