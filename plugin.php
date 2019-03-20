<?php
/*
Plugin Name: thx.jp/ Customize Core
Plugin URI:
Description: thx.jp/ カスタマイズの中核プラグイン
Version: 0.0.1
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
		public function __construct() {
			//管理画面の設定
			add_action('admin_menu', 'thx_admin_menu');
			//引用符の解除
			if (get_option('thx_remove_texturize')) {
				remove_filter("the_content", "wptexturize");
				remove_filter("the_excerpt", "wptexturize");
				remove_filter("the_title", "wptexturize");
			}
			//和欧間スペース
			if (get_option('thx_wao_space')) {
				// 和欧間スペース　フック
				add_filter('the_content', 'wao_space', 21000);
				//css読み込み
				add_action('wp_enqueue_scripts', 'wao_space_css');
			}
			//見出しカウンター
			if (get_option('thx_counted_heading')) {
				//css読み込み
				add_action('wp_enqueue_scripts', 'counted_heading_css');
			}
			// コンテンツ変更　フック
			add_filter(
				'the_content',
				array( $this, 'content_replace' ),
				20900
			);
		}//__construct()

		//amp出力を行うurl
		static $css_amp_url = array();
		//ファイル書き出し
		public function str_to_file($path, $str) {
			require_once( ABSPATH.'wp-admin/includes/file.php' );
			if ( WP_Filesystem() ) {
				global $wp_filesystem;
				$wp_filesystem -> put_contents( $path, $str );
			}
		}
		//コンテンツ変更
		public function content_replace($the_content) {
			$match = '{(<div class="shoplinkyahoo">.*?)(Yahooショッピング)(.*?</div>)}uis';
			$replece = '$1Yahoo!$3';
			$the_content = preg_replace(
				$match,
				$replece,
				$the_content
			);
			return $the_content;
		}
		//cssファイルをキューイング
		public function enqueue_file_style($css_url) {
			// cssのurlからファイル名のみ取り出す
			$css_name = preg_replace('{.*\/}uis', '', $css_url);
			// cssのファイル名から拡張子を除去
			$css_name = preg_replace('{\..*}uis', '', $css_name);
			//キュー
			wp_enqueue_style( $css_name, $css_url );
		}//enqueue_file_style()
	}//class
}//! class_exists

require_once('src/php/menu.php');
require_once('src/php/wao.php');
require_once('src/php/counted-heading.php');

new thx_Customize_Core;
