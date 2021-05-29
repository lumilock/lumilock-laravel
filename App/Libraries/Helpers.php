<?php

namespace App\Libraries;

class Helpers
{
  /**
   * @return \Laravel\Lumen\Routing\UrlGenerator
   */
  public static function urlGenerator()
  {
    return new \Laravel\Lumen\Routing\UrlGenerator(app());
  }

  /**
   * @param $path
   * @param bool $secured
   *
   * @return string
   */
  public static function asset($path, $secured = false)
  {
    return Helpers::urlGenerator()->asset($path, $secured);
  }
}
