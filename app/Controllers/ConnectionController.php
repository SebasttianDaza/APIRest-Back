<?php
namespace Ships\Controllers;

error_reporting(E_ERROR | E_PARSE);

use Ships\Controllers;
use PDO;
use PDOException;

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
    $this->username = $_ENV["USERNAME_DB"];
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
  public function getError(PDOException $e): array
  {
    response()->httpCode(500);
    return response()->json([
      "StatusMsg" => "Internal Server Error",
      "StatusCode" => 500,
      "detail" => $e->getMessage(),
    ]);
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
  public function getItems(string $table, int $start, int $count): array
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
  public function getItemById(string $table, int $id, $param = "id"): array
  {
    $query = "SELECT * FROM $table WHERE $param = $id";
    $response = $this->getData($query);

    return $response;
  }

  /**
   *  Set item in database
   * @param string $table
   * @param array $data
   * @return int
   */
  public function setItem(string $table, array $data): int
  {
    $keys = implode(", ", array_keys($data));
    $values = implode("', '", array_values($data));

    $query = "INSERT INTO $table ({$keys}) VALUES ('{$values}')";

    return $this->anyQueryID($query);
  }

  /**
   *
   */
  public function updateItem(
    string $table,
    array $data,
    int $id,
    string $param = "id"
  ): int {
    $merge = implode(
      ", ",
      array_map(
        function ($k, $v) {
          return sprintf("%s='%s'", $k, $v);
        },
        array_keys($data),
        array_values($data)
      )
    );

    $query = "UPDATE $table SET {$merge} WHERE $param = $id";
    $response = $this->anyQuery($query);

    return $response >= 1 ? $response : false;
  }

  /**
   *
   */
  public function removeItem(string $table, int $id, string $param = "id"): int
  {
    $query = "DELETE FROM $table WHERE $param = $id";
    $response = $this->anyQuery($query);

    return $response >= 1 ? $response : false;
  }

  /**
   *  Get data from users table
   * @param string $username
   * @return array
   * @return bool
   */
  public function getDataUsers(string $username, string $select): bool|array
  {
    $query = "SELECT $select, password, status FROM users WHERE username = '$username'";
    $response = $this->getData($query);

    return isset($response[0]["password"]) ? $response : false;
  }

  /**
   * Set user in database
   * @param string $username
   * @param string $password
   * @param string $email
   * @return int
   */
  public function setUser(
    string $username,
    string $password,
    string $email
  ): int {
    $query = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
    $response = $this->anyQueryID($query);

    return $response;
  }

  /**
   * Set token in database
   * @param int $userID
   * @param string $token
   * @param int $state
   * @return int
   */
  public function setToken(int $userID, string $token, int $state): int
  {
    $date = date("Y-m-d H:i:s");

    $query = "INSERT INTO users_tokens (userID, token, status, created_at, updated_at) VALUES ($userID, '$token', $state, '$date', '$date')";
    $response = $this->anyQuery($query);

    return $response;
  }

  /**
   * Get token from database
   * @param string $token
   * @return bool|array
   */
  public function getToken(string $token): bool|array
  {
    $query = "SELECT userID, token, status FROM users_tokens WHERE token = '$token' AND status = '1'";

    $response = $this->getData($query);

    return $response ? $response : false;
  }

  /**
   * Update token in database
   * @param int $userID
   * @return bool|array
   */
  public function getUpdateToken(int $userID): bool|array
  {
    $date = date("Y-m-d H:i:s");
    $query = "UPDATE users_tokens SET updated_at = '$date' WHERE userID = $userID";

    $response = $this->anyQuery($query);

    return $response >= 1 ? $response : false;
  }
}

?>
