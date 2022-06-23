<?php
  echo "
<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>

<button type='button' class='btn btn-success openBtn'>Open Modal</button>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'>
    <div class='modal-dialog'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>×</button>
                <h4 class='modal-title'>Modal with Dynamic Content</h4>
            </div>
            <div class='modal-body'>

            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('.openBtn').on('click',function(){
    $('.modal-body').load('contacto.php',function(){
        $('#myModal').modal({show:true});
    });
});
</script>";  
	
	
?> 


