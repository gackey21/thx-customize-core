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
		// 'hook_customize' => 0,
		// 'hook_customize_array' => '',
		// 'content_replace' => 0,
		// 'content_replace_array' => '',
		// 'js_tag' => 0,
		// 'js_async_defer' => 'async',
		// 'js_async_defer_array' => '',
	);
	update_option( 'thx_cc_option', $thx_cc_option );
}//if( !$thx_cc_option )

function thx_admin_menu() {
	//メインメニューに追加する
	add_menu_page(
		PLUG_IN_NAME.' の設定',
		PLUG_IN_NAME,
		'manage_options',
		'thx-jp-customize-core',
		'thx_settings_plugin_options',
		'dashicons-admin-plugins',
		30
	);
	//サブメニュー作成
	add_submenu_page(
		'thx-jp-customize-core',
		'thx.jp/ の設定',
		'thx.jp/',
		'manage_options',
		'thx-jp-customize-core',
		'thx_settings_plugin_options'
	);
}//thx_admin_menu

// フォームの枠組を出力する
function thx_settings_plugin_options() {
	// ユーザーが必要な権限を持つか確認する必要がある
	if (!current_user_can('manage_options'))  {
		wp_die( __('このページにアクセスする管理者権限がありません。') );
	}
	?>
	<div class="wrap">
		<form action="options.php" method="post">
			<?php settings_fields('thx_settings-group'); // グループ名 ?>
			<?php do_settings_sections('thx_settings'); // ページ名 ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php if (isset($_GET['settings-updated'])): ?>
		<?php if ($_GET['settings-updated']): ?>
			<div class="updated notice is-dismissible">
				<p><strong>設定を保存しました。</strong></p>
			</div>
		<?php endif; ?>
	<?php endif;//isset ?>
	<?php
	// var_dump($thx_cc_option);
}

// 管理画面の作成
function thx_settings_init() {
	register_setting('thx_settings-group', 'thx_cc_option');

	//セクション設定
	add_settings_section(
		'thx_settings_section', // セクション名
		'文字組に関する設定', // タイトル
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
		// 'thx_checkbox_callback',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name' => 'thx_cc_option',
			'option_array_name' => 'antialiase',
			// 'comment' => array(
			// 	'1' => '表示される文字にアンチエイリアス処理を施す',
			// ),
			'comment' => '表示される文字にアンチエイリアス処理を施す',
			'add' => ''
		)
	);

	//テキストの自動拡大
	add_settings_field(
		'thx_text_size_adjust',
		'テキストの自動拡大',
		// 'thx_checkbox_callback',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name' => 'thx_cc_option',
			'option_array_name' => 'text_size_adjust',
			// 'comment' => array(
			// 	'1' => 'モバイル端末でのテキスト自動拡大を無効化',
			// ),
			'comment' => 'モバイル端末でのテキスト自動拡大を無効化',
			'add' => ''
		)
	);

	//引用符の解除
	add_settings_field(
		'thx_remove_texturize',
		'引用符の解除',
		// 'thx_checkbox_callback',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name' => 'thx_cc_option',
			'option_array_name' => 'remove_texturize',
			// 'comment' => array(
			// 	'1' => '引用符などの自動変換機能を解除する',
			// ),
			'comment' => '引用符などの自動変換機能を解除する',
			'add' => ''
		)
	);

	//ルビ
	add_settings_field(
		'thx_ruby',
		'行間の崩れないルビ',
		// 'thx_checkbox_callback',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name' => 'thx_cc_option',
			'option_array_name' => 'ruby',
			// 'comment' => array(
			// 	'1' => '行間の崩れないルビを使用する',
			// ),
			'comment' => '行間の崩れないルビを使用する',
			'add' => ''
		)
	);

	//和欧間スペース
	add_settings_field(
		'thx_wao_space',
		'和欧間スペース',
		// 'thx_checkbox_callback',
		'thx_single_checkbox_callback',
		'thx_settings',
		'thx_settings_section',
		array(
			'option_name' => 'thx_cc_option',
			'option_array_name' => 'wao_space',
			// 'comment' => array(
			// 	'1' => '和文と欧文の間にアキを設ける',
			// ),
			'comment' => '和文と欧文の間にアキを設ける',
			'add' => 'thx_radio_callback',
			'arg' => array(
				'option_name' => 'thx_cc_option',
				'option_array_name' => 'wao_space_js_php',
				'comment' => array(
					'jQuery' => 'jQuery',
					'php' => 'php',
				),
				'add' => '',
			)
		)
	);
}//function thx_settings_init()

