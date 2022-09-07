<?php
    namespace Ships\Controllers;
    error_reporting(E_ERROR | E_PARSE);
    

    require_once(__DIR__ . '../../../vendor/autoload.php');
    use Dotenv;
    use PDO;
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../../.env");
    $dotenv->safeLoad();

    class ConnectionController {

        private $host;
        private $username;
        private $password;
        private $database;
        private $port;
        private $connection;

        /***
         * @Constructor
         * @Return void
         */
        function __construct() {

            $this->host = $_ENV['HOST'];
            $this->username = $_ENV['USERNAME'];
            $this->password = $_ENV['PASSWORD'];
            $this->database = $_ENV['DATABASE'];
            $this->port = $_ENV['PORT'];
            
            try {
                $options = array (
                    PDO::MYSQL_ATTR_SSL_CA => $_ENV['MYSQL_ATTR_SSL_CA'],
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                );

                $this->connection = new PDO("mysql:host=$this->host;dbname=$this->database;port=$this->port", $this->username, $this->password, $options);

            } catch (PDOException $e) {
                $this->showError($e);
            }
        }
       

        /**
         * Function to show error
         */
        public function showError($e) {
            echo "Connection failed: " . $e->getMessage();
        }

        /**
         * Function change UTF
         */
        private function changeUTF8($array) {
            array_walk_recursive($array, function(&$item, $key) {
                if(!mb_detect_encoding($item, 'utf-8', true)) {
                    $item = utf8_encode($item);
                }
            });
            return $array;
        }

        /**
         * Function to get data from database
         * @Return array
         */
        public function getData($query) {
            $result = $this->connection->query($query);
            $result = $result->fetchAll(PDO::FETCH_ASSOC);
            $result = $this->changeUTF8($result);
            return $result;
            $this->connection = null;
        }

        /**
         * @Return boolean
         * @Param string
         * Function to insert, update or delete data in database
         */
        public function anyQuery($sqlstr) {
            $query = $this->connection->query($sqlstr);
            
            //Devuelve el número de filas afectadas
            $result = $query->rowCount();
            return $result;
            $this->connection = null;
        }

        /**
         * @Return number
         * @Param string
         * Function to insert data in database
         */
        public function anyQueryID($sqlstr) {
            $query = $this->connection->query($sqlstr);
            if($query->rowCount() > 0) {
                return $this->connection->lastInsertId();
            } else {
                return 0;
            }
            $this->connection = null;
        }

        /**
         * @Return algorithm
         * @Param string
         * Function to encrypt data
         */
        protected function encrypt($string) {
            return md5($string);
        }
        

    }

?>