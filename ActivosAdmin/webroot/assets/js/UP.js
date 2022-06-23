
$('.in-postor-tabs a:first').addClass('active');
	$('.in-secciontabs .in-tabs-caption').hide();
	$('.in-secciontabs .in-tabs-caption:first').show();

	$('.in-postor-tabs a').click(function(){
		$('.in-postor-tabs a').removeClass('active');
		$('.in-secciontabs .in-tabs-caption').hide();
		$(this).addClass('active');

		var activeTab = $(this).attr('href');
		$(activeTab).show();
		return false;
	});


//$(document).ready(function(){
//	var altura = $('.menu').offset().top;
//	
//	$(window).on('scroll', function(){
//		if ( $(window).scrollTop() > altura ){
//			$('.pre-header').addClass('prenav_hidden');
//		} else {
//			$('.pre-header').removeClass('prenav_hidden');
//		}
//	});
//});


//$(document).ready(function() {
//	$('.menu').addClass('blacki');
//	
//	$(window).scroll(function () {
//	   	if($(this).scrollTop() == 0) {
//	       $('.menu').addClass('blacki');
//	    } else {
//			$('.menu').removeClass('blacki');
//		}
//	});
//});
jQuery(function ($) {

    $(".sidebar-dropdown > a").click(function() {
  $(".sidebar-submenu").slideUp(200);
  if (
    $(this)
      .parent()
      .hasClass("active")
  ) {
    $(".sidebar-dropdown").removeClass("active");
    $(this)
      .parent()
      .removeClass("active");
  } else {
    $(".sidebar-dropdown").removeClass("active");
    $(this)
      .next(".sidebar-submenu")
      .slideDown(200);
    $(this)
      .parent()
      .addClass("active");
  }
});

$("#close-sidebar").click(function() {
  $(".page-wrapper").removeClass("toggled");
});
$("#show-sidebar").click(function() {
  $(".page-wrapper").addClass("toggled");
});


   
   
});







/********************************************************************************/
/********************************************************************************/
/********************************************************************************/
//-******************* MAPA */


		function initialize() {

			var myLatlng = new google.maps.LatLng(4.692184868803152, -74.03527736663818);

			var mapCanvas = document.getElementById('map-canvas');

			var mapOptions = {
				center: myLatlng,
				zoom:16,
				zoomControl: true,
				zoomControlOptions: {
					style: google.maps.ZoomControlStyle.SMALL
				},
				panControl: false,
				mapTypeControl: true,
				scrollwheel: false,
				styles: [
					{
						"featureType": "administrative",
						"elementType": "labels.text.fill",
						"stylers": [
							{
								"color": "#333333"
							},
					{
					 "-webkit-text-stroke": "1.0px #333333"
					},
					{
					 "-webkit-text-fill-color": "#333333" 
					}
						]
					},
					{
						"featureType": "landscape",
						"elementType": "all",
						"stylers": [
							{
								"color": "#ffffff"
							}
						]
					},
					{
						"featureType": "poi",
						"elementType": "all",
						"stylers": [
							{
								"visibility": "on"
							}
						]
					},
					{
						"featureType": "poi.business",
						"elementType": "geometry.fill",
						"stylers": [
							{
								"visibility": "on"
							}
						]
					},
					{
						"featureType": "road",
						"elementType": "all",
						"stylers": [
							{
								"saturation": -100
							},
							{
								"lightness": 10
							},
	//{
							   //"color": "#CCCCCC"
							//},
						]
					},
					{
						"featureType": "road.highway",
						"elementType": "all",
						"stylers": [
							{
								"visibility": "simplified"
							}
						]
					},
					{
						"featureType": "road.arterial",
						"elementType": "labels.icon",
						"stylers": [
							{
								"visibility": "off"
							}
						]
					},
					{
						"featureType": "transit",
						"elementType": "all",
						"stylers": [
							{
								"visibility": "on"
							}
						]
					},
					{
						"featureType": "water",
						"elementType": "all",
						"stylers": [
							{
								"color": "#353535"
							},
							{
								"visibility": "on"
							}
						]
					}
				]
			};

			var map = new google.maps.Map(mapCanvas, mapOptions);

			new google.maps.Marker({
						position: myLatlng
						, map: map
						, title: ''
						, icon: ''
						, cursor: 'default'

					}); 

		}
		


/********************************************************************************/
/********************************************************************************/
/********************************************************************************/