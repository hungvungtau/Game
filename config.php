<?php
class dbConfig {
    protected $serverName;
    protected $userName;
    protected $password;
    protected $dbName;
	protected $Site_url;
    public function __construct(){
        $this -> serverName = 'localhost';
        $this -> userName = 'mossql1';
        $this -> password = 'Vanhung789!';
        $this -> dbName = 'mossql1';
		$this -> Site_url = 'http://mos.salefunnel.top/';
    }
	    
}
?>