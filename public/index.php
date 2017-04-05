<?php
require_once '../vendor/autoload.php';

// TODO: This needs moving out of the main index.php
// Required for Debug bar to gather DB queries
class DBGDB extends DB\SQL
{
	/**
	*	Instantiate class
	*	@param $dsn string
	*	@param $user string
	*	@param $pw string
	*	@param $options array
	**/
	function __construct($dsn,$user=NULL,$pw=NULL,array $options=NULL) {
		$fw=\Base::instance();
		$this->uuid=$fw->hash($this->dsn=$dsn);
		if (preg_match('/^.+?(?:dbname|database)=(.+?)(?=;|$)/is',$dsn,$parts))
			$this->dbname=$parts[1];
		if (!$options)
			$options=[];
		if (isset($parts[0]) && strstr($parts[0],':',TRUE)=='mysql')
			$options+=[\PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES '.
				strtolower(str_replace('-','',$fw->get('ENCODING'))).';'];
		$this->pdo=new DebugBar\DataCollector\PDO\TraceablePDO(new \PDO($dsn,$user,$pw,$options));
		$this->engine=$this->pdo->getattribute(\PDO::ATTR_DRIVER_NAME);
	}
}

// Required for pagebrowser tag rendering
\Template::instance()->extend('pagebrowser','\Pagination::renderTag');

////////////////
// Script Begins
////////////////
$f3 = \Base::instance();

$f3->config('../app/configs/config.ini');
$f3->config('../app/configs/routes.ini');

$f3->set('DB', new DBGDB('sqlite:'.$f3->{DBPATH}));

$f3->run();
