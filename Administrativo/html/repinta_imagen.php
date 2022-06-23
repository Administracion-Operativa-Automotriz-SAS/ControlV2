<?php
#if($_GET['width'] > 0) $width = $_GET['width']; else $width = 200;
#if($_GET['height'] > 0) $height = $_GET['height']; else $height = 200;
#if($_GET['filename']) $filename = $_GET['filename']; else $filename = 'civic.jpg';
if(preg_match('/\.jpg$/i',$filename))
	$src_image = imagecreatefromjpeg("$filename");
elseif(preg_match('/\.gif$/i',$filename))
	$src_image = imagecreatefromgif("$filename");
else
	$src_image = imagecreatefrompng("$filename");
$image = imagecreate($width, $height);
//asignamos los colores que ocuparemos más adelante
$bg = imagecolorallocate($image, 200, 200, 200);
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);

$margin_x = 2;
$margin_y = 2;

$w = $width-2*$margin_x;
$h = $height-2*$margin_y;

$src_w = imagesx($src_image);
$src_h = imagesy($src_image);

//¿desplegamos la imagen en la dimensión original?
if(($w > $src_w) && ($h > $src_h))
{
    $dst_w = $src_w;
    $dst_h = $src_h;
}
else
	//¿o escalamos la imagen de acuerdo a la dimensión horizontal?
	if(($w/$h) < ($src_w/$src_h))
	{
		$dst_w = $w;
		$dst_h = $w*$src_h/$src_w;
	}
	else
		//¿o la escalamos de acuerdo a la dimensión vertical?
	{
		$dst_w = $h*$src_w/$src_h;
		$dst_h = $h;
	}

imagecopyresized($image, $src_image, ($width-$dst_w)/2, ($height-$dst_h)/2,  0, 0, $dst_w, $dst_h, $src_w, $src_h);

//colocamos el texto sobre la imagen
//imagestring($image, 0, $margin_x, ($height-$margin_y), $filename, $black);

//encabezado correspondiente para los datos de salida
header("Content-type: image/jpeg");
//generamos la imagen
if(preg_match('/\.jpg$/i',$filename))
	imagejpeg($image);
elseif(preg_match('/\.gif$/i',$filename))
	imagegif($image);
else
	imagejpng($image);

//liberamos la memoria
imagedestroy($image);
?>