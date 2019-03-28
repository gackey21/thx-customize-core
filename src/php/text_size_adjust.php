<?php if ( ! defined( 'ABSPATH' ) ) exit;
//cssの出力
function text_size_adjust_css() {
	$css_url = plugins_url( '../css/thx-text-size-adjust.css', __FILE__ );
	$tCC = new thx_Customize_Core();
	$tCC -> enqueue_file_style($css_url);
}

//テーマがCocoonの場合はampに出力
// if (get_template() == 'cocoon-master') {
if (class_exists('thx_Cocoon_Option')) {
	thx_Customize_Core::$css_amp_url[]
		= plugins_url( '../css/thx-text-size-adjust.css', __FILE__ );
	add_filter('amp_all_css', 'echo_amp_css');
}
