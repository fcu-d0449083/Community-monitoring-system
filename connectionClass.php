<?php            //連線到mysql//
define('MYSQL_SERVER', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', 'a92015a92015');
define('MYSQL_DB', 'webcam');
class connectionClass extends mysqli{
//    public $host="localhost",$dbname="webcam",$dbpass="a92015a92015",$dbuser="root";
    public $con;
    
    public function __construct() {
        if($this->connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, MYSQL_DB)){}
        else
        {
            return "<h1>Error while connecting database</h1>";
        }
    }
}
