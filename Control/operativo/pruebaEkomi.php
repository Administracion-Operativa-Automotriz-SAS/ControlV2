<script>
/*
var url = 'https://aoasoluciones.com/hd/prueba.php';
fetch(url,{
	method: 'GET',
	headers:{
		'authorization': 'Basic RGVtb0FQSV9DbGFybzpDMTRSMDIwMk8=',
		'Content-Type': 'application/json',
		'Access-Control-Allow-Origin': 'https://aoasoluciones.com/hd/prueba.php',
		'Access-Control-Allow-Methods': 'DELETE, POST, GET, OPTIONS',
		'Access-Control-Allow-Headers': 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
		}
	
	
}).then((response) => {
	

	
})
*/
/*

        const toSendJson = {

            APIKEYAOA : "yNPlsmOGgZoGmH$7",
			enviar_data_ekomi_sms : "enviar_data_ekomi_sms",
			first_name : "Sergio",
			email : "sergiourbina7646@gmail.com",
			telephone : "+573202900788",
			numeroSiniestro : "2313123",
			documento : "1023010114",
			placa : "DDJ671"

        };

        const jsonString = JSON.stringify(toSendJson);

        const  xhr = new XMLHttpRequest();

        xhr.open("POST","https://aoasoluciones.com/hd/prueba.php");
        
        xhr.setRequestHeader("content-type","application/json");
		xhr.setRequestHeader("Access-Control-Allow-Methods","*");
		xhr.setRequestHeader("Access-Control-Allow-Headers", "Content-Type, Authorization");
		xhr.setRequestHeader("Access-Control-Allow-Origin", "*" );
		
		
		
        
        xhr.send(jsonString);

*/
</script>




<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


//require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/Requests-master/library/Requests.php");
			//Requests::register_autoloader();
			
			
			$data_to_send = array("APIKEYAOA" => "yNPlsmOGgZoGmH$7",
								"enviar_data_ekomi" => "enviar_data_ekomi",
								"first_name" => "sergio",
								"email" => "sergiourbina75678@gmail.com",
								"telephone" => "3202900723",
								"numeroSiniestro" => "1231231323",
								"documento" => "10230101223",
								"placa" => "BCD088");
			
			date_default_timezone_set('Etc/GMT-5');
			$data_to_send = array(
				"recipient_type" => "email",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => "James navarro",
				"last_name" => " ",
				"email" => "sergiocastillo@aoacolombia.com",
				"transaction_id" => 142534131322322132122,
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57"."3108265814",
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				//"meta_data"=> "{ \"numeroSiniestro\":\"$Sin->numero\", \"placaAsegurado\":\"$Sin->placa\", 
				//\"documentoAsegurado\":\"$Sin->asegurado_id\" , \"asd\":\"Gracias por visitarnos\" ,
				//\"first_name\":\"$Sin->asegurado_nombre\" , \"last_name\":\" \" }",
				"client_id"=> 534234,
				"screen_name"=> "Stiven Ub",
				"is_afnor"=> "false"
			);
			
			$ch = curl_init();
		    
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://srr.ekomi.com/add-recipient');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
            $request = curl_exec($ch);
            curl_close($ch);
			
			
			var_dump($request);




?>