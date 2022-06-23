<?php
/*código php... */

header('Content-Type: text/html; charset=UTF-8');

echo "

<!DOCTYPE>
	<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		<!--// SITE META //-->
		<meta charset='UTF-8' />	
		<meta name='viewport' content='width=device-width, initial-scale=1.0' />
				
		<!--// PINGBACK //-->
		<link rel='pingback' href='https://www.creativografico.dev/webAOA/xmlrpc.php' />

		<!--// WORDPRESS HEAD HOOK //-->
		<title>Intranet AOA Colombia &#8211; AOA Colombia</title>
    <script>
        writeCookie();
        function writeCookie()
        {
            the_cookie = document.cookie;
            if( the_cookie ){
                if( window.devicePixelRatio >= 2 ){
                    the_cookie = 'pixel_ratio='+window.devicePixelRatio+';'+the_cookie;
                    document.cookie = the_cookie;
                }
            }
        }
    </script>
<link rel='dns-prefetch' href='//s.w.org' />
<link rel='alternate' type='application/rss+xml' title='AOA Colombia &raquo; Feed' href='https://www.creativografico.dev/webAOA/index.php/feed/' />
<link rel='alternate' type='application/rss+xml' title='AOA Colombia &raquo; Comments Feed' href='https://www.creativografico.dev/webAOA/index.php/comments/feed/' />
		<script type='text/javascript'>
			window._wpemojiSettings = {'baseUrl':'https:\/\/s.w.org\/images\/core\/emoji\/12.0.0-1\/72x72\/','ext':'.png','svgUrl':'https:\/\/s.w.org\/images\/core\/emoji\/12.0.0-1\/svg\/','svgExt':'.svg','source':{'concatemoji':'https:\/\/www.creativografico.dev\/webAOA\/wp-includes\/js\/wp-emoji-release.min.js?ver=5.2.5'}};
			!function(a,b,c){function d(a,b){var c=String.fromCharCode;l.clearRect(0,0,k.width,k.height),l.fillText(c.apply(this,a),0,0);var d=k.toDataURL();l.clearRect(0,0,k.width,k.height),l.fillText(c.apply(this,b),0,0);var e=k.toDataURL();return d===e}function e(a){var b;if(!l||!l.fillText)return!1;switch(l.textBaseline='top',l.font='600 32px Arial',a){case'flag':return!(b=d([55356,56826,55356,56819],[55356,56826,8203,55356,56819]))&&(b=d([55356,57332,56128,56423,56128,56418,56128,56421,56128,56430,56128,56423,56128,56447],[55356,57332,8203,56128,56423,8203,56128,56418,8203,56128,56421,8203,56128,56430,8203,56128,56423,8203,56128,56447]),!b);case'emoji':return b=d([55357,56424,55356,57342,8205,55358,56605,8205,55357,56424,55356,57340],[55357,56424,55356,57342,8203,55358,56605,8203,55357,56424,55356,57340]),!b}return!1}function f(a){var c=b.createElement('script');c.src=a,c.defer=c.type='text/javascript',b.getElementsByTagName('head')[0].appendChild(c)}var g,h,i,j,k=b.createElement('canvas'),l=k.getContext&&k.getContext('2d');for(j=Array('flag','emoji'),c.supports={everything:!0,everythingExceptFlag:!0},i=0;i<j.length;i++)c.supports[j[i]]=e(j[i]),c.supports.everything=c.supports.everything&&c.supports[j[i]],'flag'!==j[i]&&(c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&c.supports[j[i]]);c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&!c.supports.flag,c.DOMReady=!1,c.readyCallback=function(){c.DOMReady=!0},c.supports.everything||(h=function(){c.readyCallback()},b.addEventListener?(b.addEventListener('DOMContentLoaded',h,!1),a.addEventListener('load',h,!1)):(a.attachEvent('onload',h),b.attachEvent('onreadystatechange',function(){'complete'===b.readyState&&c.readyCallback()})),g=c.source||{},g.concatemoji?f(g.concatemoji):g.wpemoji&&g.twemoji&&(f(g.twemoji),f(g.wpemoji)))}(window,document,window._wpemojiSettings);
		</script>
		<style type='text/css'>
img.wp-smiley,
img.emoji {
	display: inline !important;
	border: none !important;
	box-shadow: none !important;
	height: 1em !important;
	width: 1em !important;
	margin: 0 .07em !important;
	vertical-align: -0.1em !important; 
	background: none !important;
	padding: 0 !important;
}
</style>
<link rel='stylesheet' id='wp-block-library-css'  href='css/style.css' type='text/css' media='all' />

	<link rel='stylesheet' id='layerslider-css'  href='https://www.creativografico.dev/webAOA/wp-content/plugins/LayerSlider/static/layerslider/css/layerslider.css?ver=6.9.0' type='text/css' media='all' />
