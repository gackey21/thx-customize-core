<?php if ( ! defined( 'ABSPATH' ) ) exit;
//cssの出力
function ruby_css() {
	$css_url = plugins_url( '../css/thx-ruby.css', __FILE__ );
	$tCC = new thx_Customize_Core();
	$tCC -> enqueue_file_style($css_url);
}

//jsの出力
function ruby_js() {
	$js_url = plugins_url( '../js/thx-ruby.js', __FILE__ );
	$tCC = new thx_Customize_Core();
	$tCC -> enqueue_file_script($js_url);
}

//テーマがCocoonの場合はampに出力
// if (get_template() == 'cocoon-master') {
if (class_exists('thx_Cocoon_Option')) {
	thx_Customize_Core::$css_amp_url[]
		= plugins_url( '../css/thx-ruby.css', __FILE__ );
	add_filter('amp_all_css', 'echo_amp_css');
}
