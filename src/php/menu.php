<?php if ( ! defined( 'ABSPATH' ) ) exit;
//プラグイン名
define('PLUG_IN_NAME', 'thx.jp/');
//テーマ設定ページ用のURLクエリ
// define('THEME_SETTINGS_PAFE', 'theme-settings');
//トップレベルオリジナル設定名
// define('SETTING_NAME_TOP', PLUG_IN_NAME.__( ' 設定', PLUG_IN_NAME ));

// require_once('src/menu/menu.php');

function thx_admin_menu() {
	//設定のサブメニューに追加する
	add_options_page(
		PLUG_IN_NAME,
		PLUG_IN_NAME,
		'manage_options',
		'thx-jp-customize-core',
		'thx_settings_plugin_options'
	);
}
// フォームの枠組を出力する
function thx_settings_plugin_options() {
	?>
	<div class="wrap">
		<form action="options.php" method="post">
			<?php settings_fields('thx_settings-group'); // グループ名 ?>
			<?php do_settings_sections('thx_settings'); // ページ名 ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
// 管理画面の作成
function thx_settings_init() {

	//セクション設定
	add_settings_section(
		'thx_settings_section', // セクション名
		'プラグインの設定', // タイトル
		'thx_settings_section_callback_function', // echo '<p>プラグインのON/OFFを切り替えます。</p>';
		'thx_settings' // このセクションを表示するページ名。do_settings_sectionsで設定
	);

	//引用符の解除
	add_settings_field(
		'thx_remove_texturize',
		'引用符の解除',
		'thx_remove_texturize_callback',
		'thx_settings','thx_settings_section'
	);
	register_setting('thx_settings-group', 'thx_remove_texturize');

	//和欧間スペース
	add_settings_field(
		'thx_wao_space',
		'和欧間スペース',
		'thx_wao_space_callback',
		'thx_settings',
		'thx_settings_section'
	);
	register_setting('thx_settings-group', 'thx_wao_space');

	// add_settings_field(
	// 	'thx_wao_space_js_php',
	// 	'',
	// 	'thx_wao_space_js_php_callback',
	// 	'thx_settings',
	// 	'thx_settings_section'
	// );
	register_setting('thx_settings-group', 'thx_wao_space_js_php');

	//ルビ
	add_settings_field(
		'thx_ruby',
		'行間の崩れないルビ',
		'thx_ruby_callback',
		'thx_settings',
		'thx_settings_section'
	);
	register_setting('thx_settings-group', 'thx_ruby');

	//見出しのカウンター
	add_settings_field(
		'thx_counted_heading',
		'見出しのカウンター',
		'thx_counted_heading_callback',
		'thx_settings',
		'thx_settings_section'
	);
	register_setting('thx_settings-group', 'thx_counted_heading');

	//コンテンツの置き換え
	add_settings_field(
		'thx_content_replace',
		'コンテンツの置き換え（β版）',
		'thx_content_replace_callback',
		'thx_settings',
		'thx_settings_section'
	);
	register_setting('thx_settings-group', 'thx_content_replace');

	add_settings_field(
		'thx_content_replace_array',
		'',
		'thx_content_replace_array_callback',
		'thx_settings',
		'thx_settings_section'
	);
	register_setting('thx_settings-group', 'thx_content_replace_array');
}

function thx_settings_section_callback_function() {
	echo '<p>各機能のON/OFFを切り替えます。</p>';
}

// コールバック
function thx_remove_texturize_callback() {
	echo '<p><input name="thx_remove_texturize" id="thx_remove_texturize" type="checkbox" value="1"';
	checked( get_option( 'thx_remove_texturize', 1 ) );
	echo ' />引用符などの自動変換機能を解除する</p>';
}
function thx_wao_space_callback() {
	echo '<p><input name="thx_wao_space" id="thx_wao_space" type="checkbox" value="1"';
	checked( get_option( 'thx_wao_space' ), 1 );
	echo ' />和文と欧文の間にアキを設ける</p>';
	thx_wao_space_js_php_callback();
}
function thx_wao_space_js_php_callback() {
	echo '<p>　<input name="thx_wao_space_js_php" id="thx_wao_space_js_php" type="radio" value="jQuery"';
	checked( get_option( 'thx_wao_space_js_php' ), 'jQuery' );
	echo ' />jQuery</p>';
	echo '<p>　<input name="thx_wao_space_js_php" id="thx_wao_space_js_php" type="radio" value="php"';
	checked( get_option( 'thx_wao_space_js_php' ), 'php' );
	echo ' />php</p>';
}
function thx_ruby_callback() {
	echo '<p><input name="thx_ruby" id="thx_ruby" type="checkbox" value="1"';
	checked( get_option( 'thx_ruby', 1 ), 1 );
	echo ' />行間の崩れないルビを使用する</p>';
}
function thx_counted_heading_callback() {
	echo '<p><input name="thx_counted_heading" id="thx_counted_heading" type="checkbox" value="1"';
	checked( get_option( 'thx_counted_heading', 1 ), 1 );
	echo ' />見出しにカウンターを付加する</p>';
}
function thx_content_replace_callback() {
	echo '<p><input name="thx_content_replace" id="thx_content_replace" type="checkbox" value="1"';
	checked( get_option( 'thx_content_replace', 1 ), 1 );
	echo ' />出力されるHTMLを正規表現で置換する</p>';
}
function thx_content_replace_array_callback() {
	echo '<textarea
	id="thx_content_replace_array"
	name="thx_content_replace_array"
	cols="80"
	rows="4"
	placeholder="{&quot;正規表現&quot;:&quot;置換文字&quot;,･･･}">'
	.get_option( 'thx_content_replace_array' ).
	'</textarea>';
}
