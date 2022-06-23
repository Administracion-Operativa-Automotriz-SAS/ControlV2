<?php

$Ahora=date('Y-m-d');
echo "
<script language='javascript'>
function carga()
{
window.open('ws.php?Acc=procesa_ws&FECHA1=$Ahora&FECHA2=$Ahora&EMAIL=arturoquintero@aoacolombia.com,jforero@mapfre.com.co,wfsilva@mapfre.com.co,anvalbu@mapfre.com.co&enlinea=on','_self');
}
</script>
<body onload='carga()'>llamando la rutina</body>";
?>