// コールバック
function thx_single_checkbox_callback($args) {
	// $thx_cc_option = get_option('thx_cc_option');
	// $option_name = $args['option_array_name'];
	if ($args['option_array_name']) {
		$option_array_name = $args['option_array_name'];
		$option_name = $args['option_name'];
		$thx_option = get_option($option_name)[$option_array_name];
		$option_name = $option_name.'['.$option_array_name.']';
		// $option_name = 'thx_cc_option['.$option_array_name.']';
		// $thx_option = get_option('thx_cc_option')[$option_array_name];
	} else {
		$option_name = $args['option_name'];
		$thx_option = get_option($option_name);
	}
	$name_id = 'name="'.$option_name.'" id="'.$option_name.'"';
	// $name_id = 'name="thx_cc_option['.$option_name.']" id="thx_'.$option_name.'"';
	?>
	<p>
		<input type="hidden" <?=$name_id?> value="0" />
		<input type="checkbox" <?=$name_id?> value="1"
		<?php checked( $thx_option, 1 ); ?>
		/><?=$args['comment']?>
	</p>
	<?php
	if ($args['add']) {
		call_user_func($args['add'], $args['arg']);
	}
}
function thx_checkbox_callback($args) {
	if ($args['option_array_name']) {
		$option_array_name = $args['option_array_name'];
		$option_name = $args['option_name'];
		$thx_option = get_option($option_name)[$option_array_name];
		$option_name = $option_name.'['.$option_array_name.']';
		// $option_name = 'thx_cc_option['.$option_array_name.']';
		// $thx_option = get_option('thx_cc_option')[$option_array_name];
	} else {
		$option_name = $args['option_name'];
		$thx_option = get_option($option_name);
	}
	$name_comment_flag = $args['comment'];
	?>
	<?php foreach ($name_comment_flag as $name => $comment_flag): ?>
		<?php foreach ($comment_flag as $comment => $flag): ?>
			<?php $name_id = 'name="'.$option_name.'['.$name.']" id="'.$option_name.'"'; ?>
			<p>
				<input type="hidden" <?=$name_id?> value="0" />
				<input type="checkbox" <?=$name_id?> value="1"
				<?php checked( $thx_option[$name], "1" ); ?>
				/>
				<?=$comment?>
			</p>
		<?php endforeach;//$comment => $flag ?>
	<?php endforeach;//$name => $comment_flag ?>
	<?php
	// var_dump($thx_option);
	if ($args['add']) {
		call_user_func($args['add'], $args['arg']);
	}
}
function thx_radio_callback($args) {
	if ($args['option_array_name']) {
		$option_array_name = $args['option_array_name'];
		$option_name = $args['option_name'];
		$thx_option = get_option($option_name)[$option_array_name];
		$option_name = $option_name.'['.$option_array_name.']';
		// $option_name = 'thx_cc_option['.$option_array_name.']';
		// $thx_option = get_option('thx_cc_option')[$option_array_name];
	} else {
		$option_name = $args['option_name'];
		$thx_option = get_option($option_name);
	}
	$key_comment = $args['comment'];
	$name_id = 'name="'.$option_name.'" id="'.$option_name.'"';
	?>
	<?php foreach ($key_comment as $key => $comment): ?>
	<p>
		<input type="radio" <?=$name_id?> value=<?=$key?>
		<?php checked( $thx_option, $key ); ?>
		/>
		<?=$comment?>
	</p>
	<?php endforeach; ?>
	<?php
	if ($args['add']) {
		call_user_func($args['add'], $args['arg']);
	}
	// var_dump(get_option('thx_cc_option'));
	// var_dump($thx_option);
}
function thx_textarea_callback($args) {
	if ($args['option_array_name']) {
		$option_array_name = $args['option_array_name'];
		$option_name = $args['option_name'];
		$thx_option = get_option($option_name)[$option_array_name];
		$option_name = $option_name.'['.$option_array_name.']';
		// $option_name = 'thx_cc_option['.$option_array_name.']';
		// $thx_option = get_option('thx_cc_option')[$option_array_name];
	} else {
		$option_name = $args['option_name'];
		$thx_option = get_option($option_name);
	}
	if (isset($args['rows'])) {
		$rows = $args['rows'];
	} else {
		$rows = '4';
	}
	$comment = $args['comment'];
	$placeholder = $args['placeholder'];
	$name_id = 'name="'.$option_name.'" id="'.$option_name.'"';
	// $thx_cc_option = get_option('thx_cc_option');
	// $option_name = $args['option_array_name'];
	// $comment = $args['comment'];
	// $placeholder = $args['placeholder'];
	// $name_id = 'name="thx_cc_option['.$option_name.']" id="thx_'.$option_name.'"';
	?>
	<?php if ($comment): ?>
		<p><?=$comment?></p>
	<?php endif; ?>
	<textarea <?=$name_id?> class="thx-monospace" cols="80" rows=<?=$rows?> placeholder=<?=$placeholder?>><?=$thx_option?></textarea>
	<?php
	// var_dump($thx_option);
}
