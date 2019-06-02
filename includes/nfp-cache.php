<?php

class NFP_Cache
{

  /**
   * get_transient
   * permanent caching
   *
   * @param $key :String
   * @param $setter :Function
   *
   * @return mixed
   */
  public static function get_transient($key, $setter, $expiration = 0)
  {
    if (!$data = get_transient($key)) {
      $data = $setter();
      if ($data && !empty($data)) {
        self::set_transient($key, $data, $expiration);
      }
    }
    return $data;
  }

  public static function set_transient($key, $data, $expiration = 0)
  {
    set_transient($key, $data, $expiration);
  }

  /**
   * get_cached
   * This is once per page load.
   *
   * @param $key : String
   * @param $setter : Function
   *
   * @return mixed
   */
  public static function get_cached($key, $setter)
  {
    if (!$data = wp_cache_get($key)) {
      $data = $setter();
      wp_cache_add($key, $data);
    }

    return $data;
  }

  public static function remove_cached($key)
  {
    wp_cache_delete($key);
  }
}
