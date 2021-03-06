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
					'#' .
						'[ !-;=-~\p{Ll}\x{200b}]{2,}' .
						'|' .
						'[ !-;=-~\p{Ll}]+' .
					'#uis' => function ( $match ) {
						return
							'<span class = "thx_wao_spc"> </span>' .
							'<span class = "thx_pwid">' .
								$match[0] .
							'</span>' .
							'<span class = "thx_wao_spc"> </span>';
					},
					//句読点と閉じ括弧の検索（後端単数の句読点のみ、ぶら下がり）
					'#' .
						'([、。，．）｝］〕」』】〉》]{2,})' .
						'|' .
						'([、。，．])' .
						'|' .
						'([）｝］〕」』】〉》])' .
					'#uis' => function ( $match ) {
						//句読点や閉じ括弧が２文字以上続いている場合
						if ( $match[1] ) {
							//終わりの２文字が同一ではなく、句読点で終わっていれば、ぶら下がり処理
							if (
								( mb_substr( $match[1], -1, 1 ) !== mb_substr( $match[1], -2, 1 ) )
								&&
								(
									'、' === mb_substr( $match[1], -1 )
									||
									'。' === mb_substr( $match[1], -1 )
									||
									'，' === mb_substr( $match[1], -1 )
									||
									'．' === mb_substr( $match[1], -1 )
								)
							) {
								return
									'<span class = "thx_closing_mark">' .
										mb_substr( $match[1], 0, -1 ) .
									'</span>' .
									'<span class = "thx_punc_wrap">' .
										'<span class = "thx_punctuation">' .
											mb_substr( $match[1], -1 ) .
										'</span>' .
									'</span>' .
									'<span class = "thx_clps_spc"> </span>';
							} else { //ぶら下がり不要
								return
									'<span class = "thx_closing_mark">' .
										$match[1] .
									'</span>' .
									'<span class = "thx_clps_spc"> </span>';
							}
						} elseif ( $match[2] ) { //句読点が１文字のみは、ぶら下がり
							return
								'<span class = "thx_punc_wrap">' .
									'<span class = "thx_punctuation">' .
										$match[2] .
									'</span>' .
								'</span>' .
								'<span class = "thx_clps_spc"> </span>';
						} else { //ぶら下がり不要
							return
							'<span class = "thx_closing_mark">' .
								$match[3] .
							'</span>' .
							'<span class = "thx_clps_spc"> </span>';
						}
					},
					//中点の検索
					'#' .
						'[・：；]+' .
					'#uis' => function ( $match ) {
						return
							'<span class = "thx_clps_spc"> </span>' .
							'<span class = "thx_mid_dot">' .
								$match[0] .
							'</span>' .
							'<span class = "thx_clps_spc"> </span>';
					},
					//開き括弧の検索
					'#' .
						'[（｛［〔「『【〈《]+' .
					'#uis' => function ( $match ) {
						return
							'<span class = "thx_clps_spc"> </span>' .
							'<span class = "thx_opening_mark">' .
								$match[0] .
							'</span>';
					},
					//括弧内の和欧間スペースを除去（禁則対策）
					'#' .
						'(<span class = "thx_opening_mark">[（｛［〔「『【〈《]+</span>)' .
						'(<span class = "thx_wao_spc"> </span>)' .
					'#uis' => function ( $match ) {
						return $match[1];
					},
					'#' .
						'(<span class = "thx_wao_spc"> </span>)' .
						'(<span class = "thx_closing_mark">[、。，．）｝］〕」』】〉》]+</span>)' .
					'#uis' => function ( $match ) {
						return $match[2];
					},
					//欧文と役物以外を全角処理に
					'#' .
						'[^ !-~\p{Ll}、。，．・：；（｛［〔「『【〈《）｝］〕」』】〉》]{2,}' .
						'|' .
						'[^ !-~\p{Ll}\x{200b}、。，．・：；（｛［〔「『【〈《）｝］〕」』】〉》]+' .
					'#uis' => function ( $match ) {
						return '<span class = "thx_fwid">' . $match[0] . '</span>';
					},
					//ゼロスペース処理
					'#' .
						'[\x{200b}]+' .
					'#uis' => function ( $match ) {
						return '<span class = "thx_zero_spc">' . $match[0] . '</span>';
					},
				],
				$str
			);//$the_content .= preg_replace_callback_array()
		}//else ( '</style>' === $tag )
		$the_content .= $tag;
	}//foreach ( $pairing as $value )

	//重複するthx_clps_spcを削除
	$the_content
		= str_replace(
			'<span class = "thx_clps_spc"> </span><span class = "thx_clps_spc"> </span>',
			'<span class = "thx_clps_spc"> </span>',
			$the_content
		);

	//相殺スペースは和欧間スペースを吸収
	$the_content
		= str_replace(
			array(
				'<span class = "thx_clps_spc"> </span><span class = "thx_wao_spc"> </span>',
				'<span class = "thx_wao_spc"> </span><span class = "thx_clps_spc"> </span>',
			),
			'<span class = "thx_clps_spc"> </span>',
			$the_content
		);

	//括弧内の<a>などを禁則対策
	$the_content
		= preg_replace_callback_array(
			[
				'#' .
					'(<span class = "thx_opening_mark">[（｛［〔「『【〈《]+</span>)' .
					'(<[^>]*>)' .
					'(<span class = "thx_wao_spc"> </span>)' .
				'#uis' => function ( $match ) {
					return $match[1] . $match[2];
				},
				'#' .
					'(<span class = "thx_wao_spc"> </span>)' .
					'(<[^>]*>)' .
					'(<span class = "thx_closing_mark">[、。，．）｝］〕」』】〉》]+</span>)' .
				'#uis' => function ( $match ) {
					return $match[2] . $match[3];
				},
			],
			$the_content
		);

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