<link rel='stylesheet' id='wp-block-library-css'  href='https://www.creativografico.dev/webAOA/wp-includes/css/dist/block-library/style.min.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='contact-form-7-css'  href='https://www.creativografico.dev/webAOA/wp-content/plugins/contact-form-7/includes/css/styles.css?ver=5.1.4' type='text/css' media='all' />
<link rel='stylesheet' id='wpsm_ac-font-awesome-front-css'  href='https://www.creativografico.dev/webAOA/wp-content/plugins/responsive-accordion-and-collapse/css/font-awesome/css/font-awesome.min.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='wpsm_ac_bootstrap-front-css'  href='https://www.creativografico.dev/webAOA/wp-content/plugins/responsive-accordion-and-collapse/css/bootstrap-front.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='rs-plugin-settings-css'  href='https://www.creativografico.dev/webAOA/wp-content/plugins/revslider/public/assets/css/rs6.css?ver=6.0.9' type='text/css' media='all' />
<style id='rs-plugin-settings-inline-css' type='text/css'>
#rs-demo-id {}
</style>
<link rel='stylesheet' id='wpsm_tabs_r-font-awesome-front-css'  href='https://www.creativografico.dev/webAOA/wp-content/plugins/tabs-responsive/assets/css/font-awesome/css/font-awesome.min.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='wpsm_tabs_r_bootstrap-front-css'  href='https://www.creativografico.dev/webAOA/wp-content/plugins/tabs-responsive/assets/css/bootstrap-front.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='wpsm_tabs_r_animate-css'  href='https://www.creativografico.dev/webAOA/wp-content/plugins/tabs-responsive/assets/css/animate.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='bootstrap-css'  href='https://www.creativografico.dev/webAOA/wp-content/themes/dante/css/bootstrap.min.css' type='text/css' media='all' />
<link rel='stylesheet' id='font-awesome-v5-css'  href='https://www.creativografico.dev/webAOA/wp-content/themes/dante/css/font-awesome.min.css?ver=5.10.1' type='text/css' media='all' />
<link rel='stylesheet' id='font-awesome-v4shims-css'  href='https://www.creativografico.dev/webAOA/wp-content/themes/dante/css/v4-shims.min.css' type='text/css' media='all' />
<link rel='stylesheet' id='ssgizmo-css'  href='https://www.creativografico.dev/webAOA/wp-content/themes/dante/css/ss-gizmo.css' type='text/css' media='all' />
<link rel='stylesheet' id='sf-main-css'  href='https://www.creativografico.dev/webAOA/wp-content/themes/dante/style.css' type='text/css' media='all' />
<link rel='stylesheet' id='sf-responsive-css'  href='https://www.creativografico.dev/webAOA/wp-content/themes/dante/css/responsive.css' type='text/css' media='all' />
<script type='text/javascript'>
/* <![CDATA[ */
var LS_Meta = {'v':'6.9.0'};
/* ]]> */
</script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/LayerSlider/static/layerslider/js/greensock.js?ver=1.19.0'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/LayerSlider/static/layerslider/js/layerslider.kreaturamedia.jquery.js?ver=6.9.0'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/LayerSlider/static/layerslider/js/layerslider.transitions.js?ver=6.9.0'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/revslider/public/assets/js/revolution.tools.min.js?ver=6.0'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/revslider/public/assets/js/rs6.min.js?ver=6.0.9'></script>
<meta name='generator' content='Powered by LayerSlider 6.9.0 - Multi-Purpose, Responsive, Parallax, Mobile-Friendly Slider Plugin for WordPress.' />
<!-- LayerSlider updates and docs at: https://layerslider.kreaturamedia.com -->
<link rel='https://api.w.org/' href='https://www.creativografico.dev/webAOA/index.php/wp-json/' />
<meta name='generator' content='WordPress 5.2.5' />
<link rel='canonical' href='https://www.creativografico.dev/webAOA/index.php/intranet-aoa-colombia/' />
<link rel='shortlink' href='https://www.creativografico.dev/webAOA/?p=79' />
<link rel='alternate' type='application/json+oembed' href='https://www.creativografico.dev/webAOA/index.php/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fwww.creativografico.dev%2FwebAOA%2Findex.php%2Fintranet-aoa-colombia%2F' />
<link rel='alternate' type='text/xml+oembed' href='https://www.creativografico.dev/webAOA/index.php/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fwww.creativografico.dev%2FwebAOA%2Findex.php%2Fintranet-aoa-colombia%2F&#038;format=xml' />

		<script>
		(function(h,o,t,j,a,r){
			h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
			h._hjSettings={hjid:1530846,hjsv:5};
			a=o.getElementsByTagName('head')[0];
			r=o.createElement('script');r.async=1;
			r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
			a.appendChild(r);
		})(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
		</script>
					<script type='text/javascript'>
			var ajaxurl = 'https://www.creativografico.dev/webAOA/wp-admin/admin-ajax.php';
			</script>
		<style type='text/css'>
body, p, #commentform label, .contact-form label {font-size: 14px;line-height: 22px;}h1 {font-size: 24px;line-height: 34px;}h2 {font-size: 20px;line-height: 30px;}h3, .blog-item .quote-excerpt {font-size: 18px;line-height: 24px;}h4, .body-content.quote, #respond-wrap h3, #respond h3 {font-size: 16px;line-height: 20px;}h5 {font-size: 14px;line-height: 18px;}h6 {font-size: 12px;line-height: 16px;}nav .menu li {font-size: 14px;}::selection, ::-moz-selection {background-color: #a8ad00; color: #fff;}.recent-post figure, span.highlighted, span.dropcap4, .loved-item:hover .loved-count, .flickr-widget li, .portfolio-grid li, input[type='submit'], .wpcf7 input.wpcf7-submit[type='submit'], .gform_wrapper input[type='submit'], .mymail-form input[type='submit'], .woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li span.current, figcaption .product-added, .woocommerce .wc-new-badge, .yith-wcwl-wishlistexistsbrowse a, .yith-wcwl-wishlistaddedbrowse a, .woocommerce .widget_layered_nav ul li.chosen > *, .woocommerce .widget_layered_nav_filters ul li a, .sticky-post-icon, .fw-video-close:hover {background-color: #a8ad00!important; color: #ffffff;}a:hover, a:focus, #sidebar a:hover, .pagination-wrap a:hover, .carousel-nav a:hover, .portfolio-pagination div:hover > i, #footer a:hover, #copyright a, .beam-me-up a:hover span, .portfolio-item .portfolio-item-permalink, .read-more-link, .blog-item .read-more, .blog-item-details a:hover, .author-link, #reply-title small a, #respond .form-submit input:hover, span.dropcap2, .spb_divider.go_to_top a, love-it-wrapper:hover .love-it, .love-it-wrapper:hover span.love-count, .love-it-wrapper .loved, .comments-likes .loved span.love-count, .comments-likes a:hover i, .comments-likes .love-it-wrapper:hover a i, .comments-likes a:hover span, .love-it-wrapper:hover a i, .item-link:hover, #header-translation p a, #swift-slider .flex-caption-large h1 a:hover, .wooslider .slide-title a:hover, .caption-details-inner .details span > a, .caption-details-inner .chart span, .caption-details-inner .chart i, #swift-slider .flex-caption-large .chart i, #breadcrumbs a:hover, .ui-widget-content a:hover, .yith-wcwl-add-button a:hover, #product-img-slider li a.zoom:hover, .woocommerce .star-rating span, .article-body-wrap .share-links a:hover, ul.member-contact li a:hover, .price ins, .bag-product a.remove:hover, .bag-product-title a:hover, #back-to-top:hover,  ul.member-contact li a:hover, .fw-video-link-image:hover i, .ajax-search-results .all-results:hover, .search-result h5 a:hover .ui-state-default a:hover {color: #a8ad00;}.carousel-wrap > a:hover, #mobile-menu ul li:hover > a {color: #a8ad00!important;}.comments-likes a:hover span, .comments-likes a:hover i {color: #a8ad00!important;}.read-more i:before, .read-more em:before {color: #a8ad00;}input[type='text']:focus, input[type='email']:focus, input[type='tel']:focus, textarea:focus, .bypostauthor .comment-wrap .comment-avatar,.search-form input:focus, .wpcf7 input:focus, .wpcf7 textarea:focus, .ginput_container input:focus, .ginput_container textarea:focus, .mymail-form input:focus, .mymail-form textarea:focus {border-color: #a8ad00!important;}nav .menu ul li:first-child:after,.navigation a:hover > .nav-text, .returning-customer a:hover {border-bottom-color: #a8ad00;}nav .menu ul ul li:first-child:after {border-right-color: #a8ad00;}.spb_impact_text .spb_call_text {border-left-color: #a8ad00;}.spb_impact_text .spb_button span {color: #fff;}#respond .form-submit input#submit {border-color: #a8ad00;background-color: #FFFFFF;}#respond .form-submit input#submit:hover {border-color: #a8ad00;background-color: #a8ad00;color: #ffffff;}.woocommerce .free-badge, .my-account-login-wrap .login-wrap form.login p.form-row input[type='submit'], .woocommerce .my-account-login-wrap form input[type='submit'] {background-color: #2b2b2b; color: #ffffff;}a[rel='tooltip'], ul.member-contact li a, .blog-item-details a, .post-info a, a.text-link, .tags-wrap .tags a, .logged-in-as a, .comment-meta-actions .edit-link, .comment-meta-actions .comment-reply, .read-more {border-color: #a8ad00;}.super-search-go {border-color: #a8ad00!important;}.super-search-go:hover {background: #a8ad00!important;border-color: #a8ad00!important;}body {color: #003057;}.pagination-wrap a, .search-pagination a {color: #003057;}.layout-boxed #header-search, .layout-boxed #super-search, body > .sf-super-search {background-color: #2b2b2b;}body {background-color: #2b2b2b;}#main-container, .tm-toggle-button-wrap a {background-color: #FFFFFF;}a, .ui-widget-content a {color: #767676;}.pagination-wrap li a:hover, ul.bar-styling li:not(.selected) > a:hover, ul.bar-styling li > .comments-likes:hover, ul.page-numbers li > a:hover, ul.page-numbers li > span.current {color: #ffffff!important;background: #a8ad00;border-color: #a8ad00;}ul.bar-styling li > .comments-likes:hover * {color: #ffffff!important;}.pagination-wrap li a, .pagination-wrap li span, .pagination-wrap li span.expand, ul.bar-styling li > a, ul.bar-styling li > div, ul.page-numbers li > a, ul.page-numbers li > span, .curved-bar-styling, ul.bar-styling li > form input {border-color: #a8ad00;}ul.bar-styling li > a, ul.bar-styling li > span, ul.bar-styling li > div, ul.bar-styling li > form input {background-color: #FFFFFF;}input[type='text'], input[type='password'], input[type='email'], input[type='tel'], textarea, select {border-color: #a8ad00;background: #f7f7f7;}textarea:focus, input:focus {border-color: #999!important;}.modal-header {background: #f7f7f7;}.recent-post .post-details, .team-member .team-member-position, .portfolio-item h5.portfolio-subtitle, .mini-items .blog-item-details, .standard-post-content .blog-item-details, .masonry-items .blog-item .blog-item-details, .jobs > li .job-date, .search-item-content time, .search-item-content span, .blog-item-details a, .portfolio-details-wrap .date,  .portfolio-details-wrap .tags-link-wrap {color: #222222;}ul.bar-styling li.facebook > a:hover {color: #fff!important;background: #3b5998;border-color: #3b5998;}ul.bar-styling li.twitter > a:hover {color: #fff!important;background: #4099FF;border-color: #4099FF;}ul.bar-styling li.google-plus > a:hover {color: #fff!important;background: #d34836;border-color: #d34836;}ul.bar-styling li.pinterest > a:hover {color: #fff!important;background: #cb2027;border-color: #cb2027;}#header-search input, #header-search a, .super-search-close, #header-search i.ss-search {color: #fff;}#header-search a:hover, .super-search-close:hover {color: #a8ad00;}.sf-super-search, .spb_supersearch_widget.asset-bg {background-color: #2b2b2b;}.sf-super-search .search-options .ss-dropdown > span, .sf-super-search .search-options input {color: #a8ad00; border-bottom-color: #a8ad00;}.sf-super-search .search-options .ss-dropdown ul li .fa-check {color: #a8ad00;}.sf-super-search-go:hover, .sf-super-search-close:hover { background-color: #a8ad00; border-color: #a8ad00; color: #ffffff;}#top-bar {background: #a8ad00; color: #ffffff;}#top-bar .tb-welcome {border-color: #f7f7f7;}#top-bar a {color: #ffffff;}#top-bar .menu li {border-left-color: #f7f7f7; border-right-color: #f7f7f7;}#top-bar .menu > li > a, #top-bar .menu > li.parent:after {color: #ffffff;}#top-bar .menu > li > a:hover, #top-bar a:hover {color: #2b2b2b;}#top-bar .show-menu {background-color: #f7f7f7;color: #2b2b2b;}#header-languages .current-language {background: #76881d; color: #a8ad00;}#header-section:before, #header .is-sticky .sticky-header, #header-section .is-sticky #main-nav.sticky-header, #header-section.header-6 .is-sticky #header.sticky-header, .ajax-search-wrap {background-color: #ffffff;background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ffffff), to(#ffffff));background: -webkit-linear-gradient(top, #ffffff, #ffffff);background: -moz-linear-gradient(top, #ffffff, #ffffff);background: -ms-linear-gradient(top, #ffffff, #ffffff);background: -o-linear-gradient(top, #ffffff, #ffffff);}#logo img {padding-top: 10px;padding-bottom: 0px;}#logo img, #logo img.retina {width: 250px;}#logo {max-height: 42px;}#header-section .header-menu .menu li, #mini-header .header-right nav .menu li {border-left-color: #a8ad00;}#header-section #main-nav {border-top-color: #a8ad00;}#top-header {border-bottom-color: #e4e4e4;}#top-header {border-bottom-color: #e4e4e4;}#top-header .th-right > nav .menu li, .ajax-search-wrap:after {border-bottom-color: #e4e4e4;}.ajax-search-wrap, .ajax-search-results, .search-result-pt .search-result {border-color: #a8ad00;}.page-content {border-bottom-color: #a8ad00;}.ajax-search-wrap input[type='text'], .search-result-pt h6, .no-search-results h6, .search-result h5 a {color: #8c8c8c;}@media only screen and (max-width: 991px) {
			.naked-header #header-section, .naked-header #header-section:before, .naked-header #header .is-sticky .sticky-header, .naked-header .is-sticky #header.sticky-header {background-color: #ffffff;background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ffffff), to(#ffffff));background: -webkit-linear-gradient(top, #ffffff, #ffffff);background: -moz-linear-gradient(top, #ffffff, #ffffff);background: -ms-linear-gradient(top, #ffffff, #ffffff);background: -o-linear-gradient(top, #ffffff, #ffffff);}
			}nav#main-navigation .menu > li > a span.nav-line {background-color: #76881d;}.show-menu {background-color: #2b2b2b;color: #ffffff;}nav .menu > li:before {background: #76881d;}nav .menu .sub-menu .parent > a:after {border-left-color: #76881d;}nav .menu ul.sub-menu {background-color: #a8ad00;}nav .menu ul.sub-menu li {border-bottom-color: #a8ad00;border-bottom-style: solid;}nav.mega-menu li .mega .sub .sub-menu, nav.mega-menu li .mega .sub .sub-menu li, nav.mega-menu li .sub-container.non-mega li, nav.mega-menu li .sub li.mega-hdr {border-top-color: #a8ad00;border-top-style: solid;}nav.mega-menu li .sub li.mega-hdr {border-right-color: #a8ad00;border-right-style: solid;}nav .menu > li.menu-item > a, nav .menu > li.menu-item.indicator-disabled > a, #menubar-controls a, nav.search-nav .menu>li>a, .naked-header .is-sticky nav .menu > li a {color: #8c8c8c;}nav .menu > li.menu-item:hover > a {color: #76881d;}nav .menu ul.sub-menu li.menu-item > a, nav .menu ul.sub-menu li > span, #top-bar nav .menu ul li > a {color: #ffffff;}nav .menu ul.sub-menu li.menu-item:hover > a {color: #ffffff!important; background: #76881d;}nav .menu li.parent > a:after, nav .menu li.parent > a:after:hover {color: #aaa;}nav .menu li.current-menu-ancestor > a, nav .menu li.current-menu-item > a, #mobile-menu .menu ul li.current-menu-item > a, nav .menu li.current-scroll-item > a {color: #76881d;}nav .menu ul li.current-menu-ancestor > a, nav .menu ul li.current-menu-item > a {color: #a8ad00; background: #76881d;}#main-nav .header-right ul.menu > li, .wishlist-item {border-left-color: #a8ad00;}#nav-search, #mini-search {background: #a8ad00;}#nav-search a, #mini-search a {color: #ffffff;}.bag-header, .bag-product, .bag-empty, .wishlist-empty {border-color: #a8ad00;}.bag-buttons a.sf-button.bag-button, .bag-buttons a.sf-button.wishlist-button, .bag-buttons a.sf-button.guest-button {background-color: #a8ad00; color: #003057!important;}.bag-buttons a.checkout-button, .bag-buttons a.create-account-button, .woocommerce input.button.alt, .woocommerce .alt-button, .woocommerce button.button.alt, .woocommerce #account_details .login form p.form-row input[type='submit'], #login-form .modal-body form.login p.form-row input[type='submit'] {background: #2b2b2b; color: #ffffff;}.woocommerce .button.update-cart-button:hover, .woocommerce #account_details .login form p.form-row input[type='submit']:hover, #login-form .modal-body form.login p.form-row input[type='submit']:hover {background: #a8ad00; color: #ffffff;}.woocommerce input.button.alt:hover, .woocommerce .alt-button:hover, .woocommerce button.button.alt:hover {background: #a8ad00; color: #ffffff;}.shopping-bag:before, nav .menu ul.sub-menu li:first-child:before {border-bottom-color: #76881d;}nav ul.menu > li.menu-item.sf-menu-item-btn > a {background-color: #76881d;color: #8c8c8c;}nav ul.menu > li.menu-item.sf-menu-item-btn:hover > a {color: #76881d;background-color: #8c8c8c;}#base-promo {background-color: #e4e4e4;}#base-promo > p, #base-promo.footer-promo-text > a, #base-promo.footer-promo-arrow > a {color: #2b2b2b;}#base-promo.footer-promo-arrow:hover, #base-promo.footer-promo-text:hover {background-color: #a8ad00;color: #ffffff;}#base-promo.footer-promo-arrow:hover > *, #base-promo.footer-promo-text:hover > * {color: #ffffff;}.page-heading {background-color: #f7f7f7;border-bottom-color: #a8ad00;}.page-heading h1, .page-heading h3 {color: #003057;}#breadcrumbs {color: #2b2b2b;}#breadcrumbs a, #breadcrumb i {color: #767676;}body, input[type='text'], input[type='password'], input[type='email'], textarea, select, .ui-state-default a {color: #003057;}h1, h1 a {color: #003057;}h2, h2 a {color: #003057;}h3, h3 a {color: #003057;}h4, h4 a, .carousel-wrap > a {color: #003057;}h5, h5 a {color: #003057;}h6, h6 a {color: #003057;}.spb_impact_text .spb_call_text, .impact-text, .impact-text-large {color: #2b2b2b;}.read-more i, .read-more em {color: transparent;}.pb-border-bottom, .pb-border-top, .read-more-button {border-color: #a8ad00;}#swift-slider ul.slides {background: #2b2b2b;}#swift-slider .flex-caption .flex-caption-headline {background: #FFFFFF;}#swift-slider .flex-caption .flex-caption-details .caption-details-inner {background: #FFFFFF; border-bottom: #a8ad00}#swift-slider .flex-caption-large, #swift-slider .flex-caption-large h1 a {color: #ffffff;}#swift-slider .flex-caption h4 i {line-height: 20px;}#swift-slider .flex-caption-large .comment-chart i {color: #ffffff;}#swift-slider .flex-caption-large .loveit-chart span {color: #a8ad00;}#swift-slider .flex-caption-large a {color: #a8ad00;}#swift-slider .flex-caption .comment-chart i, #swift-slider .flex-caption .comment-chart span {color: #2b2b2b;}figure.animated-overlay figcaption {background-color: #a8ad00;}
