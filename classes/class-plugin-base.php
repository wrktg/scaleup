<?php
class ScaleUp_Plugin {

  private static $_app_server;

  private static $_templates;

  function __construct() {

    self::$_app_server  = new ScaleUp_App_Server();
    self::$_templates   = new ScaleUp_Templates();

  }

}
