<?
include('inc/funciones_.php');
q("update siniestro set ubicacion=0 where id=$id");
echo "<body onload='opener.location.reload();window.close();void(nul);'></body>";
?>
