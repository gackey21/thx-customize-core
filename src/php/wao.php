<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//和欧間スペース
function wao_space( $the_content ) {
	//alt内の「>」を文字参照に
	if ( preg_match_all( '{alt="[^\"]*"}uis', $the_content, $m_alt ) ) {
		foreach ( $m_alt as $value ) {
			// var_dump( '<pre>' . $value . '</pre>' );
			$alt_amp     = preg_replace( '{(>)}is', '&gt;', $value );
			$the_content = str_replace( $value, $alt_amp, $the_content );
		}
	}

	//htmlをタグとテキストに分解・ペアリング
	$tag_match = '{(<.*?>)}uis';
	$pairing   = array_chunk(
		preg_split(
			$tag_match,
			$the_content,
			-1,
			PREG_SPLIT_DELIM_CAPTURE
		),
		2
	);

	//ペア補充（notice対策）
	$count                 = count( $pairing );
	$pairing[ $count - 1 ] = array( ' ', ' ' );

	//ペアリングをspanしながら結合
	$the_content = '';
	$ltn_match   = '{[ !-;=-~\p{Ll}]+}uis';
	$ltn_replece = '<span class = "thx_wao">$0</span>';
	// var_dump('<pre>');
	// var_dump($pairing);
	// var_dump('</pre>');
	foreach ( $pairing as $value ) {
		$str = trim( $value[0] );
		$tag = $value[1];
		if ( '</style>' === $tag ) {
			$the_content .= $str . $tag;
		} else {
			$the_content
			.= preg_replace(
				$ltn_match,
				$ltn_replece,
				$str
			) . $tag;
		}
	}
	return $the_content;
}//wao_space($the_content)

//jsの出力
// function wao_space_js() {
// 	$js_url = plugins_url( '../js/thx-wao-space.js', __FILE__ );
// 	$tCC = new thx_Customize_Core();
// 	$tCC -> enqueue_file_script($js_url);
// }

//cssの出力
// function wao_space_css() {
// 	$css_url = plugins_url( '../css/thx-wao-space.css', __FILE__ );
// 	$tCC = new thx_Customize_Core();
// 	thx_Customize_Core::$css_amp_url[] = plugins_url( '../css/thx-wao-space.css', __FILE__ );
// 	$tCC -> enqueue_file_style($css_url);
// 	// thx_Customize_Core::$css_amp_url[] = $css_url;
// }

//テーマがCocoonの場合はampに出力
// if (get_template() == 'cocoon-master') {
// 	// thx_Customize_Core::$css_amp_url[]
// 	// 	= plugins_url( '../css/thx-wao-space.css', __FILE__ );
// 	add_filter('amp_all_css', 'echo_amp_css');
// }
