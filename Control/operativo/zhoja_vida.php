<?php
include('inc/funciones_.php');
html();
$E=qo("select * from csa_estudiante where id=$id");
echo "$E->apellido1 $E->apellido2 $E->nombre1 $E->nombre2";
?>

