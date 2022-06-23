<?php
include('inc/funciones_.php');
$Pos=explode(',',$gprmc);
$Latp=$Pos[3];
$Lat=(((float)substr($Latp,0,2))+((float)(substr($Latp,2,16))/60)) * ($Pos[4]=='N'?1:-1);
$Lonp=$Pos[5];
$Lon=(((float)substr($Lonp,0,3)) + (((float)substr($Lonp,3,16))/60)) * ($Pos[6]=='W'?-1:1);
$Vel=$Pos[7];
$Ahora=date('Y-m-d H:i:s');
q("insert into gp (identificacion,lat,lon,velocidad,fecha) values ($acct,$Lat,$Lon,'$Vel','$Ahora') ");
?>