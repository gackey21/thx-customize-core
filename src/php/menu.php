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
		add_settings_section(
				'thx_settings_section', // セクション名
				'プラグインの設定', // タイトル
				'thx_settings_section_callback_function', // echo '<p>プラグインのON/OFFを切り替えます。</p>';
				'thx_settings' // このセクションを表示するページ名。do_settings_sectionsで設定
		);
		add_settings_field(
				'thx_remove_texturize', // フィールド名
				'引用符の解除', // タイトル
				'thx_remove_texturize_callback', // コールバック関数。この関数の実行結果が出力される
				'thx_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
				'thx_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
		);
		register_setting(
				'thx_settings-group', // グループ名。settings_fieldsで設定
				'thx_remove_texturize' // オプション名
		);
		add_settings_field(
				'thx_wao_space', // フィールド名
				'和欧間スペース', // タイトル
				'thx_wao_space_callback', // コールバック関数。この関数の実行結果が出力される
				'thx_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
				'thx_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
		);
		register_setting(
				'thx_settings-group', // グループ名。settings_fieldsで設定
				'thx_wao_space' // オプション名
		);
		add_settings_field(
				'thx_wao_space_js_php', // フィールド名
				'', // タイトル
				'thx_wao_space_js_php_callback', // コールバック関数。この関数の実行結果が出力される
				'thx_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
				'thx_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
		);
		register_setting(
				'thx_settings-group', // グループ名。settings_fieldsで設定
				'thx_wao_space_js_php' // オプション名
		);
		add_settings_field(
				'thx_ruby', // フィールド名
				'行間の崩れないルビ', // タイトル
				'thx_ruby_callback', // コールバック関数。この関数の実行結果が出力される
				'thx_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
				'thx_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
		);
		register_setting(
				'thx_settings-group', // グループ名。settings_fieldsで設定
				'thx_ruby' // オプション名
		);
		add_settings_field(
				'thx_counted_heading', // フィールド名
				'見出しのカウンター', // タイトル
				'thx_counted_heading_callback', // コールバック関数。この関数の実行結果が出力される
				'thx_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
				'thx_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
		);
		register_setting(
				'thx_settings-group', // グループ名。settings_fieldsで設定
				'thx_counted_heading' // オプション名
		);
		add_settings_field(
				'thx_content_replace', // フィールド名
				'コンテンツの置き換え（β版）', // タイトル
				'thx_content_replace_callback', // コールバック関数。この関数の実行結果が出力される
				'thx_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
				'thx_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
		);
		register_setting(
				'thx_settings-group', // グループ名。settings_fieldsで設定
				'thx_content_replace' // オプション名
		);
		add_settings_field(
				'thx_content_replace_array', // フィールド名
				'', // タイトル
				'thx_content_replace_array_callback', // コールバック関数。この関数の実行結果が出力される
				'thx_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
				'thx_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
		);
		register_setting(
				'thx_settings-group', // グループ名。settings_fieldsで設定
				'thx_content_replace_array' // オプション名
		);
}

function thx_settings_section_callback_function() {
		echo '<p>各機能のON/OFFを切り替えます。</p>';
}

// チェックボックスを表示
function thx_remove_texturize_callback() {
		echo '<p><input name="thx_remove_texturize" id="thx_remove_texturize" type="checkbox" value="1"';
		checked( get_option( 'thx_remove_texturize', 1 ) );
		echo ' />引用符などの自動変換機能を解除する</p>';
}
function thx_wao_space_callback() {
		echo '<p><input name="thx_wao_space" id="thx_wao_space" type="checkbox" value="1"';
		checked( get_option( 'thx_wao_space' ), 1 );
		echo ' />和文と欧文の間にアキを設ける</p>';
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
