<?php
namespace Ships\Controllers;

error_reporting(E_ERROR | E_PARSE);

require_once(__DIR__ . "../../../vendor/autoload.php");
use Ships\Controllers;
use Dotenv;
use PDO;
use PDOException;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../../.env");
$dotenv->safeLoad();

class ConnectionController extends UtilsController
{
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
  function __construct()
  {
    $this->host = $_ENV["HOST"];
    $this->username = $_ENV["USERNAME"];
    $this->password = $_ENV["PASSWORD"];
    $this->database = $_ENV["DATABASE"];
    $this->port = $_ENV["PORT"];

    try {
      $options = [
        PDO::MYSQL_ATTR_SSL_CA => $_ENV["MYSQL_ATTR_SSL_CA"],
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
      ];

      // Create connection
      $this->connection = new PDO(
        "mysql:host=$this->host;dbname=$this->database;",
        $this->username,
        $this->password,
        $options
      );
    } catch (PDOException $e) {
      $this->getError($e);
    }
  }

  /**
   * @param PDOException $e
   * @return void
   * Show error function
   */
  public function getError(PDOException $e): void
  {
    echo "Connection failed: " . $e->getMessage();
  }

  /**
   * Function change UTF
   * @param array $array
   * @return array
   */
  private function changeUTF8(array $array): array
  {
    array_walk_recursive($array, function (&$item, $key) {
      if (!mb_detect_encoding($item, "utf-8", true)) {
        $item = utf8_encode($item);
      }
    });

    return $array;
  }

  /**
   * Get data from database
   * @param string $query
   * @return array
   */
  public function getData(string $query): array
  {
    $result = $this->connection->query($query);
    $result = $result->fetchAll(PDO::FETCH_ASSOC);
    $result = $this->changeUTF8($result);
    // Return result
    return $result;
    $this->connection = null;
  }

  /**
   * Insert, update or delete data in database
   * @param string $sqlstr
   * @return int
   */
  public function anyQuery(string $sqlstr): int
  {
    $query = $this->connection->query($sqlstr);

    // Return number of affected rows
    $result = $query->rowCount();
    return $result;
    $this->connection = null;
  }

  /**
   * Insert data in database and return last insert id
   * @param string $sqlstr
   * @return int
   */
  public function anyQueryID(string $sqlstr): int
  {
    // Execute query
    $query = $this->connection->query($sqlstr);
    if ($query->rowCount() > 0) {
      return $this->connection->lastInsertId();
    } else {
      return 0;
    }
    // Turn off connection
    $this->connection = null;
  }

  /**
   * Encrypt data with md5
   * @param string $string
   * @return string
   */
  protected function encrypt(string $string): string
  {
    return md5($string);
  }

  /**
   * Get all items from table
   * @param string $table
   * @return array
   */
  public function getItems(string $table,  int $start, int $count): array
  {
    $query = "SELECT * FROM $table LIMIT $start, $count";
    $response = $this->getData($query);

    return $response;
  }

  /**
   * Get item by id
   * @param string $table
   * @param int $id
   * @return array
   */
  public function getItemById(string $table, int $id): array
  {
    $query = "SELECT * FROM $table WHERE id = $id";
    $response = $this->getData($query);

    return $response;
  }
  
}

?>
