<?php
namespace Ships\Controllers;

class UtilsController
{
  public function getEnv($key)
  {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../../../.env");
    $dotenv->safeLoad();
    return $_ENV[$key];
  }

  public function getInArray(array $array, array $array_keys): bool
  {
    $result = false;

    foreach ($array_keys as $key) {
      if (!array_key_exists($key, $array) || empty($array[$key])) return true;
    }

    return $result;
  }
}

?>
