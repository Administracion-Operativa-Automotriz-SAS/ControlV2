var $TABLE = $('#table');
var $BTN = $('#export-btn');
var $EXPORT = $('#export');

$('.table-add').click(function () {
  var $clone = $TABLE.find('tr.hide').clone(true).removeClass('hide table-line');
  $TABLE.find('table').append($clone);
});

$('.table-remove').click(function () {
  $(this).parents('tr').detach();
});

$('.table-up').click(function () {
  var $row = $(this).parents('tr');
  if ($row.index() === 1) return; // Don't go above the header
  $row.prev().before($row.get(0));
});

$('.table-down').click(function () {
  var $row = $(this).parents('tr');
  $row.next().after($row.get(0));
});

// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

$BTN.click(function () {
  var $rows = $TABLE.find('tr:not(:hidden)');
  var headers = [];
  var data = [];
  
  // Get the headers (add special header logic here)
  $($rows.shift()).find('th:not(:empty)').each(function () {
	headers.push($(this).text().toLowerCase());
  });
  
  // Turn all existing rows into a loopable array
  $rows.each(function () {
	var $td = $(this).find('td');
	var h = {};
	
	// Use the headers from earlier to name our hash keys
	headers.forEach(function (header, i) {
	  h[header] = $td.eq(i).text();   
	});
	
	data.push(h);
  });
  
  // Output the result
  $EXPORT.text(JSON.stringify(data));
});
		//alert("here");
		var start = document.getElementById('start');
		console.log(start);
		if(start != null)
		{
			start.focus();
			start.style.backgroundColor = 'green';
			start.style.color = 'white';
		}
		
		function dotheneedful(sibling) {
		  if (sibling != null) {
			start.focus();
			start.style.backgroundColor = '';
			start.style.color = '';
			sibling.focus();
			sibling.style.backgroundColor = 'green';
			sibling.style.color = 'white';
			start = sibling;
		  }
		}

		document.onkeydown = checkKey;

		function checkKey(e) {
		  e = e || window.event;
		  if (e.keyCode == '38') {
			// up arrow
			var idx = start.cellIndex;
			var nextrow = start.parentElement.previousElementSibling;
			if (nextrow != null) {
			  var sibling = nextrow.cells[idx];
			  dotheneedful(sibling);
			}
		  } else if (e.keyCode == '40') {
			// down arrow
			var idx = start.cellIndex;
			var nextrow = start.parentElement.nextElementSibling;
			if (nextrow != null) {
			  var sibling = nextrow.cells[idx];
			  dotheneedful(sibling);
			}
		  } else if (e.keyCode == '37') {
			// left arrow
			var sibling = start.previousElementSibling;
			dotheneedful(sibling);
		  } else if (e.keyCode == '39') {
			// right arrow
			var sibling = start.nextElementSibling;
			dotheneedful(sibling);
		  }
		}