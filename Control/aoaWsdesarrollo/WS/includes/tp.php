<?php
  class SecurityHeader
{
    function getGuid()
    {
         $token = md5(uniqid());
        // better, difficult to guess
        $better_token = md5(uniqid(rand(), true));
        return substr($better_token, 0, 10).'-'.substr($better_token, 9, 4)
            .'-'.substr($better_token, 13, 4).'-'.substr($better_token, 17, 4).'-'.substr($better_token, 21, 10);
    }
    
    function AppendTpPolicy($client, $operationName, $userName, $userPassword)
    {
        $client->namespaces['wsa'] = 'http://schemas.xmlsoap.org/ws/2004/08/addressing';
        $client->namespaces['wsse'] = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $client->namespaces['wsu'] = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

        $operation = $client->getOperationData($operationName, 'soap');
        $soapAction = $operation["soapAction"];
        $endPoint = $operation["endpoint"];

        $securityHeader = new SecurityHeader;

        $msgID = 'urn:uuid:'.$securityHeader->getGuid();


        $headers = array(   'wsa:Action'=>$soapAction,
                            'wsa:MessageID'=>$msgID,
                            'wsa:ReplyTo'=>array('wsa:Address'=>'http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous'),
                            'wsa:To'=>$endPoint);

        $xml = '';

        foreach ($headers as $k => $v) {
        $xml.= $client->serialize_val($v, $k, false, false, false, false, 'literal');
        }

        $xml.= "<wsse:Security SOAP-ENV:mustUnderstand=\"1\">";

        $xml.="<wsse:UsernameToken xmlns:wsu=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd\" wsu:Id=\"SecurityToken-"
                .$securityHeader->getGuid()."\">";
        $xml.=$client->serialize_val($userName, "wsse:Username", false, false, false, false, 'literal');
        $xml.=$client->serialize_val($userPassword, "wsse:Password", false, false, false, 
            array('Type'=>'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText'), 'literal');
            
        $xml.="</wsse:UsernameToken>";

        $xml.= "</wsse:Security>";

        $client->setHeaders($xml);
    }
}
?>
