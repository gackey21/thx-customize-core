<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//半角スペース用にKosugiをエンキュー
function enqueue_kosugi_space() {
	wp_enqueue_style( 'google-webfont-style', '//fonts.googleapis.com/css?family=Kosugi&text=_ 　' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_kosugi_space' );

//簡易的な日本語組版
function thx_typesetting( $the_content ) {
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
	foreach ( $pairing as $value ) {
		$str = trim( $value[0] );
		$tag = $value[1];

		if (
			( '</style>' === $tag )
			||
			( '</rt>' === $tag )
			||
			( '</li>' === $tag )
		) {
			$the_content .= $str;
		} else {
			$the_content
			.= preg_replace(
				'{[^ !-~\p{Ll}、。，．・：；（｛［〔「『【〈《）｝］〕」』】〉》]+}uis',
				'<span class = "thx_fwid">$0</span>',
				preg_replace_callback_array(
					[
						'{[ !-;=-~\p{Ll}]+}uis' => function ( $match ) {
							return '<span class = "thx_wao_spc"> </span><span class = "thx_pwid">' . $match[0] . '</span><span class = "thx_wao_spc"> </span>';
						},
						'{[、。，．]}uis'           => function ( $match ) {
							return '<span class = "thx_punc_wrap"><span class = "thx_punctuation">' . $match[0] . '</span></span><span class = "thx_clps_spc"> </span>';
						},
						'{[・：；]}uis'            => function ( $match ) {
							return '<span class = "thx_clps_spc"> </span><span class = "thx_mid_dot">' . $match[0] . '</span><span class = "thx_clps_spc"> </span>';
						},
						'{[（｛［〔「『【〈《]}uis'      => function ( $match ) {
							return '<span class = "thx_clps_spc"> </span><span class = "thx_opening_bracket">' . $match[0] . '</span>';
						},
						'{[）｝］〕」』】〉》]}uis'      => function ( $match ) {
							return '<span class = "thx_closing_bracket">' . $match[0] . '</span><span class = "thx_clps_spc"> </span>';
						},
						'{(<span class = "thx_opening_bracket">[（｛［〔「『【〈《]</span>)(<span class = "thx_wao_spc"> </span>)}uis' => function ( $match ) {
							return $match[1];
						},
						'{(<span class = "thx_wao_spc"> </span>)(<span class = "thx_closing_bracket">[）｝］〕」』】〉》]</span>)}uis' => function ( $match ) {
							return $match[2];
						},
						'{(<span class = "thx_clps_spc"> </span>)(<span class = "thx_wao_spc"> </span>)}uis' => function ( $match ) {
							return $match[1];
						},
						'{(<span class = "thx_wao_spc"> </span>)(<span class = "thx_clps_spc"> </span>)}uis' => function ( $match ) {
							return $match[2];
						},
					],
					$str
				)//preg_replace_callback_array()
			);
		}//else ( '</style>' === $tag )
		$the_content .= $tag;
	}//foreach ( $pairing as $value )
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
