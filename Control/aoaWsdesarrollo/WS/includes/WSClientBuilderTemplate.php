<?php
$template = <<<EOT
<?php
class {$class_name} {

  protected \$service;

  /**
   * Constructs the service
   */
  public function __construct ()
  {
    \$wsdl = "{$WSDL}";
    try {
      \$this->service = new SoapClient( \$wsdl );
    } catch ( Exception \$e ) {
      return \$e->getMessage();
    }
  }

  /**
   * Provides managment of errors calling the service's methods
   */
  public function __call ( \$name, \$arguments )
  {
    \$result = false;
    \$max_retries = {$retries};
    \$retry_count = 0;

    while( !\$result && \$retry_count < \$max_retries ) {
      try {
        \$result = parent::__call( \$name, \$arguments );
      } catch( SoapFault \$fault ) {
        if( \$fault->faultstring != 'Could not connect to host' ) {
          throw \$fault;
        }
      }
      sleep(1);
      \$retry_count++;
    }
    if ( \$retry_count == \$max_retries ) {
      throw new SoapFault( 'Could not connect to host after {$retries} attempts' );
    }
    return \$result;
  }

{$methods}}
?>

EOT;
?>
