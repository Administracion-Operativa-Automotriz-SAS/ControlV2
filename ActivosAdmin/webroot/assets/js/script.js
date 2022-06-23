
$(document).ready(function() {
	$(".submenu").hide();
	$(".contenedor--menu").hide();

	   $(".icono").click(function() {
		$(".contenedor--menu").animate({
				width: "toggle"

			});
	  });   

	$( ".submenu" ).before(innerHTML = "\u25bc");
		$('.submenu')
		//despliega solo el submenu de ese menu concreto
		$('.menu__enlace').click(function(event){
			var elem = $(this).next();

			if(elem.is('ul')){          
				event.preventDefault();
				elem.slideToggle();
			}
		});

});//fin de la funcion ready