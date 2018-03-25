<?php

namespace App\Service;

use Pimple\Container;

/**
 * Class AppContainer
 * @package App
 * @author  rumur
 */
class AppContainer {

  /** @var AppContainer */
  protected static $_instance;

  /** @var \Pimple\Container */
  protected $provider;

  /**
   * ApplicationContainer constructor.
   */
  private function __construct()
  {
    $this->provider = new Container();
  }

  /**
   * Singleton.
   *
   * @return AppContainer
   *
   * @author rumur
   */
  public static function getInstance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  /**
   * Getter for provider.
   *
   * @return Container
   *
   * @author rumur
   */
  public function getProvider()
  {
    return $this->provider;
  }
}
