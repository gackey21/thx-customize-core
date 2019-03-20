<?php if ( ! defined( 'ABSPATH' ) ) exit;
//和欧間スペース
function wao_space($the_content) {
	//alt内の「>」を文字参照に
	// if (preg_match_all("{(alt=\".*?[\n\"])}is", $the_content, $m_alt)) {
	// 	foreach ($m_alt[1] as $value) {
	// 		var_dump('<pre>'.$value.'</pre>');
	// 		$alt_amp = preg_replace('{(>)}is', '&gt;',$value);
	// 		$the_content = str_replace($value, $alt_amp, $the_content);
	// 	}
	// }

	//htmlをタグとテキストに分解・ペアリング
	$tag_match = "{(<.*?>)}uis";
	$pairing = array_chunk(
		preg_split(
			$tag_match,
			$the_content,
			-1,
			PREG_SPLIT_DELIM_CAPTURE
		),
		2
	);

	//ペア補充（notice対策）
	$count = count($pairing);
	$pairing[$count - 1] = array(' ', ' ');

	//ペアリングをspanしながら結合
	$the_content = '';
	$ltn_match = '{[ !-;=-~\p{Ll}]+}uis';
	$ltn_replece = '<span class = "thx_wao">$0</span>';
	// var_dump('<pre>');
	// var_dump($pairing);
	// var_dump('</pre>');
	foreach ($pairing as $value) {
		$str = trim($value[0]);
		$tag = $value[1];
		$the_content
		.= preg_replace(
			$ltn_match,
			$ltn_replece,
			$str
		).$tag;
	}
	return $the_content;
}//wao_space($the_content)

//cssの出力
function wao_space_css() {
	$css_url = plugins_url( '../css/thx-wao-space.css', __FILE__ );
	$tCC = new thx_Customize_Core();
	$tCC -> enqueue_file_style($css_url);
}

//テーマがCocoonの場合はampに出力
if (get_template() == 'cocoon-master') {
	thx_Customize_Core::$css_amp_url[]
		= plugins_url( '../css/thx-wao-space.css', __FILE__ );
	add_filter('amp_all_css', 'echo_amp_css');
}

function echo_amp_css($css) {//ampインライン出力用のcssファイルを選択
	foreach (thx_Customize_Core::$css_amp_url as $css_url) {
		var_dump($css_url);
		$css .= css_url_to_css_minify_code($css_url);
	}
	// $css_url = plugins_url( '../css/thx-wao-space.css', __FILE__ );
	// $css .= css_url_to_css_minify_code($css_url);
	echo $css;
}
