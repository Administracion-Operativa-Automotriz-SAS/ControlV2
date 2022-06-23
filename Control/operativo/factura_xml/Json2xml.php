<?php

class Json2XML {	
	
	
   
   public static function  generate_xml($json_data)
   {
		$generated_xml = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
	   $generated_xml = "";
	   foreach($json_data as $key => $value)
	   {
		   
		   if(is_object($value))
		   {
			   $extra_tags = array();
			   
				$tag = "";
				
				$tag .= "<".$key;
				
			   $next = self::find_next($value);
			   
			   //print_r($next["properties"]);
			   
			    if(count($next["properties"])>0)
			    {
				    $properties_inside_tag = "";	
				   foreach($next["properties"] as $next_property)				   
				   {
						//print_r($next_property);
					   $get_key_value = self::get_key_value($next_property);
						//print_r($get_key_value);
					   
					   $next_key = $get_key_value["key"];
					   $next_value = $get_key_value["value"];					   
					   
					   //if(strpos($next_key,"xmlns:") !== false or strpos($next_key,"-xsi:") !== false)
						if(strpos($next_key,"-") !== false)   
					   {					    
						    //eliminar el guion del inicio						    				   
							$next_key = substr($next_key, 1);
							
							
							
							$properties_inside_tag .= ' '.$next_key.'="'.$next_value.'" ';	
							//$tag .= " ".$next_key." ";
							
					   }
					   else
					   {
						   array_push($extra_tags,$next_property);
						  //echo "no match ".$next_key;
						  // echo "<br>";
					   }
				   }
				   $tag .= "\n".$properties_inside_tag;
			    }		
				
				
				$tag .= ">";
				
			    //echo $tag;
				$generated_xml .= $tag;	
			   
			   
			    $generated_xml .= self::generate_xml($value);
			   
			    $endtag = "</".$key.">";
			   
			   //echo $endtag;
				$generated_xml .= $endtag; 
			   
		   }
		   else{
			
			   if(strpos($key,"-") === false and strpos($key,"#") === false)
			   {	
					if(!is_array($value))
				    {
						//echo "<".$key.">".$value."</".$key.">";
						$generated_xml .= "<".$key.">".$value."</".$key.">";
				    }
					else{						
						
						foreach($value as $sub_key => $sub_value)
						{
							//echo "<".$key.">";
							$generated_xml .= "<".$key.">"; 
							//self::generate_xml($sub_value);
							$generated_xml .= self::generate_xml($sub_value);
							//echo "</".$key.">";
							$generated_xml .= "</".$key.">";
						}						
						
				    }				   
			   }
			   else{
				   if(strpos($key,"#") !== false)
				   {
						//echo $value;
						$generated_xml .= $value;
				   }	
			   }
		   }
		   
	   }
	   
	   return $generated_xml;
	   
   }
   
   private static function get_key_value($data)
   {
	   foreach($data as $key=>$value)
	   {
		   return array("key"=>$key,"value"=>$value);
	   }
   }

	
	private static function find_next($complex_object)
	{		
		//print_r($complex_object);
		
		
		//echo "<br>";
		
		//$properties = array();		
		$array_objects = array();
		$properties = array();
		
		foreach($complex_object as $key => $value)
		{			   	
			 if(is_object($value) or is_array($value))
			 {
				//array_push($array_objects,array("key"=>$key,"value"=>$value));
				//echo "objeto con llave ".$key;
				//echo "<br>";
				array_push($array_objects,$value);
			 }
			 else
			 {
				 //echo "llave que no es objeto si no valor ".$key;
				 //echo "<br>";
				 array_push($properties,array($key=>$value));
			 }			
		}		
		
		return array("properties"=>$properties,"array_objects"=>$array_objects);		
	}
	
	
   
   
   
}
?>