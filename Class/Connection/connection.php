<?php
    error_reporting(E_ERROR);
    //Traer vendor/autoload.php
    require_once './vendor/autoload.php';
    require_once "./Class/Interface/interface.php";

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../../.env");
    $dotenv->safeLoad();

    class Connection implements IConnection {

        private $host;
        private $username;
        private $password;
        private $database;
        private $connection;

        // Function to get information and do the connection
        function __construct() {
            

            $this->host = $_ENV['HOST'];
            $this->username = $_ENV['USERNAME'];
            $this->password = $_ENV['PASSWORD'];
            $this->database = $_ENV['DATABASE'];
            
            try {
                $options = array (
                    PDO::MYSQL_ATTR_SSL_CA => "/etc/ssl/certs/ca-certificates.crt",
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                );

                $this->connection = new PDO('mysql:host='.$this->host.';dbname='.$this->database, $this->username, $this->password, $options);

            } catch (PDOException $e) {
                $this->showError($e);
            }
        }
       
        
        //Function to get data about connection
        private function dataConnection() {
            $direction = dirname(__FILE__);
            $jsondata = file_get_contents($direction . "/" . "config.json");
            return json_decode($jsondata, true);
        }

        //Function to show error, if it's the case
        public function showError($e) {
            echo "Connection failed: " . $e->getMessage();
        }

        //Change data to UTF-8
        private function changeUTF8($array) {
            array_walk_recursive($array, function(&$item, $key) {
                if(!mb_detect_encoding($item, 'utf-8', true)) {
                    $item = utf8_encode($item);
                }
            });
            return $array;
        }

        //Function to get data from database
        public function getData($query) {
            $result = $this->connection->query($query);
            $result = $result->fetchAll(PDO::FETCH_ASSOC);
            $result = $this->changeUTF8($result);
            return $result;
            $this->connection = null;
        }

        //Function to insert, update or delete data in database
        public function anyQuery($sqlstr) {
            $query = $this->connection->query($sqlstr);
            
            //Devuelve el número de filas afectadas
            $result = $query->rowCount();
            return $result;
            $this->connection = null;
        }

        //Function to insert
        public function anyQueryID($sqlstr) {
            $query = $this->connection->query($sqlstr);
            if($query->rowCount() > 0) {
                return $this->connection->lastInsertId();
            } else {
                return 0;
            }
            $this->connection = null;
        }

        protected function encrypt($string) {
            return md5($string);
        }
        

    }

?>