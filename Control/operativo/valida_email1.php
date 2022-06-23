<?php

if (!$_POST['mail'])
{
	echo "<html><body>
	<form action=valida_email1.php method=POST>
	<input type=text name=mail>
	<input type=submit name=boton value=Aceptar>
	</form>
	</html>";
}

else
{
	$Resultado= ValidateMail($_POST['mail']);
	if($Resultado[0]) echo $Resultado[1];
	else echo "Fallo ".$Resultado[1];
}
// Step 1 -- Initialize the script
// The first step in any script, of course, is to initialize any variables you'll be using. In this case, declare any global variables you'll be using:

function ValidateMail($Email)
{
	global $HTTP_HOST;
	$result = array();
	// Step 2 -- Check the e-mail address format
	// Next, you'll use our regular expression to determine if the e-mail address is properly formatted. If the e-mail address is not valid, return in error:
	if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $Email)) 
	{
		$result[0]=false;
		$result[1]="$Email is not properly formatted";
		return $result;
	}
	// Step 3 -- Find the address of the mail server
	// Now, split apart the e-mail address and use the domain name to search for a mail server you can use to further check the e-mail address. If no mail server is found, you'll just use the domain address as a mail server address:
	// Note: In the event that the optional step 4 is not followed, the else portion of this step must return in error in order for the script to function properly.
	list ( $Username, $Domain ) = split ("@",$Email);
	if (getmxrr($Domain, $MXHost))  
	{
		$ConnectAddress = $MXHost[0];
	} 
	else 
	{
		$ConnectAddress = $Domain;
	}
	// Step 4 -- Connect to mail server and check e-mail address (OPTIONAL)
	// Finally, once you have the best guess at a mail server, it's time to open a connection and talk to the server. As I stated earlier, this step is optional. After every command you send, you'll need to read a kilobyte (1024 bytes) of data from the server. It should be more than enough to receive the complete response from the server for that command.
	// Note that you'll store the output from the server in three separate variables: $To, $From and $Out. This is done so you can check the responses after you close the connection, to see if you actually have a real e-mail address or not.
	// If the script cannot connect at all, or the e-mail address wasn't valid, set the $result array to the proper values:
	$Connect = fsockopen ( $ConnectAddress, 25 );
	if ($Connect) 
	{
		if (ereg("^220", $Out = fgets($Connect, 1024))) 
		{
			fputs ($Connect, "HELO $HTTP_HOST\r\n");
			$Out = fgets ( $Connect, 1024 );
			fputs ($Connect, "MAIL FROM: <{$Email}>\r\n");
			$From = fgets ( $Connect, 1024 );
			fputs ($Connect, "RCPT TO: <{$Email}>\r\n");
			$To = fgets ($Connect, 1024);
			fputs ($Connect, "QUIT\r\n");
			fclose($Connect);
			if (!ereg ("^250", $From) || !ereg ( "^250", $To )) 
			{
				$result[0]=false;
				$result[1]="Server rejected address";
				return $result;
			}
		} 
		else 
		{
			$result[0] = false;
			$result[1] = "No response from server";
			return $result;
		}

	} 
	else 
	{
		$result[0]=false;
		$result[1]="Can not connect E-Mail server.";
		return $result;
	}
	// Step 5 -- Return the results
	// Finally, our last and easiest step is to return the results and finish:
	$result[0]=true;
	$result[1]="$Email appears to be valid.";
	return $result;
} // end of function

?>