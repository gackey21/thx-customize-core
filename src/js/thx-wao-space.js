/* .entry-content内の<p>、<li>にある半角英数字記号文字(列)にspan */
jQuery(function(){
  jQuery('.entry-content p,.entry-content li[class != "blocks-gallery-item"]').each(function() {
		jQuery(this).html(
			jQuery(this).html().replace(
				/(<\/?[^>]+>)|([\s!-;=-~]+)/g,
				function(){
					if( arguments[1] ) return arguments[1] ;
					if( arguments[2] ) return '<span class = "thx_wao">' + arguments[2] + '</span>' ;
				}
			)
		);
	});
});
