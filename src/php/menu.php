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
}
add_action('admin_init', 'thx_settings_init');

function thx_settings_section_callback_function() {
		echo '<p>各機能のON/OFFを切り替えます。</p>';
}

// チェックボックスを表示
function thx_remove_texturize_callback() {
		echo '<p><input name="thx_remove_texturize" id="thx_remove_texturize" type="checkbox" value="1"';
		checked( get_option( 'thx_remove_texturize', 1 ), 1 );
		echo ' />引用符などの自動変換機能を解除する</p>';
}
function thx_wao_space_callback() {
		echo '<p><input name="thx_wao_space" id="thx_wao_space" type="checkbox" value="1"';
		checked( get_option( 'thx_wao_space', 1 ), 1 );
		echo ' />和文と欧文の間にアキを設ける</p>';
}
function thx_counted_heading_callback() {
		echo '<p><input name="thx_counted_heading" id="thx_counted_heading" type="checkbox" value="1"';
		checked( get_option( 'thx_counted_heading', 1 ), 1 );
		echo ' />見出しにカウンターを付加する</p>';
}
