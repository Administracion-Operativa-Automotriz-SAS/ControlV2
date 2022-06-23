var onloadCallback = function() {
	var recaptchas = document.querySelectorAll('div[class=g-recaptcha]');
				
		html_element_id = grecaptcha.render( "captcha", {
		'sitekey' : '6Ld-3OAUAAAAAI49_SbKJQC2qB0HOi3wsaCGsCIr',
		});
	
}