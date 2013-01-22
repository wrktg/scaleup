<?php
class ScaleUp_Plugin {

  private static $_app_server;

  function __construct() {

    self::$_app_server = new ScaleUp_App_Server();

  }

}
