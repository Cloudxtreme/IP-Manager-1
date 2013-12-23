<?php

class ipm {

	const ver = "5.0";
	
	public function getVer() {
	
		echo self::ver;
		
	}
	
	public function getPageLevel($mod = "",$user) {
	
		require('Connections/subman.php');
	
		mysql_select_db($database_subman, $subman);
		$query_getLevel_level = "SELECT usrgrouppermissions.level FROM usrgrouppermissions LEFT JOIN usrgroup ON usrgroup.id = usrgrouppermissions.usrgroup LEFT JOIN module ON module.id = usrgrouppermissions.module LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND module.id = '".$mod."';";
		$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
		$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
		$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
		return $row_getLevel_level['level'];
		
		mysql_free_result($getLevel_level);

	}
	
}

class DBconfig extends ipm {
    
    protected $serverName;
    protected $userName;
    protected $passCode;
    protected $dbName;

    function DBconfig() {
        $this -> serverName = 'localhost';
        $this -> userName = 'root';
        $this -> passCode = '';
        $this -> dbName = 'ipmanager';
    }
    
}

class Mysql extends DBconfig    {

	public $connectionString;
	public $dataSet;
	private $sqlQuery;

    protected $databaseName;
    protected $hostName;
    protected $userName;
    protected $passCode;

	function Mysql()    {
    
    	$this -> connectionString = NULL;
    	$this -> sqlQuery = NULL;
    	$this -> dataSet = NULL;

        $dbPara = new DBconfig();
        $this -> databaseName = $dbPara -> dbName;
        $this -> hostName = $dbPara -> serverName;
        $this -> userName = $dbPara -> userName;
        $this -> passCode = $dbPara ->passCode;
        $dbPara = NULL;
            
    }

	function dbConnect()    {
    	
    	$this -> connectionString = mysql_connect($this -> serverName,$this -> userName,$this -> passCode);
    	mysql_select_db($this -> databaseName,$this -> connectionString);
    	return $this -> connectionString;
	
	}

	function dbDisconnect() {
    
    	$this -> connectionString = NULL;
    	$this -> sqlQuery = NULL;
	    $this -> dataSet = NULL;
	    $this -> databaseName = NULL;
    	$this -> hostName = NULL;
	    $this -> userName = NULL;
    	$this -> passCode = NULL;

	}

	function selectAll($tableName)  {
    
    	$this -> sqlQuery = 'SELECT * FROM '.$this -> databaseName.'.'.$tableName;
    	$this -> dataSet = mysql_query($this -> sqlQuery,$this -> connectionString);
        
        return $this -> dataSet;

	}

	function selectWhere($tableName,$rowName,$operator,$value,$valueType)   {
    	$this -> sqlQuery = 'SELECT * FROM '.$tableName.' WHERE '.$rowName.' '.$operator.' ';
    	if($valueType == 'int') {
        	$this -> sqlQuery .= $value;
    	}
    	else if($valueType == 'char')   {
        	$this -> sqlQuery .= "'".$value."'";
    	}
    	$this -> dataSet = mysql_query($this -> sqlQuery,$this -> connectionString);
    	$this -> sqlQuery = NULL;
    
    	return $this -> dataSet;
    	#return $this -> sqlQuery;

	}

	function insertInto($tableName,$values) {
    	$i = NULL;

    	$this -> sqlQuery = 'INSERT INTO '.$tableName.' VALUES (';
    	$i = 0;
    	while($values[$i]["val"] != NULL && $values[$i]["type"] != NULL)    {
        	if($values[$i]["type"] == "char")   {
            	$this -> sqlQuery .= "'";
            	$this -> sqlQuery .= $values[$i]["val"];
            	$this -> sqlQuery .= "'";
        	}
        	else if($values[$i]["type"] == 'int')   {
            	$this -> sqlQuery .= $values[$i]["val"];
        	}
        	$i++;
        	if($values[$i]["val"] != NULL)  {
            	$this -> sqlQuery .= ',';
        	}
    	}
    	$this -> sqlQuery .= ')';
            #echo $this -> sqlQuery;
    	mysql_query($this -> sqlQuery,$this ->connectionString);
            
        return $this -> sqlQuery;
    	#$this -> sqlQuery = NULL;

	}

}
class device extends ipm {

	protected $deviceName;
	protected $deviceDescr;
	protected $deviceType;
	protected $deviceGroup;
	protected $deviceMgmt;
	protected $deviceSNMP;

}

class card extends device {

	protected $cardType;
	protected $cardTimeslots;
	protected $cardTimeslotBandwidth;
	protected $cardRack;
	protected $cardSlot;
	protected $cardModule;
	protected $cardStartPort;
	protected $cardEndPort;

}

class port extends card {

	protected $port;
	protected $portCustomer;
	protected $portUsage;
	protected $portAddress;
	protected $portVLAN;
	protected $portVPN;
	protected $portVRFVC;
	
}

class subint extends card {

	protected $subint;
	protected $subintCustomer;
	protected $subintUsage;
	protected $subintAddress;
	protected $subintVLAN;
	protected $subintVPN;
	protected $subintVRFVC;
	
}

?>