<?php
class configDatabase {
    protected $serverName;
    protected $userName;
    protected $passCode;
    protected $dbName;

    function Dbconfig() {
        $this -> serverName = 'localhost';
        $this -> userName = 'root';
        $this -> passCode = '';
        $this -> dbName = 'dev104_apidb';
    }
}
?>