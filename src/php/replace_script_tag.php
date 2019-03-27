<?php if ( ! defined( 'ABSPATH' ) ) exit;
//jQueryの非同期読み込み
if ( !(is_admin() ) ) {
}
function replace_script_tag ( $tag ) {
	$thx_cc_option = get_option('thx_cc_option');
	// var_dump($tag);
	if ( !preg_match( '/\b(defer|async)\b/', $tag ) ) {
		if ( !preg_match( '/highlight-js/', $tag ) ) {
			if ( !preg_match( '/.carousel-content/', $tag ) ) {
				return str_replace(
					"type='text/javascript'",
					$thx_cc_option['async_js'],
					$tag
				);
			}
		}
	}
	return $tag;
}
