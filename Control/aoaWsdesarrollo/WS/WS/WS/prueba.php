 <?php
 Array
(
    [Learner_Detail] => Array
        (
        [Learner_Id] => 09070567    
        [Known_As] => Aaron
        [Name] => Mr Aaron Hawley
        [Year] => 2011
        [Tutor_Name] =>  
        [Prior_Institution] => Bishop of Rochester Academy
        [Employer_Name] => 
        [Gender] => Male
        [Ethnicity] => White - English / Welsh / Scottish / Northern Irish / British
        [Nationality] => UNITED KINGDOM
        [DOB] => 07 Jul 1995
        [Age_at_end_of_Aug] => 16
    )

[Other] => Array
    (
        [NI_Number] => 
        [ULN] => 7966088560
        [University_Number] => 
        [LLDHP] => No Disability
    )
)
I have the following code to setup and register my server

$server->wsdl->addComplexType(
'Learner_Details',
'complexType',
'struct',
'all',
'',
array(
     'Learner_Id' => array('name' => 'Learner_Id', 'type' => 'xsd:string'),
     'Known_As' => array('name' => 'Known_As', 'type' => 'xsd:string'),
     'Name' => array('name' => 'Name', 'type' => 'xsd:string'),
     'Year' => array('name' => 'Year', 'type' => 'xsd:string'),
     'Tutor_Name' => array('name' => 'Tutor_Name', 'type' => 'xsd:string'),
     'Prior_Institution' => array('name' => 'Prior_Institution', 'type' => 'xsd:string'),
     'Employer_Name' => array('name' => 'Employer_Name', 'type' => 'xsd:string'),
     'Gender' => array('name' => 'Gender', 'type' => 'xsd:string'),
     'Ethnicity' => array('name' => 'Ethnicity', 'type' => 'xsd:string'),
     'Nationality' => array('name' => 'Nationality', 'type' => 'xsd:string'),
     'DOB' => array('name' => 'DOB', 'type' => 'xsd:string'),
     'Age_at_end_of_Aug' => array('name' => 'Age_at_end_of_Aug', 'type' => 'xsd:string'),
)
);

$server->wsdl->addComplexType(
'Contact_Details',
'complexType',
'struct',
'all',
'',
array(
     'Email' => array('name' => 'Email', 'type' => 'xsd:string'),
     'Mobile_Tel' => array('name' => 'Mobile_Tel', 'type' => 'xsd:string'),
     'Home_Phone' => array('name' => 'Home_Phone', 'type' => 'xsd:string'),
     'Daytime_Phone' => array('name' => 'Daytime_Phone', 'type' => 'xsd:string'),
     'Emergency_Home_Tel' => array('name' => 'Emergency_Home_Tel', 'type' => 'xsd:string'),
     'SCON_Daytime_Number' => array('name' => 'SCON_Daytime_Number', 'type' =>     'xsd:string'),
     'Emergency_Mobile' => array('name' => 'Emergency_Mobile', 'type' => 'xsd:string'),
     'EMR_Relationship_to_Learner' => array('name' => 'EMR_Relationship_to_Learner', 'type'     => 'xsd:string'),
     'Prior_Attainment_Level' => array('name' => 'Prior_Attainment_Level', 'type' => 'xsd:string'),
     'Address_1' => array('name' => 'Address_1', 'type' => 'xsd:string'),
     'Address_2' => array('name' => 'Address_2', 'type' => 'xsd:string'),
     'Address_3' => array('name' => 'Address_3', 'type' => 'xsd:string'),
     'Address_4' => array('name' => 'Address_4', 'type' => 'xsd:string'),
     'Address_5' => array('name' => 'Address_5', 'type' => 'xsd:string'),
     'Country' => array('name' => 'Country', 'type' => 'xsd:string'),
     'Postcode' => array('name' => 'Postcode', 'type' => 'xsd:string'),
     'GNAL_to_Date' => array('name' => 'GNAL_to_Date', 'type' => 'xsd:string'),
     'EMAL_EMA_Number' => array('name' => 'EMAL_EMA_Number', 'type' => 'xsd:string'),
     'EMAL_ALG_Ref' => array('name' => 'EMAL_ALG_Ref', 'type' => 'xsd:string'),
     'Left_College' => array('name' => 'Left_College', 'type' => 'xsd:string'),
     'Rest_Use' => array('name' => 'Rest_Use', 'type' => 'xsd:string'),
     'Student_Status' => array('name' => 'Student_Status', 'type' => 'xsd:string'),
     'CoD' => array('name' => 'CoD', 'type' => 'xsd:string'),
)
);


$server->wsdl->addComplexType(
'Other',
'complexType',
'struct',
'all',
'',
array(
     'NI_Number' => array('name' => 'NI_Number', 'type' => 'xsd:string'),
     'ULN' => array('name' => 'ULN', 'type' => 'xsd:string'),
     'University_Number' => array('name' => 'University_Number', 'type' => 'xsd:string'),
     'LLDHP' => array('name' => 'LLDHP', 'type' => 'xsd:string'),
)
 );

$server->wsdl->addComplexType(
'OtherArray',
'complexType',
'array',
'',
'SOAP-ENC:Array',
array(),
array(
array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Other[]')
),
'tns:Other'
);

$server->wsdl->addComplexType(
'Learner_DetailsArray',
'complexType',
'array',
'',
'SOAP-ENC:Array',
array(),
array(
array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Learner_Details[]')
),
'tns:Learner_Details'
);

 $server->wsdl->addComplexType(
'Contact_DetailsArray',
'complexType',
'array',
'',
'SOAP-ENC:Array',
array(),
array(
array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Contact_Details[]')
),
'tns:Contact_Details'
);



 $server->wsdl->addComplexType(
'totalInfo',
'complexType',
'struct',
'all',
'',
array(
     'Learner_Details' => array('name' => 'Learner_Details', 'type' =>     'tns:Learner_DetailsArray'),
     'Contact_Details' => array('name' => 'Contact Details', 'type' =>     'tns:Contact_DetailsArray'),
     'Other' => array('name' => 'Other', 'type' => 'tns:OtherArray'),

)
);

 $server->register(
'getStudentInfoById3',
array('name' => 'xsd:string'),
array('return' => 'tns:totalInfo'),
$namespace
);

?>