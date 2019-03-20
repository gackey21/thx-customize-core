<?php if ( ! defined( 'ABSPATH' ) ) exit;
//コンテンツ変更
function content_replace($the_content) {
	$match_replace = json_decode( get_option( 'thx_content_replace_array' ) , true ) ;
	foreach ($match_replace as $match => $replace) {
		$the_content = preg_replace($match, $replace, $the_content);
	}
	return $the_content;
}
