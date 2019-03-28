<?php if ( ! defined( 'ABSPATH' ) ) exit;
//プラグイン名
define('PLUG_IN_NAME', 'thx.jp/');
$thx_cc_option = get_option('thx_cc_option');
if( !$thx_cc_option ) {
	$thx_cc_option = array(
		'antialiase' => 1,
		'text_size_adjust' => 1,
		'remove_texturize' => 1,
		'wao_space' => 1,
		'wao_space_js_php' => 'jQuery',
		'ruby' => 1,
		// 'counted_heading' => 0,
		'content_replace' => 0,
		'content_replace_array' => '',
		'js_tag' => 0,
		'js_async_defer' => 'async',
		'js_async_defer_array' => '',
	);
	update_option( 'thx_cc_option', $thx_cc_option );
}

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
			<?php do_settings_sections('thx_expand_settings'); // ページ名 ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
// 管理画面の作成
function thx_settings_init() {
	register_setting('thx_settings-group', 'thx_cc_option');

	//セクション設定
	add_settings_section(
		'thx_settings_section', // セクション名
		'基本機能の設定', // タイトル
		'thx_settings_section_callback', // echo '<p>プラグインのON/OFFを切り替えます。</p>';
		'thx_settings' // このセクションを表示するページ名。do_settings_sectionsで設定
	);
	function thx_settings_section_callback() {
		echo '<p>各機能のON/OFFを切り替えます。</p>';
	}

	//アンチエイリアス
	add_settings_field(
		'thx_antialiase',
		'アンチエイリアス処理',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_array_name' => 'antialiase',
			'comment' => '表示される文字にアンチエイリアス処理を施す',
			'add' => ''
		)
	);

	//テキストの自動拡大
	add_settings_field(
		'thx_text_size_adjust',
		'テキストの自動拡大',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_array_name' => 'text_size_adjust',
			'comment' => 'テキストの自動拡大を無効化',
			'add' => ''
		)
	);

	//引用符の解除
	add_settings_field(
		'thx_remove_texturize',
		'引用符の解除',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_array_name' => 'remove_texturize',
			'comment' => '引用符などの自動変換機能を解除する',
			'add' => ''
		)
	);

	//ルビ
	add_settings_field(
		'thx_ruby',
		'行間の崩れないルビ',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_array_name' => 'ruby',
			'comment' => '行間の崩れないルビを使用する',
			'add' => ''
		)
	);

	//和欧間スペース
	add_settings_field(
		'thx_wao_space',
		'和欧間スペース',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_array_name' => 'wao_space',
			'comment' => '和文と欧文の間にアキを設ける',
			// 'add' => 'thx_wao_space_js_php_callback'
			'add' => 'thx_radio_callback',
			'arg' => array(
				'option_array_name' => 'wao_space_js_php',
				'comment' => array(
					'jQuery' => 'jQuery',
					'php' => 'php',
				),
				'add' => '',
			)
		)
	);

	// //見出しのカウンター
	// add_settings_field(
	// 	'thx_counted_heading',
	// 	'見出しのカウンター',
	// 	'thx_single_checkbox_callback',
	// 	'thx_settings',
	// 	'thx_settings_section',
	// 	array(
	// 		'option_array_name' => 'counted_heading',
	// 		'comment' => '見出しにカウンターを付加する',
	// 		'add' => ''
	// 	)
	// );

	//拡張機能の設定
	add_settings_section(
		'thx_expand_settings_section', // セクション名
		'拡張機能の設定', // タイトル
		'thx_expand_settings_section_callback', // echo '<p>プラグインのON/OFFを切り替えます。</p>';
		'thx_expand_settings' // このセクションを表示するページ名。do_settings_sectionsで設定
	);
	function thx_expand_settings_section_callback() {
		echo '<p>検証中の機能です。</p>';
	}

	//コンテンツの置き換え
	add_settings_field(
		'thx_content_replace',
		'コンテンツの置き換え（β版）',
		'thx_single_checkbox_callback',
		'thx_expand_settings',
		'thx_expand_settings_section',
		array(
			'option_array_name' => 'content_replace',
			'comment' => '出力されるHTMLを正規表現で置換する',
			// 'add' => 'thx_content_replace_array_callback',
			// 'arg' => '',
			'add' => 'thx_textarea_callback',
			'arg' => array(
				'option_array_name' => 'content_replace_array',
				'comment' => '↓json形式で入力',
				'placeholder' => '{&quot;正規表現&quot;:&quot;置換文字&quot;,･･･}',
				'add' => '',
			)
		)
	);

	//jQueryの非同期読み込み
	add_settings_field(
		'thx_js_tag',
		'jQueryの非同期読み込み（β版）',
		'thx_single_checkbox_callback',
		'thx_expand_settings',
		'thx_expand_settings_section',
		array(
			'option_array_name' => 'js_tag',
			'comment' => 'html内の&lt;script&gt;タグを非同期で読み込む',
			'add' => 'thx_radio_callback',
			'arg' => array(
				'option_array_name' => 'js_async_defer',
				'comment' => array(
					'async' => 'async',
					'defer' => 'defer',
				),
				'add' => 'thx_textarea_callback',
				'arg' => array(
					'option_array_name' => 'js_async_defer_array',
					'comment' => '↓除外項目を改行区切りで入力',
					'placeholder' => '',
					'add' => '',
				)
			)
		)
	);
}//function thx_settings_init()

// コールバック
function thx_single_checkbox_callback($args) {
	$thx_cc_option = get_option('thx_cc_option');
	$option_name = $args['option_array_name'];
	$name_id = 'name="thx_cc_option['.$option_name.']" id="thx_'.$option_name.'"';
	?>
	<p>
		<input type="hidden" <?=$name_id?> value="0" />
		<input type="checkbox" <?=$name_id?> value="1"
		<?php checked( $thx_cc_option[$option_name], 1 ); ?>
		/><?=$args['comment']?>
	</p>
	<?php
	if ($args['add']) {
		call_user_func($args['add'], $args['arg']);
	}
}
function thx_radio_callback($args) {
	$thx_cc_option = get_option('thx_cc_option');
	$option_name = $args['option_array_name'];
	$key_comment = $args['comment'];
	$name_id = 'name="thx_cc_option['.$option_name.']" id="thx_'.$option_name.'"';
	?>
	<?php foreach ($key_comment as $key => $comment): ?>
	<p>
		<input type="radio" <?=$name_id?> value=<?=$key?>
		<?php checked( $thx_cc_option[$option_name], $key ); ?>
		/>
		<?=$comment?>
	</p>
	<?php endforeach; ?>
	<?php
	if ($args['add']) {
		call_user_func($args['add'], $args['arg']);
	}
}
function thx_textarea_callback($args) {
	$thx_cc_option = get_option('thx_cc_option');
	$option_name = $args['option_array_name'];
	$comment = $args['comment'];
	$placeholder = $args['placeholder'];
	$name_id = 'name="thx_cc_option['.$option_name.']" id="thx_'.$option_name.'"';
	?>
	<?php if ($comment): ?>
		<p><?=$comment?></p>
	<?php endif; ?>
	<textarea <?=$name_id?> cols="80" rows="4" placeholder=<?=$placeholder?>>
<?php echo $thx_cc_option[$option_name]; ?></textarea>
	<?php
	// var_dump($thx_cc_option);
}
