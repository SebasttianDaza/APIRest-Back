<?php
namespace Ships\Controllers;

class UtilsController
{
  /**
   * @param array $array
   * @param array $array_keys
   * @return bool
   * Check if array keys are empty and their value
   * @example
   *  $array = [
   *    "key1" => "value1",
   *  "key2" => "value2",
   *  ];
   *  $array_keys = ["key1", "key2"]
   *  $result = true
   */
  public function getInArray(array $array, array $array_keys): bool
  {
    $result = false;

    foreach ($array_keys as $key) {
      if (!array_key_exists($key, $array) || empty($array[$key])) return true;
    }

    return $result;
  }

  /**
   * Get all values from specific key in a multidimensional array
   *
   * @param $key string
   * @param $arr array
   * @return null|string|array
   */
  public function array_value_recursive($key, array $arr)
  {
    $val = [];
    array_walk_recursive($arr, function ($v, $k) use ($key, &$val) {
      if ($k == $key) {
        array_push($val, $v);
      }
    });
    return count($val) > 1 ? $val : array_pop($val);
  }
}

?>
