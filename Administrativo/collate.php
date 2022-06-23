<?php
include('inc/funciones_.php');
set_time_limit(0);
if(function_exists('html')) html(); else include('inc/html.php');
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
echo "<body>
	<form action='collate.php' target='_self' method='POST' name='forma' id='forma'>
	Character Set: <input type='text' name='cs' id='cs' value='utf8' size='20' maxlength='20'>  
	Collate <input type='text' name='cl' id='cl' value='utf8_general_ci' size='30' maxlength='50'>  
	<input type='hidden' name='Acc' value='ajustar_collate'> A partir de : <input type='text' name='apartir' id='apartir' value='' size='10' maxlength='30'>
	<input type='submit' name='continuar' id='continuar' value=' Continuar '>
	</form>
	<form action='collate.php' target='_self' method='POST' name='forma1' id='forma1'>
	ReConstruir Funciones internas MySql. 
	<input type='hidden' name='Acc' value='recrear_funciones'>
	<input type='submit' name='continuar' id='continuar' value=' Continuar '>
	</form>
";


function ajustar_collate()
{
	global $cs,$cl,$apartir;
	$Tablas=q("show tables ");
	require('inc/link.php');
	while($T=mysql_fetch_row($Tablas))
	{
		$Tabla=$T[0].' '.$T[1];
		if($Tabla>$apartir)
		{
			echo "<h3>$Tabla</h3>";
			mysql_query("alter table $Tabla default character set $cs collate $cl ",$LINK);
			mysql_query("alter table $Tabla convert to character set $cs collate $cl ",$LINK);
		//	$Campos=mysql_query("show full columns from $Tabla",$LINK);
		//	while($C=mysql_fetch_row($Campos))
		//	{
		//		$Campo=$C[0];$Tipo=$C[1]; $Otro=$C[2];
		//		echo "<li>$Campo [$Tipo] $Otro ";
		//		if($C[2] && $C[2]!='unsigned') { echo "Cambiando.."; mysql_query("alter table $Tabla modify column $Campo $Tipo character set $cs collate $cl ",$LINK);}
		//	}
		}
	}

	mysql_close($LINK);
}


function recrear_funciones()
{
	$Tablas=q("show tables like '%_t' ");
	require('inc/link.php');
	while($T=mysql_fetch_row($Tablas))
	{
		$Tabla=$T[0];
		if(r($Tabla,2)=='_t')
		{
			echo "<hr>TABLA: <b>$Tabla</b><br>";
			$Relacionados=mysql_query("select campo,tipo,traet,traen,trael,traex from $Tabla where (traet!='' and traen!='' and trael!='') or (traex!='') ",$LINK);
			while($R=mysql_fetch_object($Relacionados))
			{
				
				if($R->traet && $R->traen && $R->trael)
				{
					$Nombre_funcion='T_'.$R->traet;
					echo "<br>Creando la funcion <b>$Nombre_funcion</b>";
					mysql_query("drop function if exists $Nombre_funcion ");
				//	$Tipo=qom("show columns from $R->traet like '$R->trael' ",$LINK);
					$Comando="create function $Nombre_funcion(Dato_ $R->tipo) returns varchar(200) reads sql data
						begin Declare Resultado_ varchar(200) default '';
						Select $R->traen into Resultado_ from $R->traet where $R->trael = Dato_ limit 1;
						return Resultado_; end";
					if(!mysql_query($Comando,$LINK)) die($Comando.'<br>'.$Tabla.'<br>'.$this->campo.'<br>'.mysql_error($LINK) );
				}
				elseif(!$R->traet && $R->traex && strpos($R->traex,';'))
				{
					$Cases=str_replace(';', "' When '",$R->traex);
					$Cases=str_replace(',',"' Then '",$Cases);
					$Nombre_funcion='M_'.$Tabla.'_'.$R->campo;
					mysql_query("drop function if exists $Nombre_funcion ");
					$Comando="create function $Nombre_funcion (Dato_ $R->tipo) returns varchar(200) no sql
					begin Declare Resultado varchar(200) default '';
					select case Dato when '$Cases' end into Resultado; return Resultado; end";
					if(!mysql_query($Comando,$LINK)) die($Comando.'<br>'.$Tabla.'<br>'.$this->campo.'<br>'.mysql_error($LINK) );
				}
			}
		}
	}
	mysql_close($LINK);
}
?>