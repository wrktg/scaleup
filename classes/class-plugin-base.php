<?php
class ScaleUp_Plugin {

  private static $_initialized = false;

  private static $_app_server;

  private static $_templates;

  private static $_schemas;

  function __construct() {

    self::$_app_server  = new ScaleUp_App_Server();
    self::$_templates   = new ScaleUp_Templates();
    self::$_schemas     = new ScaleUp_Schemas();

    if ( !self::$_initialized ) {
      $this->initialize();
      self::$_initialized = true;
    }

  }

  function initialize() {

  }

}
