/* ルビ */
jQuery(window).on('load', function() {
	jQuery('ruby').each(function() {
		var fz = parseInt(jQuery(this).css('font-size'));
		var lh = parseInt(jQuery(this).css('line-height'));
		var ow = jQuery(this).outerWidth();
				ow = Math.round(ow / fz * 2) * fz / 2;
		var ruby_html       = jQuery(this).html();
		var yomigana        = jQuery(this).children("rt").text();
		var yomigana_length = yomigana.length * fz / 2;


		// <div class="thx_yomi…>の作成
		var yomigana_span = '<div class="thx_yomi';

		if ((yomigana.length == 1) && (ow > fz)) {
			yomigana_span += ' thx_yomi_mono';
		}

		if (yomigana_length + fz / 2 > ow) {
			yomigana_span += ' thx_yomi_long';
		}

		yomigana_span += '">';

		// よみがなのループ
		for (var i = 0; i < yomigana.length; i++) {
			yomigana_span += '<span>' + yomigana.substr(i, 1) + '</span>';
		}

		yomigana_span += '</div>';// <div class="thx_yomi…>


		// <span class="thx_quo_spc">の作成
		var q_space_length = (yomigana_length - ow) / fz * 2;
		var span_q_space = '';
		if (yomigana_length > ow) {
			span_q_space = '<span class="thx_quo_spc">';
			for (var i = 0; i < q_space_length; i++) {
				span_q_space += '&#x2005;';
			}
			span_q_space += '</span>';
		}


		// htmlの合成
		jQuery(this).html(span_q_space + ruby_html + span_q_space + yomigana_span);
	});
});
