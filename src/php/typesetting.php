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
	//htmlをテキストとタグに分解
	$pairing = Thx_Customize_Core::html_split_text_tag( $the_content );

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
			.= preg_replace_callback_array(
				[
					//欧文の検索（ゼロスペースを含む場合は２文字以上）
					'@' .
						'[ !-;=-~\p{Ll}\x{200b}]{2,}' .
						'|' .
						'[ !-;=-~\p{Ll}]+' .
					'@uis' => function ( $match ) {
						return
							'<span class = "thx_wao_spc"> </span>' .
							'<span class = "thx_pwid">' .
								$match[0] .
							'</span>' .
							'<span class = "thx_wao_spc"> </span>';
					},
					//句読点の検索
					'{' .
						'[、。，．]' .
					'}uis' => function ( $match ) {
						return
							'<span class = "thx_punc_wrap">' .
								'<span class = "thx_punctuation">' .
									$match[0] .
								'</span>' .
							'</span>' .
							'<span class = "thx_clps_spc"> </span>';
					},
					//連続する句読点はぶら下がり対象外
					'{' .
						'(<span class = "thx_punc_wrap"><span class = "thx_punctuation">)' .
						'([、。，．])' .
						'(</span></span><span class = "thx_clps_spc"> </span><span class = "thx_punc_wrap"><span class = "thx_punctuation">)' .
						'([、。，．])' .
						'(</span></span>)' .
					'}uis' => function ( $match ) {
						return '<span class = "thx_punc_punc">' . $match[2] . $match[4] . '</span>';
					},
					'{' .
						'(<span class = "thx_punc_punc">)' .
						'([、。，．]+)' .
						'(</span><span class = "thx_clps_spc"> </span><span class = "thx_punc_wrap"><span class = "thx_punctuation">)' .
						'([、。，．])' .
						'(</span></span>)' .
					'}uis' => function ( $match ) {
						return '<span class = "thx_punc_punc">' . $match[2] . $match[4] . '</span>';
					},
					//中点の検索
					'{' .
						'[・：；]' .
					'}uis' => function ( $match ) {
						return
							'<span class = "thx_clps_spc"> </span>' .
							'<span class = "thx_mid_dot">' .
								$match[0] .
							'</span>' .
							'<span class = "thx_clps_spc"> </span>';
					},
					//括弧の検索
					'{' .
						'[（｛［〔「『【〈《]' .
					'}uis' => function ( $match ) {
						return
							'<span class = "thx_clps_spc"> </span>' .
							'<span class = "thx_opening_bracket">' .
								$match[0] .
							'</span>';
					},
					'{' .
						'[）｝］〕」』】〉》]' .
					'}uis' => function ( $match ) {
						return
							'<span class = "thx_closing_bracket">' .
								$match[0] .
							'</span>' .
							'<span class = "thx_clps_spc"> </span>';
					},
					//括弧内の和欧間スペースを除去（禁則対策）
					'{' .
						'(<span class = "thx_opening_bracket">[（｛［〔「『【〈《]</span>)' .
						'(<span class = "thx_wao_spc"> </span>)' .
					'}uis' => function ( $match ) {
						return $match[1];
					},
					'{' .
						'(<span class = "thx_wao_spc"> </span>)' .
						'(<span class = "thx_closing_bracket">[）｝］〕」』】〉》]</span>)' .
					'}uis' => function ( $match ) {
						return $match[2];
					},
					//連即する括弧のスペースを除去
					'{' .
						'(<span class = "thx_opening_bracket">[（｛［〔「『【〈《]</span>)' .
						'(<span class = "thx_clps_spc"> </span>)' .
						'(<span class = "thx_opening_bracket">[（｛［〔「『【〈《]</span>)' .
					'}uis' => function ( $match ) {
						return $match[1] . $match[3];
					},
					'{' .
						'(<span class = "thx_closing_bracket">[）｝］〕」』】〉》]</span>)' .
						'(<span class = "thx_clps_spc"> </span>)' .
						'(<span class = "thx_closing_bracket">[）｝］〕」』】〉》]</span>)' .
					'}uis' => function ( $match ) {
						return $match[1] . $match[3];
					},
					//閉じ括弧と句読点
					'{' .
						'(<span class = "thx_closing_bracket">[）｝］〕」』】〉》]</span>)' .
						'(<span class = "thx_clps_spc"> </span>)' .
						'(<span class = "thx_punc_wrap"><span class = "thx_punctuation">)' .
						'([、。，．])' .
						'(</span></span>)' .
					'}uis' => function ( $match ) {
						return $match[1] . $match[3] . $match[4] . $match[5];
					},
					'{' .
						'(<span class = "thx_closing_bracket">[）｝］〕」』】〉》]</span>)' .
						'(<span class = "thx_clps_spc"> </span>)' .
						'(<span class = "thx_punc_punc">)' .
						'([、。，．]+)' .
						'(</span>)' .
					'}uis' => function ( $match ) {
						return $match[1] . $match[3] . $match[4] . $match[5];
					},
					//句読点と閉じ括弧はぶら下がり対象外
					'{' .
						'(<span class = "thx_punc_wrap"><span class = "thx_punctuation">)' .
						'([、。，．])' .
						'(</span></span>)' .
						'(<span class = "thx_clps_spc"> </span>)' .
						'(<span class = "thx_closing_bracket">)' .
						'([）｝］〕」』】〉》])' .
						'(</span>)' .
					'}uis' => function ( $match ) {
						return '<span class = "thx_punc_clbr">' . $match[2] . $match[6] . '</span>';
					},
					//相殺スペースは和欧間スペースを吸収
					'{' .
						'(<span class = "thx_clps_spc"> </span>)' .
						'(<span class = "thx_wao_spc"> </span>)' .
					'}uis' => function ( $match ) {
						return $match[1];
					},
					'{' .
						'(<span class = "thx_wao_spc"> </span>)' .
						'(<span class = "thx_clps_spc"> </span>)' .
					'}uis' => function ( $match ) {
						return $match[2];
					},
					//欧文と役物以外を全角処理に
					'@' .
						'[^ !-~\p{Ll}、。，．・：；（｛［〔「『【〈《）｝］〕」』】〉》]{2,}' .
						'|' .
						'[^ !-~\p{Ll}\x{200b}、。，．・：；（｛［〔「『【〈《）｝］〕」』】〉》]+' .
					'@uis' => function ( $match ) {
						return '<span class = "thx_fwid">' . $match[0] . '</span>';
					},
					//ゼロスペース処理
					'{' .
						'[\x{200b}]+' .
					'}uis' => function ( $match ) {
						return '<span class = "thx_zero_spc">' . $match[0] . '</span>';
					},
				],
				$str
			);//$the_content .= preg_replace_callback_array()
		}//else ( '</style>' === $tag )
		$the_content .= $tag;
	}//foreach ( $pairing as $value )
	return $the_content;
}//thx_typesetting( $the_content )

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
