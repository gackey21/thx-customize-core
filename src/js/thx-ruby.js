/* ルビ */
jQuery(window).on('load', function() {
	jQuery('ruby').each(function() {
		var fz = parseInt(jQuery(this).css('font-size'));
		var lh = parseInt(jQuery(this).css('line-height'));
		var ow = jQuery(this).outerWidth();
		var ruby_html;
		var yomigana;
		var yomigana_span = '<div class="thx_yomi';

		ruby_html = jQuery(this).html();
		yomigana = jQuery(this).children("rt").text();
		yomigana_length = yomigana.length * fz / 2;

		if (yomigana.length == 1) {
			yomigana_span += ' thx_yomi_mono';
		}

		if (yomigana_length + fz / 2 > ow) {
			yomigana_span += ' thx_yomi_long';
		}

		var ts = - (fz / 2);
		yomigana_span += '" style="top: ' + ts + 'px">';

		var q_space_length = (yomigana_length - ow) / fz * 2;
		var span_q_space = '';
		if (yomigana_length > ow) {
			span_q_space = '<span class="thx_quo_spc">';
			for (var i = 0; i < q_space_length; i++) {
				span_q_space += '&#x2005;'
			}
			span_q_space += '</span>'
		}
		for (var i = 0; i < yomigana.length; i++) {
			yomigana_span += '<span>' + yomigana.substr(i, 1) + '</span>';
		}
		yomigana_span += '</div>';
		jQuery(this).html(span_q_space + ruby_html + span_q_space + yomigana_span);
	});
});