figure.animated-overlay figcaption .thumb-info h4, figure.animated-overlay figcaption .thumb-info h5, figcaption .thumb-info-excerpt p {color: #ffffff;}figure.animated-overlay figcaption .thumb-info i {background: #2b2b2b; color: #ffffff;}figure:hover .overlay {box-shadow: inset 0 0 0 500px #a8ad00;}h4.spb-heading span:before, h4.spb-heading span:after, h3.spb-heading span:before, h3.spb-heading span:after, h4.lined-heading span:before, h4.lined-heading span:after {border-color: #a8ad00}h4.spb-heading:before, h3.spb-heading:before, h4.lined-heading:before {border-top-color: #a8ad00}.spb_parallax_asset h4.spb-heading {border-bottom-color: #003057}.testimonials.carousel-items li .testimonial-text {background-color: #f7f7f7;}.sidebar .widget-heading h4 {color: #003057;}.widget ul li, .widget.widget_lip_most_loved_widget li {border-color: #a8ad00;}.widget.widget_lip_most_loved_widget li {background: #FFFFFF; border-color: #a8ad00;}.widget_lip_most_loved_widget .loved-item > span {color: #222222;}.widget_search form input {background: #FFFFFF;}.widget .wp-tag-cloud li a {background: #f7f7f7; border-color: #a8ad00;}.widget .tagcloud a:hover, .widget ul.wp-tag-cloud li:hover > a {background-color: #a8ad00; color: #ffffff;}.loved-item .loved-count > i {color: #003057;background: #a8ad00;}.subscribers-list li > a.social-circle {color: #ffffff;background: #2b2b2b;}.subscribers-list li:hover > a.social-circle {color: #fbfbfb;background: #a8ad00;}.sidebar .widget_categories ul > li a, .sidebar .widget_archive ul > li a, .sidebar .widget_nav_menu ul > li a, .sidebar .widget_meta ul > li a, .sidebar .widget_recent_entries ul > li, .widget_product_categories ul > li a, .widget_layered_nav ul > li a {color: #767676;}.sidebar .widget_categories ul > li a:hover, .sidebar .widget_archive ul > li a:hover, .sidebar .widget_nav_menu ul > li a:hover, .widget_nav_menu ul > li.current-menu-item a, .sidebar .widget_meta ul > li a:hover, .sidebar .widget_recent_entries ul > li a:hover, .widget_product_categories ul > li a:hover, .widget_layered_nav ul > li a:hover {color: #a8ad00;}#calendar_wrap caption {border-bottom-color: #2b2b2b;}.sidebar .widget_calendar tbody tr > td a {color: #ffffff;background-color: #2b2b2b;}.sidebar .widget_calendar tbody tr > td a:hover {background-color: #a8ad00;}.sidebar .widget_calendar tfoot a {color: #2b2b2b;}.sidebar .widget_calendar tfoot a:hover {color: #a8ad00;}.widget_calendar #calendar_wrap, .widget_calendar th, .widget_calendar tbody tr > td, .widget_calendar tbody tr > td.pad {border-color: #a8ad00;}.widget_sf_infocus_widget .infocus-item h5 a {color: #2b2b2b;}.widget_sf_infocus_widget .infocus-item h5 a:hover {color: #a8ad00;}.sidebar .widget hr {border-color: #a8ad00;}.widget ul.flickr_images li a:after, .portfolio-grid li a:after {color: #ffffff;}.slideout-filter .select:after {background: #FFFFFF;}.slideout-filter ul li a {color: #ffffff;}.slideout-filter ul li a:hover {color: #a8ad00;}.slideout-filter ul li.selected a {color: #ffffff;background: #a8ad00;}ul.portfolio-filter-tabs li.selected a {background: #f7f7f7;}.spb_blog_widget .filter-wrap {background-color: #222;}.portfolio-item {border-bottom-color: #a8ad00;}.masonry-items .portfolio-item-details {background: #f7f7f7;}.spb_portfolio_carousel_widget .portfolio-item {background: #FFFFFF;}.spb_portfolio_carousel_widget .portfolio-item h4.portfolio-item-title a > i {line-height: 20px;}.masonry-items .blog-item .blog-details-wrap:before {background-color: #f7f7f7;}.masonry-items .portfolio-item figure {border-color: #a8ad00;}.portfolio-details-wrap span span {color: #666;}.share-links > a:hover {color: #a8ad00;}.blog-aux-options li.selected a {background: #a8ad00;border-color: #a8ad00;color: #ffffff;}.blog-filter-wrap .aux-list li:hover {border-bottom-color: transparent;}.blog-filter-wrap .aux-list li:hover a {color: #ffffff;background: #a8ad00;}.mini-blog-item-wrap, .mini-items .mini-alt-wrap, .mini-items .mini-alt-wrap .quote-excerpt, .mini-items .mini-alt-wrap .link-excerpt, .masonry-items .blog-item .quote-excerpt, .masonry-items .blog-item .link-excerpt, .standard-post-content .quote-excerpt, .standard-post-content .link-excerpt, .timeline, .post-info, .body-text .link-pages, .page-content .link-pages {border-color: #a8ad00;}.post-info, .article-body-wrap .share-links .share-text, .article-body-wrap .share-links a {color: #222222;}.standard-post-date {background: #a8ad00;}.standard-post-content {background: #f7f7f7;}.format-quote .standard-post-content:before, .standard-post-content.no-thumb:before {border-left-color: #f7f7f7;}.search-item-img .img-holder {background: #f7f7f7;border-color:#a8ad00;}.masonry-items .blog-item .masonry-item-wrap {background: #f7f7f7;}.mini-items .blog-item-details, .share-links, .single-portfolio .share-links, .single .pagination-wrap, ul.portfolio-filter-tabs li a {border-color: #a8ad00;}.related-item figure {background-color: #2b2b2b; color: #ffffff}.required {color: #ee3c59;}.comments-likes a i, .comments-likes a span, .comments-likes .love-it-wrapper a i, .comments-likes span.love-count, .share-links ul.bar-styling > li > a {color: #222222;}#respond .form-submit input:hover {color: #fff!important;}.recent-post {background: #FFFFFF;}.recent-post .post-item-details {border-top-color: #a8ad00;color: #a8ad00;}.post-item-details span, .post-item-details a, .post-item-details .comments-likes a i, .post-item-details .comments-likes a span {color: #222222;}.sf-button.accent {color: #ffffff; background-color: #a8ad00;}.sf-button.sf-icon-reveal.accent {color: #ffffff!important; background-color: #a8ad00!important;}.sf-button.accent:hover {background-color: #2b2b2b;color: #ffffff;}a.sf-button, a.sf-button:hover, #footer a.sf-button:hover {background-image: none;color: #fff!important;}a.sf-button.gold, a.sf-button.gold:hover, a.sf-button.lightgrey, a.sf-button.lightgrey:hover, a.sf-button.white, a.sf-button.white:hover {color: #222!important;}a.sf-button.transparent-dark {color: #003057!important;}a.sf-button.transparent-light:hover, a.sf-button.transparent-dark:hover {color: #a8ad00!important;} input[type='submit'], .wpcf7 input.wpcf7-submit[type='submit'], .gform_wrapper input[type='submit'], .mymail-form input[type='submit'] {color: #fff;}input[type='submit']:hover, .wpcf7 input.wpcf7-submit[type='submit']:hover, .gform_wrapper input[type='submit']:hover, .mymail-form input[type='submit']:hover {background-color: #2b2b2b!important;color: #ffffff;}input[type='text'], input[type='email'], input[type='password'], textarea, select, .wpcf7 input[type='text'], .wpcf7 input[type='email'], .wpcf7 textarea, .wpcf7 select, .ginput_container input[type='text'], .ginput_container input[type='email'], .ginput_container textarea, .ginput_container select, .mymail-form input[type='text'], .mymail-form input[type='email'], .mymail-form textarea, .mymail-form select {background: #f7f7f7; border-color: #a8ad00;}.sf-icon {color: #a8ad00;}.sf-icon-cont {border-color: rgba(118,136,29,0.5);}.sf-icon-cont:hover, .sf-hover .sf-icon-cont, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont, .sf-hover .sf-icon-box-hr {background-color: #76881d;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont:after {border-top-color: #76881d;border-left-color: #76881d;}.sf-icon-cont:hover .sf-icon, .sf-hover .sf-icon-cont .sf-icon, .sf-icon-box.sf-icon-box-boxed-one .sf-icon, .sf-icon-box.sf-icon-box-boxed-three .sf-icon {color: #ffffff;}.sf-icon-box-animated .front {background: #f7f7f7; border-color: #a8ad00;}.sf-icon-box-animated .front h3 {color: #003057!important;}.sf-icon-box-animated .back {background: #a8ad00; border-color: #a8ad00;}.sf-icon-box-animated .back, .sf-icon-box-animated .back h3 {color: #ffffff!important;}.sf-icon-accent.sf-icon-cont, .sf-icon-accent > i {color: #a8ad00;}.sf-icon-cont.sf-icon-accent {border-color: #a8ad00;}.sf-icon-cont.sf-icon-accent:hover, .sf-hover .sf-icon-cont.sf-icon-accent, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-accent, .sf-hover .sf-icon-box-hr.sf-icon-accent {background-color: #a8ad00;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-accent:after {border-top-color: #a8ad00;border-left-color: #a8ad00;}.sf-icon-cont.sf-icon-accent:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-accent .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-accent .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-accent .sf-icon {color: #ffffff;}.sf-icon-secondary-accent.sf-icon-cont, .sf-icon-secondary-accent > i {color: #2b2b2b;}.sf-icon-cont.sf-icon-secondary-accent {border-color: #2b2b2b;}.sf-icon-cont.sf-icon-secondary-accent:hover, .sf-hover .sf-icon-cont.sf-icon-secondary-accent, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-secondary-accent, .sf-hover .sf-icon-box-hr.sf-icon-secondary-accent {background-color: #2b2b2b;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-secondary-accent:after {border-top-color: #2b2b2b;border-left-color: #2b2b2b;}.sf-icon-cont.sf-icon-secondary-accent:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-secondary-accent .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-secondary-accent .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-secondary-accent .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-secondary-accent {background: #2b2b2b; border-color: #2b2b2b;}.sf-icon-box-animated .back.sf-icon-secondary-accent, .sf-icon-box-animated .back.sf-icon-secondary-accent h3 {color: #ffffff!important;}.sf-icon-icon-one.sf-icon-cont, .sf-icon-icon-one > i, i.sf-icon-icon-one {color: #76881d;}.sf-icon-cont.sf-icon-icon-one {border-color: #76881d;}.sf-icon-cont.sf-icon-icon-one:hover, .sf-hover .sf-icon-cont.sf-icon-icon-one, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-one, .sf-hover .sf-icon-box-hr.sf-icon-icon-one {background-color: #76881d;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-one:after {border-top-color: #76881d;border-left-color: #76881d;}.sf-icon-cont.sf-icon-icon-one:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-icon-one .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-icon-one .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-icon-one .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-icon-one {background: #76881d; border-color: #76881d;}.sf-icon-box-animated .back.sf-icon-icon-one, .sf-icon-box-animated .back.sf-icon-icon-one h3 {color: #ffffff!important;}.sf-icon-icon-two.sf-icon-cont, .sf-icon-icon-two > i, i.sf-icon-icon-two {color: #76881d;}.sf-icon-cont.sf-icon-icon-two {border-color: #76881d;}.sf-icon-cont.sf-icon-icon-two:hover, .sf-hover .sf-icon-cont.sf-icon-icon-two, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-two, .sf-hover .sf-icon-box-hr.sf-icon-icon-two {background-color: #76881d;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-two:after {border-top-color: #76881d;border-left-color: #76881d;}.sf-icon-cont.sf-icon-icon-two:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-icon-two .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-icon-two .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-icon-two .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-icon-two {background: #76881d; border-color: #76881d;}.sf-icon-box-animated .back.sf-icon-icon-two, .sf-icon-box-animated .back.sf-icon-icon-two h3 {color: #ffffff!important;}.sf-icon-icon-three.sf-icon-cont, .sf-icon-icon-three > i, i.sf-icon-icon-three {color: #003057;}.sf-icon-cont.sf-icon-icon-three {border-color: #003057;}.sf-icon-cont.sf-icon-icon-three:hover, .sf-hover .sf-icon-cont.sf-icon-icon-three, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-three, .sf-hover .sf-icon-box-hr.sf-icon-icon-three {background-color: #003057;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-three:after {border-top-color: #003057;border-left-color: #003057;}.sf-icon-cont.sf-icon-icon-three:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-icon-three .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-icon-three .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-icon-three .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-icon-three {background: #003057; border-color: #003057;}.sf-icon-box-animated .back.sf-icon-icon-three, .sf-icon-box-animated .back.sf-icon-icon-three h3 {color: #ffffff!important;}.sf-icon-icon-four.sf-icon-cont, .sf-icon-icon-four > i, i.sf-icon-icon-four {color: #2b2b2b;}.sf-icon-cont.sf-icon-icon-four {border-color: #2b2b2b;}.sf-icon-cont.sf-icon-icon-four:hover, .sf-hover .sf-icon-cont.sf-icon-icon-four, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-four, .sf-hover .sf-icon-box-hr.sf-icon-icon-four {background-color: #2b2b2b;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-four:after {border-top-color: #2b2b2b;border-left-color: #2b2b2b;}.sf-icon-cont.sf-icon-icon-four:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-icon-four .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-icon-four .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-icon-four .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-icon-four {background: #2b2b2b; border-color: #2b2b2b;}.sf-icon-box-animated .back.sf-icon-icon-four, .sf-icon-box-animated .back.sf-icon-icon-four h3 {color: #ffffff!important;}span.dropcap3 {background: #000;color: #fff;}span.dropcap4 {color: #fff;}.spb_divider, .spb_divider.go_to_top_icon1, .spb_divider.go_to_top_icon2, .testimonials > li, .jobs > li, .spb_impact_text, .tm-toggle-button-wrap, .tm-toggle-button-wrap a, .portfolio-details-wrap, .spb_divider.go_to_top a, .impact-text-wrap, .widget_search form input, .asset-bg.spb_divider {border-color: #a8ad00;}.spb_divider.go_to_top_icon1 a, .spb_divider.go_to_top_icon2 a {background: #FFFFFF;}.spb_tabs .ui-tabs .ui-tabs-panel, .spb_content_element .ui-tabs .ui-tabs-nav, .ui-tabs .ui-tabs-nav li {border-color: #a8ad00;}.spb_tabs .ui-tabs .ui-tabs-panel, .ui-tabs .ui-tabs-nav li.ui-tabs-active a {background: #FFFFFF!important;}.spb_tabs .nav-tabs li a, .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus, .spb_accordion .spb_accordion_section, .spb_tour .nav-tabs li a {border-color: #a8ad00;}.spb_tabs .nav-tabs li.active a, .spb_tour .nav-tabs li.active a, .spb_accordion .spb_accordion_section > h3.ui-state-active a {background-color: #f7f7f7;}.spb_tour .ui-tabs .ui-tabs-nav li a {border-color: #a8ad00;}.spb_tour.span3 .ui-tabs .ui-tabs-nav li {border-color: #a8ad00!important;}.toggle-wrap .spb_toggle, .spb_toggle_content {border-color: #a8ad00;}.toggle-wrap .spb_toggle:hover {color: #a8ad00;}.ui-accordion h3.ui-accordion-header .ui-icon {color: #003057;}.ui-accordion h3.ui-accordion-header.ui-state-active:hover a, .ui-accordion h3.ui-accordion-header:hover .ui-icon {color: #a8ad00;}blockquote.pullquote {border-color: #a8ad00;}.borderframe img {border-color: #eeeeee;}.labelled-pricing-table .column-highlight {background-color: #fff;}.labelled-pricing-table .pricing-table-label-row, .labelled-pricing-table .pricing-table-row {background: #f7f7f7;}.labelled-pricing-table .alt-row {background: #fff;}.labelled-pricing-table .pricing-table-price {background: #e4e4e4;}.labelled-pricing-table .pricing-table-package {background: #f7f7f7;}.labelled-pricing-table .lpt-button-wrap {background: #e4e4e4;}.labelled-pricing-table .lpt-button-wrap a.accent {background: #222!important;}.labelled-pricing-table .column-highlight .lpt-button-wrap {background: transparent!important;}.labelled-pricing-table .column-highlight .lpt-button-wrap a.accent {background: #a8ad00!important;}.column-highlight .pricing-table-price {color: #fff;background: #76881d;border-bottom-color: #76881d;}.column-highlight .pricing-table-package {background: #a8ad00;}.column-highlight .pricing-table-details {background: #d3d680;}.spb_box_text.coloured .box-content-wrap {background: #003057;color: #fff;}.spb_box_text.whitestroke .box-content-wrap {background-color: #fff;border-color: #a8ad00;}.client-item figure {border-color: #a8ad00;}.client-item figure:hover {border-color: #333;}ul.member-contact li a:hover {color: #333;}.testimonials.carousel-items li .testimonial-text {border-color: #a8ad00;}.testimonials.carousel-items li .testimonial-text:after {border-left-color: #a8ad00;border-top-color: #a8ad00;}.team-member figure figcaption {background: #f7f7f7;}.horizontal-break {background-color: #a8ad00;}.progress .bar {background-color: #a8ad00;}.progress.standard .bar {background: #a8ad00;}.progress-bar-wrap .progress-value {color: #a8ad00;}.asset-bg-detail {background:#FFFFFF;border-color:#a8ad00;}#footer {background: #003057;}#footer, #footer p {color: #ffffff;}#footer h6 {color: #ffffff;}#footer a {color: #ffffff;}#footer .widget ul li, #footer .widget_categories ul, #footer .widget_archive ul, #footer .widget_nav_menu ul, #footer .widget_recent_comments ul, #footer .widget_meta ul, #footer .widget_recent_entries ul, #footer .widget_product_categories ul {border-color: #ffffff;}#copyright {background-color: #003057;border-top-color: #ffffff;}#copyright p {color: #979797;}#copyright a {color: #ffffff;}#copyright a:hover {color: #a8ad00;}#copyright nav .menu li {border-left-color: #ffffff;}#footer .widget_calendar #calendar_wrap, #footer .widget_calendar th, #footer .widget_calendar tbody tr > td, #footer .widget_calendar tbody tr > td.pad {border-color: #ffffff;}.widget input[type='email'] {background: #f7f7f7; color: #999}#footer .widget hr {border-color: #ffffff;}.woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span, .modal-body .comment-form-rating, .woocommerce form .form-row input.input-text, ul.checkout-process, #billing .proceed, ul.my-account-nav > li, .woocommerce #payment, .woocommerce-checkout p.thank-you, .woocommerce .order_details, .woocommerce-page .order_details, .woocommerce ul.products li.product figure figcaption .yith-wcwl-add-to-wishlist, #product-accordion .panel, .review-order-wrap { border-color: #a8ad00 ;}nav.woocommerce-pagination ul li span.current, nav.woocommerce-pagination ul li a:hover {background:#a8ad00!important;border-color:#a8ad00;color: #ffffff!important;}.woocommerce-account p.myaccount_address, .woocommerce-account .page-content h2, p.no-items, #order_review table.shop_table, #payment_heading, .returning-customer a {border-bottom-color: #a8ad00;}.woocommerce .products ul, .woocommerce ul.products, .woocommerce-page .products ul, .woocommerce-page ul.products, p.no-items {border-top-color: #a8ad00;}.woocommerce-ordering .woo-select, .variations_form .woo-select, .add_review a, .woocommerce .quantity, .woocommerce-page .quantity, .woocommerce .coupon input.apply-coupon, .woocommerce table.shop_table tr td.product-remove .remove, .woocommerce .button.update-cart-button, .shipping-calculator-form .woo-select, .woocommerce .shipping-calculator-form .update-totals-button button, .woocommerce #billing_country_field .woo-select, .woocommerce #shipping_country_field .woo-select, .woocommerce #review_form #respond .form-submit input, .woocommerce form .form-row input.input-text, .woocommerce table.my_account_orders .order-actions .button, .woocommerce #payment div.payment_box, .woocommerce .widget_price_filter .price_slider_amount .button, .woocommerce.widget .buttons a, .load-more-btn {background: #f7f7f7; color: #2b2b2b}.woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li span.current { color: #ffffff;}li.product figcaption a.product-added {color: #ffffff;}.woocommerce ul.products li.product figure figcaption, .yith-wcwl-add-button a, ul.products li.product a.quick-view-button, .yith-wcwl-add-to-wishlist, .woocommerce form.cart button.single_add_to_cart_button, .woocommerce p.cart a.single_add_to_cart_button, .lost_reset_password p.form-row input[type='submit'], .track_order p.form-row input[type='submit'], .change_password_form p input[type='submit'], .woocommerce form.register input[type='submit'], .woocommerce .wishlist_table tr td.product-add-to-cart a, .woocommerce input.button[name='save_address'], .woocommerce .woocommerce-message a.button {background: #f7f7f7;}.woocommerce ul.products li.product figure figcaption .shop-actions > a, .woocommerce .wishlist_table tr td.product-add-to-cart a {color: #003057;}.woocommerce ul.products li.product figure figcaption .shop-actions > a.product-added, .woocommerce ul.products li.product figure figcaption .shop-actions > a.product-added:hover {color: #ffffff;}ul.products li.product .product-details .posted_in a {color: #222222;}.woocommerce ul.products li.product figure figcaption .shop-actions > a:hover, ul.products li.product .product-details .posted_in a:hover {color: #a8ad00;}.woocommerce form.cart button.single_add_to_cart_button, .woocommerce p.cart a.single_add_to_cart_button, .woocommerce input[name='save_account_details'] { background: #f7f7f7!important; color: #003057 ;}
.woocommerce form.cart button.single_add_to_cart_button:disabled, .woocommerce form.cart button.single_add_to_cart_button:disabled[disabled] { background: #f7f7f7!important; color: #003057 ;}
.woocommerce form.cart button.single_add_to_cart_button:hover, .woocommerce .button.checkout-button, .woocommerce .wc-proceed-to-checkout > a.checkout-button { background: #a8ad00!important; color: #ffffff ;}
.woocommerce p.cart a.single_add_to_cart_button:hover, .woocommerce .button.checkout-button:hover, .woocommerce .wc-proceed-to-checkout > a.checkout-button:hover {background: #2b2b2b!important; color: #a8ad00!important;}.woocommerce table.shop_table tr td.product-remove .remove:hover, .woocommerce .coupon input.apply-coupon:hover, .woocommerce .shipping-calculator-form .update-totals-button button:hover, .woocommerce .quantity .plus:hover, .woocommerce .quantity .minus:hover, .add_review a:hover, .woocommerce #review_form #respond .form-submit input:hover, .lost_reset_password p.form-row input[type='submit']:hover, .track_order p.form-row input[type='submit']:hover, .change_password_form p input[type='submit']:hover, .woocommerce table.my_account_orders .order-actions .button:hover, .woocommerce .widget_price_filter .price_slider_amount .button:hover, .woocommerce.widget .buttons a:hover, .woocommerce .wishlist_table tr td.product-add-to-cart a:hover, .woocommerce input.button[name='save_address']:hover, .woocommerce input[name='apply_coupon']:hover, .woocommerce button[name='apply_coupon']:hover, .woocommerce .cart input[name='update_cart']:hover, .woocommerce form.register input[type='submit']:hover, .woocommerce form.cart button.single_add_to_cart_button:hover, .woocommerce form.cart .yith-wcwl-add-to-wishlist a:hover, .load-more-btn:hover, .woocommerce-account input[name='change_password']:hover {background: #a8ad00; color: #ffffff;}.woocommerce-MyAccount-navigation li {border-color: #a8ad00;}.woocommerce-MyAccount-navigation li.is-active a, .woocommerce-MyAccount-navigation li a:hover {color: #003057;}.woocommerce #account_details .login, .woocommerce #account_details .login h4.lined-heading span, .my-account-login-wrap .login-wrap, .my-account-login-wrap .login-wrap h4.lined-heading span, .woocommerce div.product form.cart table div.quantity {background: #f7f7f7;}.woocommerce .help-bar ul li a:hover, .woocommerce .continue-shopping:hover, .woocommerce .address .edit-address:hover, .my_account_orders td.order-number a:hover, .product_meta a.inline:hover { border-bottom-color: #a8ad00;}.woocommerce .order-info, .woocommerce .order-info mark {background: #a8ad00; color: #ffffff;}.woocommerce #payment div.payment_box:after {border-bottom-color: #f7f7f7;}.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content {background: #a8ad00;}.woocommerce .widget_price_filter .ui-slider-horizontal .ui-slider-range {background: #f7f7f7;}.yith-wcwl-wishlistexistsbrowse a:hover, .yith-wcwl-wishlistaddedbrowse a:hover {color: #ffffff;}.woocommerce ul.products li.product .price, .woocommerce div.product p.price {color: #003057;}.woocommerce ul.products li.product-category .product-cat-info {background: #a8ad00;}.woocommerce ul.products li.product-category .product-cat-info:before {border-bottom-color:#a8ad00;}.woocommerce ul.products li.product-category a:hover .product-cat-info {background: #a8ad00; color: #ffffff;}.woocommerce ul.products li.product-category a:hover .product-cat-info h3 {color: #ffffff!important;}.woocommerce ul.products li.product-category a:hover .product-cat-info:before {border-bottom-color:#a8ad00;}.woocommerce input[name='apply_coupon'], .woocommerce button[name='apply_coupon'], .woocommerce .cart input[name='update_cart'], .woocommerce .shipping-calc-wrap button[name='calc_shipping'], .woocommerce-account input[name='change_password'] {background: #f7f7f7!important; color: #2b2b2b!important}.woocommerce input[name='apply_coupon']:hover, .woocommerce button[name='apply_coupon']:hover, .woocommerce .cart input[name='update_cart']:hover, .woocommerce .shipping-calc-wrap button[name='calc_shipping']:hover, .woocommerce-account input[name='change_password']:hover, .woocommerce input[name='save_account_details']:hover {background: #a8ad00!important; color: #ffffff!important;}#buddypress .activity-meta a, #buddypress .acomment-options a, #buddypress #member-group-links li a {border-color: #a8ad00;}#buddypress .activity-meta a:hover, #buddypress .acomment-options a:hover, #buddypress #member-group-links li a:hover {border-color: #a8ad00;}#buddypress .activity-header a, #buddypress .activity-read-more a {border-color: #a8ad00;}#buddypress #members-list .item-meta .activity, #buddypress .activity-header p {color: #222222;}#buddypress .pagination-links span, #buddypress .load-more.loading a {background-color: #a8ad00;color: #ffffff;border-color: #a8ad00;}span.bbp-admin-links a, li.bbp-forum-info .bbp-forum-content {color: #222222;}span.bbp-admin-links a:hover {color: #a8ad00;}.bbp-topic-action #favorite-toggle a, .bbp-topic-action #subscription-toggle a, .bbp-single-topic-meta a, .bbp-topic-tags a, #bbpress-forums li.bbp-body ul.forum, #bbpress-forums li.bbp-body ul.topic, #bbpress-forums li.bbp-header, #bbpress-forums li.bbp-footer, #bbp-user-navigation ul li a, .bbp-pagination-links a, #bbp-your-profile fieldset input, #bbp-your-profile fieldset textarea, #bbp-your-profile, #bbp-your-profile fieldset {border-color: #a8ad00;}.bbp-topic-action #favorite-toggle a:hover, .bbp-topic-action #subscription-toggle a:hover, .bbp-single-topic-meta a:hover, .bbp-topic-tags a:hover, #bbp-user-navigation ul li a:hover, .bbp-pagination-links a:hover {border-color: #a8ad00;}#bbp-user-navigation ul li.current a, .bbp-pagination-links span.current {border-color: #a8ad00;background: #a8ad00; color: #ffffff;}#bbpress-forums fieldset.bbp-form button[type='submit'], #bbp_user_edit_submit {background: #f7f7f7; color: #2b2b2b}#bbpress-forums fieldset.bbp-form button[type='submit']:hover, #bbp_user_edit_submit:hover {background: #a8ad00; color: #ffffff;}.asset-bg {border-color: #a8ad00;}.asset-bg.alt-one {background-color: #FFFFFF;}.asset-bg.alt-one, .asset-bg .alt-one, .asset-bg.alt-one h1, .asset-bg.alt-one h2, .asset-bg.alt-one h3, .asset-bg.alt-one h3, .asset-bg.alt-one h4, .asset-bg.alt-one h5, .asset-bg.alt-one h6, .alt-one .carousel-wrap > a {color: #222222;}.asset-bg.alt-one h4.spb-center-heading span:before, .asset-bg.alt-one h4.spb-center-heading span:after {border-color: #222222;}.alt-one .full-width-text:after {border-top-color:#FFFFFF;}.alt-one h4.spb-text-heading, .alt-one h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-two {background-color: #FFFFFF;}.asset-bg.alt-two, .asset-bg .alt-two, .asset-bg.alt-two h1, .asset-bg.alt-two h2, .asset-bg.alt-two h3, .asset-bg.alt-two h3, .asset-bg.alt-two h4, .asset-bg.alt-two h5, .asset-bg.alt-two h6, .alt-two .carousel-wrap > a {color: #222222;}.asset-bg.alt-two h4.spb-center-heading span:before, .asset-bg.alt-two h4.spb-center-heading span:after {border-color: #222222;}.alt-two .full-width-text:after {border-top-color:#FFFFFF;}.alt-two h4.spb-text-heading, .alt-two h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-three {background-color: #FFFFFF;}.asset-bg.alt-three, .asset-bg .alt-three, .asset-bg.alt-three h1, .asset-bg.alt-three h2, .asset-bg.alt-three h3, .asset-bg.alt-three h3, .asset-bg.alt-three h4, .asset-bg.alt-three h5, .asset-bg.alt-three h6, .alt-three .carousel-wrap > a {color: #222222;}.asset-bg.alt-three h4.spb-center-heading span:before, .asset-bg.alt-three h4.spb-center-heading span:after {border-color: #222222;}.alt-three .full-width-text:after {border-top-color:#FFFFFF;}.alt-three h4.spb-text-heading, .alt-three h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-four {background-color: #FFFFFF;}.asset-bg.alt-four, .asset-bg .alt-four, .asset-bg.alt-four h1, .asset-bg.alt-four h2, .asset-bg.alt-four h3, .asset-bg.alt-four h3, .asset-bg.alt-four h4, .asset-bg.alt-four h5, .asset-bg.alt-four h6, .alt-four .carousel-wrap > a {color: #222222;}.asset-bg.alt-four h4.spb-center-heading span:before, .asset-bg.alt-four h4.spb-center-heading span:after {border-color: #222222;}.alt-four .full-width-text:after {border-top-color:#FFFFFF;}.alt-four h4.spb-text-heading, .alt-four h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-five {background-color: #FFFFFF;}.asset-bg.alt-five, .asset-bg .alt-five, .asset-bg.alt-five h1, .asset-bg.alt-five h2, .asset-bg.alt-five h3, .asset-bg.alt-five h3, .asset-bg.alt-five h4, .asset-bg.alt-five h5, .asset-bg.alt-five h6, .alt-five .carousel-wrap > a {color: #222222;}.asset-bg.alt-five h4.spb-center-heading span:before, .asset-bg.alt-five h4.spb-center-heading span:after {border-color: #222222;}.alt-five .full-width-text:after {border-top-color:#FFFFFF;}.alt-five h4.spb-text-heading, .alt-five h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-six {background-color: #FFFFFF;}.asset-bg.alt-six, .asset-bg .alt-six, .asset-bg.alt-six h1, .asset-bg.alt-six h2, .asset-bg.alt-six h3, .asset-bg.alt-six h3, .asset-bg.alt-six h4, .asset-bg.alt-six h5, .asset-bg.alt-six h6, .alt-six .carousel-wrap > a {color: #222222;}.asset-bg.alt-six h4.spb-center-heading span:before, .asset-bg.alt-six h4.spb-center-heading span:after {border-color: #222222;}.alt-six .full-width-text:after {border-top-color:#FFFFFF;}.alt-six h4.spb-text-heading, .alt-six h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-seven {background-color: #FFFFFF;}.asset-bg.alt-seven, .asset-bg .alt-seven, .asset-bg.alt-seven h1, .asset-bg.alt-seven h2, .asset-bg.alt-seven h3, .asset-bg.alt-seven h3, .asset-bg.alt-seven h4, .asset-bg.alt-seven h5, .asset-bg.alt-seven h6, .alt-seven .carousel-wrap > a {color: #222222;}.asset-bg.alt-seven h4.spb-center-heading span:before, .asset-bg.alt-seven h4.spb-center-heading span:after {border-color: #222222;}.alt-seven .full-width-text:after {border-top-color:#FFFFFF;}.alt-seven h4.spb-text-heading, .alt-seven h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-eight {background-color: #FFFFFF;}.asset-bg.alt-eight, .asset-bg .alt-eight, .asset-bg.alt-eight h1, .asset-bg.alt-eight h2, .asset-bg.alt-eight h3, .asset-bg.alt-eight h3, .asset-bg.alt-eight h4, .asset-bg.alt-eight h5, .asset-bg.alt-eight h6, .alt-eight .carousel-wrap > a {color: #222222;}.asset-bg.alt-eight h4.spb-center-heading span:before, .asset-bg.alt-eight h4.spb-center-heading span:after {border-color: #222222;}.alt-eight .full-width-text:after {border-top-color:#FFFFFF;}.alt-eight h4.spb-text-heading, .alt-eight h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-nine {background-color: #FFFFFF;}.asset-bg.alt-nine, .asset-bg .alt-nine, .asset-bg.alt-nine h1, .asset-bg.alt-nine h2, .asset-bg.alt-nine h3, .asset-bg.alt-nine h3, .asset-bg.alt-nine h4, .asset-bg.alt-nine h5, .asset-bg.alt-nine h6, .alt-nine .carousel-wrap > a {color: #222222;}.asset-bg.alt-nine h4.spb-center-heading span:before, .asset-bg.alt-nine h4.spb-center-heading span:after {border-color: #222222;}.alt-nine .full-width-text:after {border-top-color:#FFFFFF;}.alt-nine h4.spb-text-heading, .alt-nine h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-ten {background-color: #FFFFFF;}.asset-bg.alt-ten, .asset-bg .alt-ten, .asset-bg.alt-ten h1, .asset-bg.alt-ten h2, .asset-bg.alt-ten h3, .asset-bg.alt-ten h3, .asset-bg.alt-ten h4, .asset-bg.alt-ten h5, .asset-bg.alt-ten h6, .alt-ten .carousel-wrap > a {color: #222222;}.asset-bg.alt-ten h4.spb-center-heading span:before, .asset-bg.alt-ten h4.spb-center-heading span:after {border-color: #222222;}.alt-ten .full-width-text:after {border-top-color:#FFFFFF;}.alt-ten h4.spb-text-heading, .alt-ten h4.spb-heading {border-bottom-color:#222222;}.asset-bg.light-style, .asset-bg.light-style h1, .asset-bg.light-style h2, .asset-bg.light-style h3, .asset-bg.light-style h3, .asset-bg.light-style h4, .asset-bg.light-style h5, .asset-bg.light-style h6 {color: #fff!important;}.asset-bg.dark-style, .asset-bg.dark-style h1, .asset-bg.dark-style h2, .asset-bg.dark-style h3, .asset-bg.dark-style h3, .asset-bg.dark-style h4, .asset-bg.dark-style h5, .asset-bg.dark-style h6 {color: #222!important;}#main-container { background: transparent url('https://www.creativografico.dev/webAOA/wp-content/uploads/2019/10/fondo-intranet-AOA-Colombia.jpg') repeat center top; background-size: auto; }.standard-post-content, .blog-aux-options li a, .blog-aux-options li form input, .masonry-items .blog-item .masonry-item-wrap, .widget .wp-tag-cloud li a, ul.portfolio-filter-tabs li.selected a, .masonry-items .portfolio-item-details {background: #FFFFFF;}.format-quote .standard-post-content:before, .standard-post-content.no-thumb:before {border-left-color: #FFFFFF;}body, h6, #sidebar .widget-heading h3, #header-search input, .header-items h3.phone-number, .related-wrap h4, #comments-list > h3, .item-heading h1, .sf-button, button, input[type='submit'], input[type='email'], input[type='reset'], input[type='button'], .spb_accordion_section h3, #header-login input, #mobile-navigation > div, .search-form input, input, button, select, textarea {font-family: 'Arial', Arial, Helvetica, Tahoma, sans-serif;}h1, h2, h3, h4, h5, .custom-caption p, span.dropcap1, span.dropcap2, span.dropcap3, span.dropcap4, .spb_call_text, .impact-text, .impact-text-large, .testimonial-text, .header-advert, .sf-count-asset .count-number, #base-promo, .sf-countdown, .fancy-heading h1, .sf-icon-character {font-family: 'Arial', Arial, Helvetica, Tahoma, sans-serif;}nav .menu li {font-family: 'Arial', Arial, Helvetica, Tahoma, sans-serif;}.mobile-browser .sf-animation, .apple-mobile-browser .sf-animation {
					opacity: 1!important;
					left: auto!important;
					right: auto!important;
					bottom: auto!important;
					-webkit-transform: scale(1)!important;
					-o-transform: scale(1)!important;
					-moz-transform: scale(1)!important;
					transform: scale(1)!important;
				}
				.mobile-browser .sf-animation.image-banner-content, .apple-mobile-browser .sf-animation.image-banner-content {
					bottom: 50%!important;
				}@media only screen and (max-width: 767px) {#top-bar nav .menu > li {border-top-color: #f7f7f7;}nav .menu > li {border-top-color: #a8ad00;}}</style>
<meta name='generator' content='Powered by Slider Revolution 6.0.9 - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface.' />
<link rel='icon' href='https://www.creativografico.dev/webAOA/wp-content/uploads/2019/09/cropped-icono-AOA-Colombia-32x32.png' sizes='32x32' />
<link rel='icon' href='https://www.creativografico.dev/webAOA/wp-content/uploads/2019/09/cropped-icono-AOA-Colombia-192x192.png' sizes='192x192' />
<link rel='apple-touch-icon-precomposed' href='https://www.creativografico.dev/webAOA/wp-content/uploads/2019/09/cropped-icono-AOA-Colombia-180x180.png' />
<meta name='msapplication-TileImage' content='https://www.creativografico.dev/webAOA/wp-content/uploads/2019/09/cropped-icono-AOA-Colombia-270x270.png' />
<script type='text/javascript'>function setREVStartSize(a){try{var b,c=document.getElementById(a.c).parentNode.offsetWidth;if(c=0===c||isNaN(c)?window.innerWidth:c,a.tabw=void 0===a.tabw?0:parseInt(a.tabw),a.thumbw=void 0===a.thumbw?0:parseInt(a.thumbw),a.tabh=void 0===a.tabh?0:parseInt(a.tabh),a.thumbh=void 0===a.thumbh?0:parseInt(a.thumbh),a.tabhide=void 0===a.tabhide?0:parseInt(a.tabhide),a.thumbhide=void 0===a.thumbhide?0:parseInt(a.thumbhide),a.mh=void 0===a.mh||''==a.mh?0:a.mh,'fullscreen'===a.layout||'fullscreen'===a.l)b=Math.max(a.mh,window.innerHeight);else{for(var d in a.gw=Array.isArray(a.gw)?a.gw:[a.gw],a.rl)(void 0===a.gw[d]||0===a.gw[d])&&(a.gw[d]=a.gw[d-1]);for(var d in a.gh=void 0===a.el||''===a.el||Array.isArray(a.el)&&0==a.el.length?a.gh:a.el,a.gh=Array.isArray(a.gh)?a.gh:[a.gh],a.rl)(void 0===a.gh[d]||0===a.gh[d])&&(a.gh[d]=a.gh[d-1]);var e,f=Array(a.rl.length),g=0;for(var d in a.tabw=a.tabhide>=c?0:a.tabw,a.thumbw=a.thumbhide>=c?0:a.thumbw,a.tabh=a.tabhide>=c?0:a.tabh,a.thumbh=a.thumbhide>=c?0:a.thumbh,a.rl)f[d]=a.rl[d]<window.innerWidth?0:a.rl[d];for(var d in e=f[0],f)e>f[d]&&0<f[d]&&(e=f[d],g=d);var h=c>a.gw[g]+a.tabw+a.thumbw?1:(c-(a.tabw+a.thumbw))/a.gw[g];b=a.gh[g]*h+(a.tabh+a.thumbh)}void 0===window.rs_init_css&&(window.rs_init_css=document.head.appendChild(document.createElement('style'))),document.getElementById(a.c).height=b,window.rs_init_css.innerHTML+='#'+a.c+'_wrapper { height: '+b+'px }'}catch(a){console.log('Failure at Presize of Slider:'+a)}};</script>
			
	<!--// CLOSE HEAD //-->
	</head>
	
	<!--// OPEN BODY //-->
	<body class='page-template-default page page-id-79 wp-custom-logo mini-header-enabled page-shadow header-shadow layout-fullwidth responsive-fluid search-1'>
		
		<div id='header-search'>
			<div class='container clearfix'>
				<i class='ss-search'></i>
				<form method='get' class='search-form' action='https://www.creativografico.dev/webAOA/'><input type='text' placeholder='Search for something...' name='s' autocomplete='off' /></form>
				<a id='header-search-close' href='#'><i class='ss-delete'></i></a>
			</div>
		</div>
		
				
		<div id='mobile-menu-wrap'>
<form method='get' class='mobile-search-form' action='https://www.creativografico.dev/webAOA/'><input type='text' placeholder='Search' name='s' autocomplete='off' /></form>
<a class='mobile-menu-close'><i class='ss-delete'></i></a>
<nav id='mobile-menu' class='clearfix'>
<div class='menu-aoa-principal-container'><ul id='menu-aoa-principal' class='menu'><li  class='menu-item-787 menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-has-children   '><a title='Servicios integrales de movilidad' href='https://www.creativografico.dev/webAOA/'><span class='menu-item-text'>Servicios<span class='nav-line'></span></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-100 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Vehículo de Reemplazo AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/vehiculo-de-reemplazo/'>Vehículo de Reemplazo</a></li>
	<li  class='menu-item-99 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Renta de Vehículos AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/renta-de-vehiculos/'>Renta de Vehículos</a></li>
	<li  class='menu-item-98 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Renting Operativo AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/renting-operativo/'>Renting Operativo</a></li>
	<li  class='menu-item-97 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Transporte Especial AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/transporte-especial/'>Transporte Especial</a></li>
</ul>
</li>
<li  class='menu-item-74 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children   '><a title='Conócenos Información Corporativa AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/informacion-corporativa-aoa-colombia/'><span class='menu-item-text'>Conócenos<span class='nav-line'></span></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-1174 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Nuestra Empresa Información Corporativa AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/informacion-corporativa-aoa-colombia/'>Nuestra Empresa</a></li>
	<li  class='menu-item-2578 menu-item menu-item-type-custom menu-item-object-custom   '><a href='https://www.creativografico.dev/webAOA/index.php/informacion-corporativa-aoa-colombia/#mision'>Misión</a></li>
	<li  class='menu-item-2577 menu-item menu-item-type-custom menu-item-object-custom   '><a href='https://www.creativografico.dev/webAOA/index.php/informacion-corporativa-aoa-colombia/#vision'>Visión</a></li>
	<li  class='menu-item-2583 menu-item menu-item-type-custom menu-item-object-custom   '><a href='https://www.creativografico.dev/webAOA/index.php/informacion-corporativa-aoa-colombia/#politica-sistema-gestion-calidad'>Política Sistema de la Gestión de Calidad</a></li>
	<li  class='menu-item-1177 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Oficinas a Nivel Nacional AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/oficinas-a-nivel-nacional/'>Oficinas a Nivel Nacional</a></li>
</ul>
</li>
<li  class='menu-item-73 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children   '><a title='Contacto AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/contacto-aoa-colombia/'><span class='menu-item-text'>Contacto<span class='nav-line'></span></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-1306 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Call Center AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/contacto-aoa-colombia/'>Call Center</a></li>
	<li  class='menu-item-1305 menu-item menu-item-type-post_type menu-item-object-page   '><a href='https://www.creativografico.dev/webAOA/index.php/contacto-por-aseguradora/'>Contacto por Aseguradora</a></li>
	<li  class='menu-item-1333 menu-item menu-item-type-post_type menu-item-object-page   '><a title='FAQ AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/faq-aoa-colombia/'>Preguntas Frecuentes</a></li>
	<li  class='menu-item-1304 menu-item menu-item-type-post_type menu-item-object-page   '><a title='PQR AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/pqr-aoa-colombia-2/'>Peticiones, Quejas y Reclamos</a></li>
	<li  class='menu-item-1303 menu-item menu-item-type-post_type menu-item-object-page   '><a href='https://www.creativografico.dev/webAOA/index.php/aviso-de-privacidad/'>Aviso de Privacidad</a></li>
	<li  class='menu-item-1302 menu-item menu-item-type-post_type menu-item-object-page   '><a href='https://www.creativografico.dev/webAOA/index.php/politica-de-proteccion-de-datos/'>Política de Protección de Datos</a></li>
	<li  class='menu-item-1301 menu-item menu-item-type-post_type menu-item-object-page   '><a href='https://www.creativografico.dev/webAOA/index.php/politica-anticorrupcion/'>Política Anticorrupción</a></li>
</ul>
</li>
<li  class='menu-item-82 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Clientes AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/clientes-aoa-colombia/'><span class='menu-item-text'>Clientes<span class='nav-line'></span></span></a></li>
<li  class='menu-item-81 menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-79 current_page_item   '><a title='Intranet AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/intranet-aoa-colombia/'><span class='menu-item-text'>Intranet<span class='nav-line'></span></span></a></li>
</ul></div></nav>
</div>
		
		<!--// OPEN #container //-->
				<div id='container'>
					
			<!--// HEADER //-->
			<div class='header-wrap'>
				
					
					
				<div id='header-section' class='header-6 logo-fade'>
<header id='header' class='sticky-header clearfix sticky-header-resized' style='position: fixed; top: 0px;'>
<div class='container'>
<div class='row'>
<div id='logo' class='logo-left clearfix'>
<a href='http://www.aoacolombia.com'>
<img class='standard' src='http://www.aoacolombia.com/wp-content/uploads/2019/09/cropped-logotipo-aoa-colombia-1.png' alt='AOA Colombia' width='500' height='50'>
</a>
<a href='#' class='visible-sm visible-xs mobile-menu-show'><i class='ss-rows'></i></a>
</div>
<div class='header-right'><nav class='search-nav std-menu'>
<ul class='menu'>
<li class='menu-search parent'><a href='#' class='header-search-link'><i class='ss-search'></i></a></li>
</ul>
</nav>
<nav id='main-navigation' class='mega-menu clearfix'>
<div class='menu-aoa-principal-container'><ul id='menu-aoa-principal-1' class='menu'><li class='menu-item-787 menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-has-children        parent'><a title='Servicios integrales de movilidad' href='http://www.aoacolombia.com/' class='dc-mega'>Servicios<span class='nav-line'></span><span class='dc-mega-icon'></span></a>
<div class='sub-container non-mega' style='left: 672.688px; top: 48px; z-index: 1000;'><ul class='sub-menu sub'>
	<li class='menu-item-100 menu-item menu-item-type-post_type menu-item-object-page'><a title='Vehículo de Reemplazo AOA Colombia' href='http://www.aoacolombia.com/index.php/vehiculo-de-reemplazo/'>Vehículo de Reemplazo</a></li>
	<li class='menu-item-99 menu-item menu-item-type-post_type menu-item-object-page'><a title='Renta de Vehículos AOA Colombia' href='http://www.aoacolombia.com/index.php/renta-de-vehiculos/'>Renta de Vehículos</a></li>
	<li class='menu-item-98 menu-item menu-item-type-post_type menu-item-object-page'><a title='Renting Operativo AOA Colombia' href='http://www.aoacolombia.com/index.php/renting-operativo/'>Renting Operativo</a></li>
	<li class='menu-item-97 menu-item menu-item-type-post_type menu-item-object-page'><a title='Transporte Especial AOA Colombia' href='http://www.aoacolombia.com/index.php/transporte-especial/'>Transporte Especial</a></li>
</ul></div>
</li>
<li class='menu-item-74 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children        parent'><a title='Conócenos Información Corporativa AOA Colombia' href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/' class='dc-mega'>Conócenos<span class='nav-line'></span><span class='dc-mega-icon'></span></a>
<div class='sub-container non-mega' style='left: 803.375px; top: 48px; z-index: 1000;'><ul class='sub-menu sub'>
	<li class='menu-item-1174 menu-item menu-item-type-post_type menu-item-object-page'><a title='Nuestra Empresa Información Corporativa AOA Colombia' href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/'>Nuestra Empresa</a></li>
	<li class='menu-item-2578 menu-item menu-item-type-custom menu-item-object-custom'><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#mision'>Misión</a></li>
	<li class='menu-item-2577 menu-item menu-item-type-custom menu-item-object-custom'><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#vision'>Visión</a></li>
	<li class='menu-item-2583 menu-item menu-item-type-custom menu-item-object-custom'><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#politica-sistema-gestion-calidad'>Política Sistema de la Gestión de Calidad</a></li>
	<li class='menu-item-1177 menu-item menu-item-type-post_type menu-item-object-page'><a title='Oficinas a Nivel Nacional AOA Colombia' href='http://www.aoacolombia.com/index.php/oficinas-a-nivel-nacional/'>Oficinas a Nivel Nacional</a></li>
</ul></div>
</li>
</li>
<li class='menu-item-73 menu-item menu-item-type-post_type menu-item-object-page current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor menu-item-has-children        parent'><a title='Contacto AOA Colombia' href='http://www.aoacolombia.com/index.php/contacto-aoa-colombia/' class='dc-mega'>Contacto<span class='nav-line'></span><span class='dc-mega-icon'></span></a>
<div class='sub-container non-mega' style='left: 898.094px; top: 48px; z-index: 1000;'><ul class='sub-menu sub'>
	<li class='menu-item-1306 menu-item menu-item-type-post_type menu-item-object-page'><a title='Call Center AOA Colombia' href='http://www.aoacolombia.com/index.php/contacto-aoa-colombia/'>Call Center</a></li>
	<li class='menu-item-1305 menu-item menu-item-type-post_type menu-item-object-page'><a href='http://www.aoacolombia.com/index.php/contacto-por-aseguradora/'>Contacto por Aseguradora</a></li>
	<li class='menu-item-1333 menu-item menu-item-type-post_type menu-item-object-page'><a title='FAQ AOA Colombia' href='http://www.aoacolombia.com/index.php/faq-aoa-colombia/'>Preguntas Frecuentes</a></li>
	<li class='menu-item-1304 menu-item menu-item-type-post_type menu-item-object-page'><a title='PQR AOA Colombia' href='http://www.aoacolombia.com/index.php/pqr-aoa-colombia-2/'>Peticiones, Quejas y Reclamos</a></li>
	<li class='menu-item-1303 menu-item menu-item-type-post_type menu-item-object-page'><a href='http://www.aoacolombia.com/index.php/aviso-de-privacidad/'>Aviso de Privacidad</a></li>
	<li class='menu-item-1302 menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-1297 current_page_item'><a href='http://www.aoacolombia.com/index.php/politica-de-proteccion-de-datos/'>Política de Protección de Datos</a></li>
	<li class='menu-item-1301 menu-item menu-item-type-post_type menu-item-object-page'><a href='http://www.aoacolombia.com/index.php/politica-anticorrupcion/'>Política Anticorrupción</a></li>
</ul></div>
</li>
<li class='menu-item-82 menu-item menu-item-type-post_type menu-item-object-page'><a title='Clientes AOA Colombia' href='http://www.aoacolombia.com/index.php/clientes-aoa-colombia/'>Clientes<span class='nav-line'></span></a></li>
<li class='menu-item-81 menu-item menu-item-type-post_type menu-item-object-page'><a title='Intranet AOA Colombia' href='http://www.aoacolombia.com/index.php/intranet-aoa-colombia/'>Intranet<span class='nav-line'></span></a></li>
</ul></div></nav>

</div>
</div> <!-- CLOSE .row -->
</div> <!-- CLOSE .container -->
</header>
				</div>

			</div>
			
			<!--// OPEN #main-container //-->
			<div id='main-container' class='clearfix'>
				
												
				            
            			<div class='page-heading page-heading-hidden clearfix asset-bg none'>
			                <div class='container'>
                    <div class='heading-text'>

                        
                            <h1 class='entry-title'>Intranet AOA Colombia</h1>

                                                
                        
                    </div>

					<div id='breadcrumbs'>
<span property='itemListElement' typeof='ListItem'><a property='item' typeof='WebPage' title='Go to AOA Colombia.' href='https://www.creativografico.dev/webAOA' class='home' ><span property='name'>AOA Colombia</span></a><meta property='position' content='1'></span> &gt; <span class='post post-page current-item'>Intranet AOA Colombia</span></div>

                </div>
            </div>
        				
									<!--// OPEN .container //-->
					<div class='container'>
					
					
									
					<!--// OPEN #page-wrap //-->
					<div id='page-wrap'>	
					
					

<div class='inner-page-wrap has-no-sidebar clearfix'>
		
	
	<!-- OPEN page -->
	<div class='clearfix ' id='79'>
	
					<div class='page-content clearfix'>
	
				
<div class='wp-block-image'><figure class='aligncenter'><img src='https://www.creativografico.dev/webAOA/wp-content/uploads/2019/09/soluciones-movilidad-triangulo-AOA-Colombia.png' alt='Soluciones Integrales de Movilidad Icono Triángulo de Seguridad AOA Colombia' class='wp-image-144'/></figure></div>

				<div class='link-pages'></div>
				

				
					<div class='wp-block-columns has-3-columns separacion-superior'>
				<div class='wp-block-column banco'>
				<div class='columna'>
					<h3 class='texto-bold-1'>Call</h3>
					<hr class='linea-servicios'>
				<img class='imagen-servicios' src='https://www.creativografico.dev/webAOA/wp-content/uploads/2019/10/contacto-politica-anticorrupcion-AOA-Colombia.jpg'>
				  <p class='parrafo-servicios'></p>
				  <br>
				  	<a href='https://app.aoacolombia.com/Control/operativo/callLogin.php?Acc=ingreso_sistema&SESION_PUBLICA=1 class='boton-cuadrado'>Ingresar</a>
				   </div>
				</div>

				
				</div>
				<div class='franja-informacion'>
				<img class='logo-AOA-principal' src='http://www.aoacolombia.com/wp-content/uploads/2019/09/logotipo-aoa-colombia-principal.png'>
				<p class='informacion-oficina'>
					<b>Oficina Principal – Bogotá – Morato</b>
					<br>
					Carrera 69 B No. 98 A – 10
					<br>
					<b>Call Center AOA</b> 018000186262
					<br>
					Teléfono +(571) 8837069
				</p>
				<hr class='linea-vertical'>
				<p class='informacion-afiliacion'>
					<b>AOA Colombia compañía afiliada</b>
				</p>
				<img class='logo-asorenting' src='http://www.aoacolombia.com/wp-content/uploads/2020/03/logotipo-asorenting.png'>
			<img class='logo-iso' src='http://www.aoacolombia.com/wp-content/uploads/2020/03/icontec-iso-9001.png'>
			</div>

			<!-- Modal -->
			<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>  
			  <div class='modal-dialog' role='document'>
				<div class='modal-content'>
			
						<div class=' alert alert-warning  formulario-acceso-clientes' role='alert'>
						  <h2 class='alert-heading  text-center'>
					 <img src='../img/Covid19-V6.jpg'>
				
				  </div>
				  <div class='modal-footer'>
					<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar  </button>
				  </div>
				</div>
			  </div>
			</div>


	

			
								<div class='link-pages'></div>
								
												
							</div>
							
					
					<!-- CLOSE page -->
					</div>

						
					
				</div>

				<!--// WordPress Hook //-->
									
									<!--// CLOSE #page-wrap //-->			
									</div>
								
								<!--// CLOSE .container //-->
								</div>
								
							<!--// CLOSE #main-container //-->
							</div>
							
													
							<div id='footer-wrap'>
							
										
							<!--// OPEN #footer //-->
						<div id='footer-wrap'>
			
						
			<!--// OPEN #footer //-->
			<section id='footer' class=''>
				<div class='container'>
					<div id='footer-widgets' class='row clearfix'>
																		
						<div class='col-sm-12'>
													<section id='custom_html-5' class='widget_text widget widget_custom_html clearfix'><div class='textwidget custom-html-widget'><p class='parrafo-footer-1'>
	Oficinas a nivel nacional
</p>
<p class='parrafo-footer-2'>
Bogotá - Medellín - Barranquilla - Cali - Pereira - Bucaramanga - Ibague - Neiva - Cúcuta - Pasto - Villavicencio - Cartagena - Manizales - Montería - Tunja - Popayán - Valledupar - Santa Marta - Sincelejo - Armenia
</p>
<hr class='linea-footer'></div></section>												
						</div>
												
					</div>
				</div>	
			
			<!--// CLOSE #footer //-->
			</section>	
						
						
			<!--// OPEN #copyright //-->
			<footer id='copyright' class=''>
				<div class='container'>
					<p>
						©2020 - <a href='http://www.aoacolombia.com'>AOA Colombia</a> - Todos los derechos reservados | Powered by <a href='https://www.creativografico.dev'>www.creativografico.dev</a>												
					</p>
					<nav class='footer-menu std-menu'>
						<div class='menu-aoa-footer-container'><ul id='menu-aoa-footer' class='menu'><li id='menu-item-753' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-753'><a title='Mapa de Navegación AOA Colombia' href='http://www.aoacolombia.com/index.php/mapa-de-navegacion-aoa-colombia/'>Mapa de Navegación</a></li>
<li id='menu-item-788' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-788'><a title='Servicios integrales de movilidad' href='http://www.aoacolombia.com/'>Servicios</a></li>
<li id='menu-item-748' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-748'><a title='FAQ AOA Colombia' href='http://www.aoacolombia.com/index.php/faq-aoa-colombia/'>FAQ</a></li>
<li id='menu-item-705' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-705'><a title='Contacto AOA Colombia' href='http://www.aoacolombia.com/index.php/contacto-aoa-colombia/'>Contacto</a></li>
</ul></div>					</nav>
				</div>
			
							<!--// CLOSE #footer //-->
							</section>	
										
										
							<!--// OPEN #copyright //-->
							<footer id='copyright' class=''>
								<div class='container'>
									<p>
										©2020 - <a href='http://www.aoacolombia.com'>AOA Colombia</a> - Todos los derechos reservados												
									</p>
									<nav class='footer-menu std-menu'>
										<div class='menu-aoa-footer-container'><ul id='menu-aoa-footer' class='menu'><li id='menu-item-753' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-753'><a title='Mapa de Navegación AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/mapa-de-navegacion-aoa-colombia/'>Mapa de Navegación</a></li>
				<li id='menu-item-788' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-788'><a title='Servicios integrales de movilidad' href='https://www.creativografico.dev/webAOA/'>Servicios</a></li>
				<li id='menu-item-748' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-748'><a title='FAQ AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/faq-aoa-colombia/'>FAQ</a></li>
				<li id='menu-item-705' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-705'><a title='Contacto AOA Colombia' href='https://www.creativografico.dev/webAOA/index.php/contacto-aoa-colombia/'>Contacto</a></li>
				</ul></div>					</nav>
								</div>
							<!--// CLOSE #copyright //-->
							</footer>
							
										
							</div>
						
						<!--// CLOSE #container //-->
						</div>
						
								
								<!--// BACK TO TOP //-->
						<div id='back-to-top' class='animate-top'><i class='ss-navigateup'></i></div>
								
						<!--// FULL WIDTH VIDEO //-->
						<div class='fw-video-area'><div class='fw-video-close'><i class='ss-delete'></i></div></div><div class='fw-video-spacer'></div>
						
												
						<!--// FRAMEWORK INCLUDES //-->
						<div id='sf-included' class=''></div>

									
						<!--// WORDPRESS FOOTER HOOK //-->
									<div id='sf-option-params'
								data-lightbox-enabled='1'
								data-lightbox-nav='default'
								data-lightbox-thumbs='true'
								data-lightbox-skin='light'
								data-lightbox-sharing='true'
								data-slider-slidespeed='6000'
								data-slider-animspeed='500'
								data-slider-autoplay='1'></div>
							  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js' type='text/javascript'></script>

						<script type='text/javascript'>
				/* <![CDATA[ */
				var wpcf7 = {'apiSettings':{'root':'https:\/\/www.creativografico.dev\/webAOA\/index.php\/wp-json\/contact-form-7\/v1','namespace':'contact-form-7\/v1'}};
				/* ]]> */
				</script>
					<script language='javascript' src='../Administrativo/inc/js/aqrenc.js'></script>

				<script> 

</script>

	
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/contact-form-7/includes/js/scripts.js?ver=5.1.4'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/responsive-accordion-and-collapse/js/bootstrap.js?ver=5.2.5'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/responsive-accordion-and-collapse/js/accordion.js?ver=5.2.5'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/plugins/tabs-responsive/assets/js/bootstrap.js?ver=5.2.5'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/modernizr.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/bootstrap.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery-ui-1.10.2.custom.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.flexslider-min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.easing.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/owl.carousel.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/dcmegamenu.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-includes/js/imagesloaded.min.js?ver=3.2.0'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-includes/js/masonry.min.js?ver=3.3.2'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.appear.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.animatenumber.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.animOnScroll.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.classie.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.countdown.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.countTo.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.easypiechart.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.equalHeights.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.hoverIntent.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.infinitescroll.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.isotope.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/imagesloaded.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.parallax.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.smartresize.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.stickem.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.stickyplugin.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/jquery.viewport.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/lib/ilightbox.min.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-content/themes/dante/js/functions.js'></script>
<script type='text/javascript' src='https://www.creativografico.dev/webAOA/wp-includes/js/wp-embed.min.js?ver=5.2.5'></script>

	
	<!--// CLOSE BODY //-->
	</body>


<!--// CLOSE HTML //-->
</html>";

?>