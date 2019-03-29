<?php if ( ! defined( 'ABSPATH' ) ) exit;
//jQueryの非同期読み込み
function replace_script_tag ( $tag ) {
	$thx_cc_option = get_option('thx_cc_option');
	if (!preg_match('/\b(defer|async)\b/', $tag)) {
		$tag = str_replace(
			"type='text/javascript'",
			$thx_cc_option['js_async_defer'],
			$tag
		);
	}
	$exclusion = str_replace(
		array("\r\n", "\r", "\n"),
		"\n",
		$thx_cc_option['js_async_defer_array']
	);
	if ($exclusion) {
		// var_dump($exclusion);
		$exclusion = explode("\n", $exclusion);
		foreach ($exclusion as $value) {
			if (strpos($tag,$value) !== false) {
				$tag = str_replace(
					$thx_cc_option['js_async_defer'],
					'',
					$tag
				);
			}
		}
	}
	return $tag;
}