<?php
/**
 * WSClientBuilder
 * Copyright (C) 2010 Basilio Briceno Hernandez <bbh@tlalokes.org>
 *
 *
 * WSClientBuilder is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, version 3 of the License.
 *
 * WSClientBuilder is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with WSClientBuilder, if not, see the LGPL web site.
 * <http://www.gnu.org/licenses/lgpl.html>
 */

/**
 * Generates a class with all methods needed to reproduce a SOAP based service
 *
 * @author Basilio Brice&ntilde;o Hern&aacute;ndez <bbh@tlalokes.org>
 * @copyright Copyright &copy; 2010 Basilio Brice&ntilde;o Hern&aacute;ndez
 * @license http://www.gnu.org/licenses/lgpl.html GNU LGPL
 */
class WSClientBuilder {

  public $WSDL;
  public $client_name;
  public $retries;
  private $service;

  /**
   * Constructs the WSClientBuilder
   *
   * @param string $WSDL
   * @param string $client_name
   * @param integer $entries
   */
  public function __construct( $WSDL, $client_name, $retries = 5 )
  {
    $this->WSDL = $WSDL;
    $this->client_name = $client_name;
    $this->retries = $retries;
    try {
      $this->service = new SoapClient( $WSDL );
    } catch ( Exception $e ) {
      return $e->getMessage();
    }
  }

  /**
   * Returns the generated client class
   *
   * @return string
   */
  public function getClass ()
  {
    $WSDL =& $this->WSDL;
    $class_name =& $this->client_name;
    $retries =& $this->retries;
    $methods = $this->getMethods();

    require_once 'inc/webservice/WSClientBuilderTemplate.php';
    return $template;
  }

  /**
   * Returns the generated methods client class
   *
   * @return string
   */
  public function getMethods ()
  {
    $search = '/([a-zA-Z]*)\s([a-zA-Z]*)\(([a-zA-Z]*)\s(\$[a-zA-Z]*)\)/';

    $replace = '  /**'."\n".
               '   * @param $3 $4'."\n".
               '   * @return $1'."\n".
               '   */'."\n".
               '  public function $2 ( $4 )'."\n".
               '  {'."\n".
               '    return $this->service->$2( $4 );'."\n".
               '  }'."\n\n";

    $methods = '';

    foreach ( $this->service->__getFunctions() as $method ) {
      $methods .= preg_replace( $search, $replace, $method );
    }

    return $methods;
  }
}
?>