<?php
class ScaleUp_View {

  private static $_this;

  function __construct() {

    if ( isset( self::$_this ) )
      wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.',
        'scaleup' ), get_class( $this ) ) );

    self::$_this = $this;

  }

}