<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html><!-- Instance_Begin template="../Templates/admin.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- Instance_BeginEditable name="doctitle" -->
<title>File manager</title>
<meta name="author" content="alex @@ bitcontent.com">
<meta name="copyright" content="2004-2005 BitContent">
<meta name="description" content="Easy File Upload. Feel free to use it in any way you want under the GNU license. Just please let a link to my site somewhere in this page if I'm not asking too much :)">
<!-- Instance_EndEditable --><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- Instance_BeginEditable name="head" --><!-- Instance_EndEditable -->
<link href="inc/css/styles.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <br>
<?php if ('start' == $op){?>
<form name="form1" method="post" action="">

<table width="80%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">
  <tr bgcolor="#8F8F9B">
    <td colspan="5" class="text1"><b><font color="#ffffff">&nbsp;A r c h i v o s&nbsp;&nbsp;&nbsp;&nbsp; </font> <big> <font color="#f0f8ff"> : : </font> <font color="#e0f0ff"> : : </font> <font color="#d0e0f0"> : : </font> <font color="#c0d0e0"> : :</font></big></b></td>
  </tr>
  <tr align="center" bgcolor="#FFFFFF">
    <td>Archivo</td>
    <td>Tamaño</td>
    <td>Fecha</td>
    <td colspan="2">Acciones</td>
  </tr>
  <?php
  if ($fileList){
  	foreach($fileList as $rec){?>
  <tr bgcolor="#FFFFFF">
    <td><?=$rec['name']?></td>
    <td align="right"><?=number_format($rec['size'])?></td>
    <td align="right"><?=date("M d Y , H:i", $rec['date'])?></td>
    <td width="100" align="center"><a href="files.php?op=download&id=<?=$id?>&Id=<?=urlencode($rec['name'])?>" target="_blank">Descargar</a></td>
    <td width="100" align="center"><a href="files.php?op=delete&id=<?=$id?>&Id=<?=urlencode($rec['name'])?>">Borrar</a></td>
  </tr>
  <?php }
  }else{?>
  <tr bgcolor="#FFFFFF">
    <td colspan="5">No hay archivos </td>
  </tr>
  <?php }?>
  <tr align="center" bgcolor="#FFFFFF">
    <td colspan="5"><a href="?op=add&id=<?=$id?>">
      <input type="submit" value="Subir Archivo">
      <input name="op" type="hidden" id="op" value="add">
    </a></td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
<p><br>
    <?php }
	if ($op =="add"){?> </p>
<form action="" method="post" enctype="multipart/form-data">
  <table width="100%" border="0" align="center" cellpadding="4" cellspacing="1" bgcolor="#CCCCCC">
    <tr>
      <td bgcolor="#8F8F9B" class="text1"><b><font color="#ffffff">&nbsp;S u b i r&nbsp;&nbsp;&nbsp;A r c h i v o&nbsp; </font> <big> <font color="#f0f8ff"> : : </font> <font color="#e0f0ff"> : : </font> <font color="#d0e0f0"> : : </font> <font color="#c0d0e0"> : :</font></big></b></td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">        <p>&nbsp;</p>        <p>Archivo :
            <input type="file" name="file">
          </p>
        <p>(click en Browse, seleccione un archivo y presione Enviar)</p>
        <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF"><input name="Input" type="submit" value="Enviar">
          <input name="op" type="hidden" id="op" value="add">
          </td>
    </tr>
  </table>
  <br>
  <br>
  <br>
</form>
<?php }?>
 <p>&nbsp;</p>
 <!-- Instance_EndEditable --></td>
  </tr>
 </table>
</body>
<!-- Instance_End --></html>
