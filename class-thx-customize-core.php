<?php
/*
Plugin Name: thx.jp/
Plugin URI:
Description: thx.jp/ カスタマイズの中核（Customize Core）プラグイン
Version: 0.1.9
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
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;}
?>
<?php
if ( ! class_exists( 'Thx_Customize_Core' ) ) {
	class Thx_Customize_Core {
		//読み込むurl
		static $push_js_url  = array();
		static $push_css_url = array();

		public function __construct() {
			$thx_cc_option = get_option( 'thx_cc_option' );
			$src_css_url   = plugins_url( 'src/css/', __FILE__ );
			$src_js_url    = plugins_url( 'src/js/', __FILE__ );

			//管理画面の設定
			add_action( '_admin_menu', 'thx_admin_menu' );
			add_action( 'admin_init', 'thx_settings_init' );

			//プラグインメニューの設定
			add_filter(
				'plugin_action_links_' . plugin_basename( __FILE__ ),
				array( $this, 'add_action_links' )
			);

			//アンインストール
			if ( function_exists( 'register_uninstall_hook' ) ) {
				register_uninstall_hook( __FILE__, 'Thx_Customize_Core::thx_cc_uninstall' );
			}

			//アンチエイリアス
			if ( '1' === $thx_cc_option['antialiase'] ) {
				$this::$push_css_url[] = $src_css_url . 'thx-antialiase.css';
			}

			//テキストの自動拡大
			if ( '1' === $thx_cc_option['text_size_adjust'] ) {
				$this::$push_css_url[] = $src_css_url . 'thx-text-size-adjust.css';
			}

			//引用符の解除
			if ( '1' === $thx_cc_option['remove_texturize'] ) {
				remove_filter( 'the_content', 'wptexturize' );
				remove_filter( 'the_excerpt', 'wptexturize' );
				remove_filter( 'the_title', 'wptexturize' );
			}

			//行間の崩れないルビ
			if ( '1' === $thx_cc_option['ruby'] ) {
				$this::$push_css_url[] = $src_css_url . 'thx-ruby.css';
				$this::$push_js_url[]  = $src_js_url . 'thx-ruby.js';
			}

			//和欧間スペース
			if ( '1' === $thx_cc_option['wao_space'] ) {
				if ( 'jQuery' === $thx_cc_option['wao_space_js_php'] ) {
					$this::$push_js_url[] = $src_js_url . 'thx-wao-space.js';
				} else {
					add_filter( 'the_content', 'wao_space', 21000 );
					add_filter( 'the_category_content', 'wao_space', 21000 );
				}

				$this::$push_css_url[] = $src_css_url . 'thx-wao-space.css';
			}

			//管理画面にCSSを追加
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style_on_admin_page' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}//__construct()

		//アインインストール時にオプション削除
		static function thx_cc_uninstall() {
			delete_option( 'thx_cc_option' );
		}

		//設定リンク追加
		public static function add_action_links( $links ) {
			$add_link = '<a href="admin.php?page=thx-jp-customize-core">設定</a>';
			array_unshift( $links, $add_link );
			return $links;
		}

		//ファイル読み込み
		public static function file_to_str( $path ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			if ( WP_Filesystem() ) {
				global $wp_filesystem;
				$str = $wp_filesystem->get_contents( $path );
				return $str;
			}
		}

		//ファイル書き出し
		public static function str_to_file( $path, $str ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			if ( WP_Filesystem() ) {
				global $wp_filesystem;
				$wp_filesystem->put_contents( $path, $str );
			}
		}

		//キューイング
		public static function enqueue_scripts() {
			foreach ( $this::$push_css_url as $url ) {
				$this::enqueue_file_style( $url );
			}
			foreach ( $this::$push_js_url as $url ) {
				$this::enqueue_file_script( $url );
			}
		}//enqueue_scripts()

		//cssファイルをキューイング
		public static function enqueue_file_style( $css_url ) {
			// cssのurlからファイル名のみ取り出す
			$css_name = preg_replace( '{.*\/}uis', '', $css_url );
			// cssのファイル名から拡張子を除去
			$css_name = preg_replace( '{\..*}uis', '', $css_name );
			//キュー
			wp_enqueue_style( $css_name, $css_url );
		}//enqueue_file_style()

		//jsファイルをキューイング
		public static function enqueue_file_script( $js_url ) {
			// jsのurlからファイル名のみ取り出す
			$js_name = preg_replace( '{.*\/}uis', '', $js_url );
			// jsのファイル名から拡張子を除去
			$js_name = preg_replace( '{\..*}uis', '', $js_name );
			//キュー
			wp_enqueue_script( $js_name, $js_url, array( 'jquery' ), false, true );
		}//enqueue_file_script()

		//管理画面にCSSを追加
		public static function enqueue_style_on_admin_page() {
			wp_enqueue_style( 'thx_admin', plugins_url( 'src/css/thx_admin.css', __FILE__ ) );
		}

		//文字列を正規表現で置換
		public static function str_preg_replace( $str, $preg_array ) {
			//正規表現式の数だけループ
			foreach ( $preg_array as $preg_match => $replace ) {
				$str = preg_replace( $preg_match, $replace, $str );

				// // $str内でマッチするものを$matchへ配列化
				// preg_match_all($preg_match, $str, $match);
				// // マッチした配列をループで置換
				// foreach ($match[0] as $value) {
				// 	$str = str_replace($value, $replace, $str);
				// }
			}
			return $str;
		}//str_preg_replace()
	}//class
}//! class_exists

require_once( 'src/php/menu.php' );
require_once( 'src/php/wao.php' );

new Thx_Customize_Core;