function resize_inputs(){
	$('input[type="text"]').each(resizeInput);
}

resize_inputs();

function resizeInput() {
	if($(this).val().length>4)
	{
		//dsdsdsdsdsd
		//alert($(this).val());
		
		$(this).attr('size', ($(this).val().length-3));				
		
	}
	else
	{
		//alert($(this).val());
		$(this).attr('size', ($(this).val().length+1));				
	}			
}



$('input[type="text"]')
// event handler
.keyup(resizeInput)
// resize on page load
//.each(resizeInput)


function make_visible(id)
{
	console.log("in");
	$("#einput"+id).css('visibility', 'visible');
}

function make_invisible(id)
{		
	console.log("invisible");
	$("#einput"+id).css('visibility', 'hidden');
}

$('textarea').on('paste input', function () {
	if ($(this).outerHeight() > this.scrollHeight){
		$(this).height(1)
	}
	while ($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))){
		$(this).height($(this).height() + 1)
		}
});

function reset_checks()
{
	$('input[type=radio]').each(function () {				
		if($(this).is(':checked'))
		{
			$(this).attr('checked',false);

		}
	});
				
		
}

