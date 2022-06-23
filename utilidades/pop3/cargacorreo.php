<?php
echo "<HTML>
	<HEAD>
	<TITLE>Test for Manuel Lemos's PHP POP3 class</TITLE>
	</HEAD>
	<BODY>";
require("pop3.php");

/* Uncomment when using SASL authentication mechanisms */
/*
require("sasl.php");
*/

	$pop3=new pop3_class;
	$pop3->hostname="mail.aoacolombia.com";             /* POP 3 server host name                      */
	$pop3->port=110;                         /* POP 3 server host port,
	                                            usually 110 but some servers use other ports
	                                            Gmail uses 995                              */
	$pop3->tls=0;                            /* Establish secure connections using TLS      */
	$user="arturoquintero@aoacolombia.com";                        /* Authentication user name                    */
	$password="Santiago6!";                    /* Authentication password                     */
	$pop3->realm="";                         /* Authentication realm or domain              */
	$pop3->workstation="";                   /* Workstation for NTLM authentication         */
	$apop=0;                                 /* Use APOP authentication                     */
	$pop3->authentication_mechanism="PLAIN";  /* SASL authentication mechanism               */
	$pop3->debug=0;                          /* Output debug information                    */
	$pop3->html_debug=1;                     /* Debug information is in HTML                */
	$pop3->join_continuation_header_lines=1; /* Concatenate headers split in multiple lines */

	if(($error=$pop3->Open())=="")
	{
		echo "<PRE>Connected to the POP3 server &quot;".$pop3->hostname."&quot;.</PRE>\n";
		if(($error=$pop3->Login($user,$password,$apop))=="")
		{
			echo "<PRE>User &quot;$user&quot; logged in.</PRE>\n";
			if(($error=$pop3->Statistics($messages,$size))=="")
			{
				echo "<PRE>There are $messages messages in the mail box with a total of $size bytes.</PRE>\n";
				$result=$pop3->ListMessages("",0);
				if(GetType($result)=="array")
				{
					//for(Reset($result),$message=0;$message<count($result);Next($result),$message++)
					//	echo "<PRE>Message ",Key($result)," - ",$result[Key($result)]," bytes.</PRE>\n";
					$result=$pop3->ListMessages("",1);
					if(GetType($result)=="array")
					{
						//for(Reset($result),$message=0;$message<count($result);Next($result),$message++)
						//	echo "<PRE>Message ",Key($result),", Unique ID - \"",$result[Key($result)],"\"</PRE>\n";
						if($messages>0)
						{
							for($mensaje=0;$mensaje<=$messages; $mensaje++)
							{
								if(($error=$pop3->RetrieveMessage($mensaje,$headers,$body,-1))=="")
								{
									echo "<h3>Message $mensaje:\n---Message headers starts below---</h3>";
									$Cabecera=procesa_cabecera($headers);
									print_r($Cabecera);
									
									//for($line=0;$line<count($headers);$line++) echo "<PRE>",HtmlSpecialChars($headers[$line]),"</PRE>";
									echo "<b>---Message headers ends above---<br>---Message body starts below---</b>";
									$QP=false;
									for($line=0;$line<count($body);$line++) 
									{
										if($QP) echo quoted_printable_decode($body[$line])."<br>";
										else
										{
											echo htmlspecialchars($body[$line])."<br>";
											if(strpos($body[$line],'quoted-printable')) $QP=true;
										}
									}
									echo "<b>---Message body ends above---</b>\n";
								}
							}
						}
						if($error=="" && ($error=$pop3->Close())=="") echo "<PRE>Disconnected from the POP3 server &quot;".$pop3->hostname."&quot;.</PRE>\n";
					}
					else $error=$result;
				}
				else $error=$result;
			}
		}
	}
	if($error!="") echo "<H2>Error: ",HtmlSpecialChars($error),"</H2>";

function procesa_cabecera($h)
{
	$Resultado=array();
	for($i=0;$i<count($h);$i++) 
	{
		$Linea=split(':',$h[$i]);
		$Resultado[$Linea[0]]=quoted_printable_decode(HtmlSpecialChars(utf8_decode($Linea[1].$Linea[2])));
	}
	return $Resultado;
}		
echo "</BODY></HTML>";
